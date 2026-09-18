<?php

use App\Http\Controllers\CmsResourceController;
use App\Models\MediaAsset;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->withoutVite();
    Storage::fake('local');
    Storage::fake('public');
});

function resourceData(array $overrides = []): array
{
    return array_merge(['title' => 'Guide communautaire', 'description' => 'Un document de référence.', 'category' => 'Formation', 'publication_state' => 'published', 'distribution_allowed' => 0], $overrides);
}

function resourcePdf(): UploadedFile
{
    return UploadedFile::fake()->createWithContent('guide.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF");
}

function resourceRecord(array $overrides = []): Publication
{
    $path = 'resources/'.Illuminate\Support\Str::uuid().'.pdf';
    Storage::disk('local')->put($path, "%PDF-1.4\n%%EOF");
    $file = MediaAsset::create(['key' => $path, 'name' => 'guide.pdf', 'kind' => 'document', 'disk' => 'local', 'path' => $path, 'visibility' => 'private', 'mime_type' => 'application/pdf', 'publication_allowed' => true, 'is_demo' => false]);

    return Publication::create(resourceData(array_merge(['cms_key' => $path, 'slug' => Illuminate\Support\Str::uuid(), 'file_media_id' => $file->id, 'availability' => 'available'], $overrides)));
}

test('resources can be read while download is checked by the server', function () {
    $resource = resourceRecord();
    $this->get(route('resources'))->assertOk()->assertSee($resource->title)->assertDontSee('Télécharger le PDF');
    $this->get(route('resources.show', $resource->slug))->assertOk()->assertSee('data-pdf-reader', false)->assertDontSee('Télécharger le PDF');
    $this->get(route('resources.read', $resource->slug))->assertOk()->assertHeader('Content-Type', 'application/pdf')->assertHeader('Content-Disposition', 'inline; filename="document.pdf"');
    $this->get(route('resources.download', $resource->slug))->assertForbidden();
    $resource->update(['distribution_allowed' => true]);
    $this->get(route('resources.show', $resource->slug))->assertSee('Télécharger le PDF');
    $this->get(route('resources.download', $resource->slug))->assertDownload('guide-communautaire.pdf');
    $resource->update(['distribution_allowed' => false]);
    $this->get(route('resources.download', $resource->slug))->assertForbidden();
    expect($resource->file->publicUrl())->toBeNull();
});

test('draft deleted demo missing and legacy resources cannot be read or downloaded', function () {
    foreach ([['publication_state' => 'draft'], ['is_demo' => true], ['cms_key' => null], ['availability' => 'pending']] as $changes) {
        $resource = resourceRecord($changes);
        foreach (['show', 'read', 'download'] as $action) {
            $this->get(route('resources.'.$action, $resource->slug))->assertNotFound();
        }
    }
    $resource = resourceRecord();
    Storage::disk('local')->delete($resource->file->path);
    $this->get(route('resources.read', $resource->slug))->assertNotFound();
    $this->get(route('resources.download', $resource->slug))->assertNotFound();
    $resource = resourceRecord();
    $resource->delete();
    foreach (['show', 'read', 'download'] as $action) {
        $this->get(route('resources.'.$action, $resource->slug))->assertNotFound();
    }
});

test('administrators create replace and delete private pdf resources with stale protection', function () {
    $this->get(route('admin.cms.resources'))->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->post(route('admin.cms.resources.store'), resourceData())->assertForbidden();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.cms.resources.create'))->assertOk();
    $this->post(route('admin.cms.resources.store'), resourceData(['document' => resourcePdf(), 'cover' => UploadedFile::fake()->image('cover.jpg')]))->assertSessionHasNoErrors()->assertRedirect();
    $resource = Publication::firstOrFail();
    expect($resource->file->disk)->toBe('local');
    Storage::disk('local')->assertExists($resource->file->path);
    Storage::disk('public')->assertMissing($resource->file->path);
    $this->get(route('admin.cms.resources'))->assertOk()->assertSee($resource->title);
    $this->get(route('admin.cms.resources.edit', $resource))->assertOk()->assertSee('Autoriser le téléchargement');
    $revision = CmsResourceController::revision($resource);
    $oldFile = $resource->file_media_id;
    $this->put(route('admin.cms.resources.update', $resource), resourceData(['revision' => $revision, 'document' => resourcePdf(), 'remove_cover' => 1, 'distribution_allowed' => 1]))->assertSessionHasNoErrors();
    $resource->refresh();
    expect($resource->file_media_id)->not->toBe($oldFile);
    expect($resource->cover_media_id)->toBeNull();
    $this->put(route('admin.cms.resources.update', $resource), resourceData(['revision' => $revision]))->assertSessionHasErrors('revision');
    $this->delete(route('admin.cms.resources.destroy', $resource), ['revision' => $revision])->assertSessionHasErrors('revision');
    $this->delete(route('admin.cms.resources.destroy', $resource), ['revision' => CmsResourceController::revision($resource)])->assertSessionHasNoErrors()->assertRedirect(route('admin.cms.resources'));
    $this->assertSoftDeleted('publications', ['id' => $resource->id]);
    $this->get(route('resources.read', $resource->slug))->assertNotFound();
    $this->get(route('resources.download', $resource->slug))->assertNotFound();
});

test('invalid uploads and publishing without pdf are rejected but drafts can be prepared', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->post(route('admin.cms.resources.store'), resourceData())->assertSessionHasErrors('document');
    $this->post(route('admin.cms.resources.store'), resourceData(['document' => UploadedFile::fake()->image('wrong.jpg')]))->assertSessionHasErrors('document');
    $this->post(route('admin.cms.resources.store'), resourceData(['document' => UploadedFile::fake()->create('large.pdf', 20481, 'application/pdf')]))->assertSessionHasErrors('document');
    $this->post(route('admin.cms.resources.store'), resourceData(['publication_state' => 'draft']))->assertSessionHasNoErrors();
    expect(Publication::count())->toBe(1);
    $legacy = resourceRecord(['cms_key' => null]);
    $this->get(route('admin.cms.resources.edit', $legacy))->assertNotFound();
    $this->put(route('admin.cms.resources.update', $legacy), resourceData())->assertNotFound();
    $this->delete(route('admin.cms.resources.destroy', $legacy))->assertNotFound();
});

test('resource catalogue is searchable paginated and reachable through old links', function () {
    foreach (range(1, 8) as $n) {
        resourceRecord(['title' => 'Document numéro '.$n, 'category' => $n === 1 ? 'Étude' : 'Rapport']);
    }
    $this->get(route('resources'))->assertOk()->assertSee('Document numéro 8')->assertDontSee('Document numéro 1')->assertSee('Page 1 sur 2');
    $this->get(route('resources', ['q' => 'numéro 1']))->assertSee('Document numéro 1')->assertDontSee('Document numéro 2');
    $this->get(route('resources', ['category' => 'Étude']))->assertSee('Document numéro 1')->assertDontSee('Document numéro 2');
    $this->get(route('resources', ['q' => 'introuvable']))->assertSee('Aucune ressource ne correspond');
    $this->get('/ressources.html')->assertRedirect('/ressources');
    $this->get('/adpdh/ressources.html')->assertRedirect('/ressources');
    $this->get('/publications')->assertRedirect(route('resources'));
    expect(adpdh_url('ressources.html'))->toBe(route('resources'));
});
