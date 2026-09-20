<?php

use App\Http\Controllers\CmsActivityController;
use App\Http\Controllers\CmsNewsController;
use App\Models\MediaAsset;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use App\Services\MediaLibrary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;

beforeEach(function () {
    Storage::fake('public');
    $this->withoutVite();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
});

function libraryImage(string $name = 'terrain.jpg'): MediaAsset
{
    return app(MediaLibrary::class)->upload(UploadedFile::fake()->image($name, 80, 60), auth()->id());
}

test('media endpoints are administrator only', function () {
    $asset = libraryImage();
    $this->actingAs(User::factory()->create());
    $this->get('/admin/cms/media')->assertForbidden();
    $this->getJson('/admin/cms/media')->assertForbidden();
    $this->postJson('/admin/cms/media', ['image' => UploadedFile::fake()->image('x.jpg')])->assertForbidden();
    $this->put('/admin/cms/media/'.$asset->id, ['name' => 'Changed'])->assertForbidden();
    $this->delete('/admin/cms/media/'.$asset->id)->assertForbidden();
});

test('upload deduplicates identical bytes even with a different filename', function () {
    $file = UploadedFile::fake()->image('original.jpg', 90, 60);
    $first = $this->postJson('/admin/cms/media', ['image' => $file])->assertCreated()->json();
    $duplicate = UploadedFile::fake()->createWithContent('renamed.jpg', file_get_contents($file->getRealPath()));
    $second = $this->postJson('/admin/cms/media', ['image' => $duplicate])->assertCreated()->json();
    expect($first['id'])->toBe($second['id'])->and(MediaAsset::count())->toBe(1)->and(Storage::disk('public')->allFiles())->toHaveCount(1);
    $this->get('/admin/cms/media')->assertOk()->assertSee('original.jpg');
    $this->get('/admin/cms/media/'.$first['id'])->assertOk()->assertSee('Utilisations');
});

test('non images oversized images and private assets cannot enter the picker', function () {
    $this->postJson('/admin/cms/media', ['image' => UploadedFile::fake()->create('script.svg', 1, 'image/svg+xml')])->assertUnprocessable();
    $this->postJson('/admin/cms/media', ['image' => UploadedFile::fake()->image('huge.jpg')->size(5121)])->assertUnprocessable();
    $asset = libraryImage();
    $asset->update(['visibility' => 'private']);
    $this->getJson('/admin/cms/media')->assertOk()->assertJsonCount(0, 'items');
    expect(fn () => app(MediaLibrary::class)->image($asset->id))->toThrow(\Illuminate\Validation\ValidationException::class);
});

test('metadata is editable and stored html is escaped on the library page', function () {
    $asset = libraryImage();
    $this->put('/admin/cms/media/'.$asset->id, ['name' => '<script>alert(1)</script>', 'alt' => 'Notre activité', 'caption' => 'Terrain'])->assertSessionHasNoErrors();
    $this->get('/admin/cms/media')->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    expect($asset->fresh()->alt)->toBe('Notre activité');
});

test('an unused upload is physically deleted but a shared image is protected', function () {
    $asset = libraryImage();
    $partner = Partner::create(['name' => 'Association', 'logo' => $asset->path]);
    $this->delete('/admin/cms/media/'.$asset->id)->assertSessionHasErrors('image');
    Storage::disk('public')->assertExists($asset->path);
    $partner->delete();
    $this->delete('/admin/cms/media/'.$asset->id)->assertRedirect('/admin/cms/media');
    Storage::disk('public')->assertMissing($asset->path);
    $this->assertDatabaseMissing('media_assets', ['id' => $asset->id]);
});

test('CMS associations and history protect media from deletion', function () {
    $asset = libraryImage();
    $post = Post::create(['title' => 'Actualité', 'category' => 'Terrain', 'slug' => 'test-photo', 'cms_key' => 'test-photo', 'user_id' => auth()->id(), 'content' => 'Texte', 'cover_media_id' => $asset->id]);
    $this->delete('/admin/cms/media/'.$asset->id)->assertSessionHasErrors('image');
    expect(app(MediaLibrary::class)->usages($asset))->toContain('posts #'.$post->id);
});

test('builtin theme images cannot be physically deleted', function () {
    $asset = libraryImage();
    $asset->update(['disk' => 'builtin']);
    $this->delete('/admin/cms/media/'.$asset->id)->assertSessionHasErrors('image');
    expect($asset->fresh())->not->toBeNull();
});

test('existing paths are indexed without copying or removing their files', function () {
    $file = UploadedFile::fake()->image('legacy.jpg');
    Storage::disk('public')->put('partners/legacy.jpg', file_get_contents($file->getRealPath()));
    Partner::create(['name' => 'Ancien partenaire', 'logo' => 'partners/legacy.jpg']);
    $this->artisan('media:sync')->assertSuccessful();
    $this->artisan('media:sync')->assertSuccessful();
    expect(MediaAsset::count())->toBe(1)->and(Storage::disk('public')->allFiles())->toHaveCount(1);
    $asset = app(MediaLibrary::class)->upload($file, auth()->id());
    expect($asset->path)->toBe('partners/legacy.jpg')->and(MediaAsset::count())->toBe(1);
});

test('legacy Livewire creation selects an existing image without a new upload', function () {
    $asset = libraryImage();
    Volt::test('admin.partners.create')->set('name', 'Partenaire partagé')->set('mediaSelections.logo', (string) $asset->id)->call('save')->assertHasNoErrors();
    expect(Partner::first()->logo)->toBe($asset->path)->and(MediaAsset::count())->toBe(1);
    $partner = Partner::first();
    Volt::test('admin.partners.edit', ['partner' => $partner])->set('mediaSelections.new_logo', '')->call('update')->assertHasNoErrors();
    expect($partner->fresh()->logo)->toBe('');
    Storage::disk('public')->assertExists($asset->path);
});

test('legacy uploads are registered centrally and replacement preserves the shared file', function () {
    $asset = libraryImage();
    $partner = Partner::create(['name' => 'Partenaire', 'logo' => $asset->path]);
    $other = Partner::create(['name' => 'Autre partenaire', 'logo' => $asset->path]);
    Volt::test('admin.partners.edit', ['partner' => $partner])->set('new_logo', UploadedFile::fake()->image('replacement.png', 40, 40))->call('update')->assertHasNoErrors();
    expect(MediaAsset::count())->toBe(2)->and($other->fresh()->logo)->toBe($asset->path);
    Storage::disk('public')->assertExists($asset->path);
    Volt::test('admin.partners.create')->set('name', 'Sans logo')->call('save')->assertHasErrors('logo');
    Volt::test('admin.partners.create')->set('name', 'Sélection invalide')->set('mediaSelections.logo', '999999')->call('save')->assertHasErrors('logo');
});

test('news and gallery can share one asset and detach it independently', function () {
    $asset = libraryImage();
    $news = ['title' => 'Nouvelle', 'excerpt' => 'Résumé', 'content' => '<p>Article détaillé.</p>', 'category' => 'Terrain', 'status' => 'draft', 'published_at' => now()->format('Y-m-d'), 'cover_media_id' => $asset->id];
    $this->post(route('admin.cms.news.store'), $news)->assertSessionHasNoErrors();
    $activity = ['title' => 'Action', 'description' => 'Résumé', 'activity_status' => 'ongoing', 'publication_state' => 'draft', 'published_at' => now()->format('Y-m-d'), 'cover_media_id' => $asset->id, 'photo_ids' => [$asset->id, $asset->id]];
    $this->post(route('admin.cms.activities.store'), $activity)->assertSessionHasNoErrors();
    $project = Project::first();
    expect($project->cover_media_id)->toBe($asset->id)->and($project->gallery->items()->count())->toBe(1)->and(MediaAsset::count())->toBe(1);
    $post = Post::first();
    $this->put(route('admin.cms.news.update', $post), array_merge($news, ['revision' => CmsNewsController::revision($post), 'cover_media_id' => '']))->assertSessionHasNoErrors();
    expect($post->fresh()->cover_media_id)->toBeNull()->and($project->fresh()->cover_media_id)->toBe($asset->id);
    Storage::disk('public')->assertExists($asset->path);
});

test('all legacy creation pages render the reusable selector', function () {
    foreach (['partners', 'posts', 'achievements', 'sliders', 'testimonials', 'team-members', 'projects', 'services', 'publications', 'gallery-photos'] as $module) {
        $this->get(route('admin.'.$module.'.create'))->assertOk()->assertSee('data-media-picker', false)->assertSee('media-library-dialog', false);
    }
});
