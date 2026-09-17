<?php

use App\Http\Controllers\CmsWorkController;
use App\Models\PartnershipType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->withoutVite();
});

test('partnership reasons are editable ordered and visibility controlled', function () {
    $this->get(route('admin.cms.partnership'))->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->post(route('admin.cms.partnership.store'), [])->assertForbidden();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.cms.partnership'))->assertOk();
    $this->get(route('admin.cms.partnership.create'))->assertOk();
    $data = ['title' => 'Une nouvelle raison', 'description' => 'Un engagement durable.', 'sort_order' => 0, 'is_visible' => 1];
    $this->post(route('admin.cms.partnership.store'), $data)->assertSessionHasNoErrors();
    $reason = PartnershipType::where('title', $data['title'])->firstOrFail();
    $this->get(route('partnership'))->assertOk()->assertSee($data['title'])->assertSee($data['description'])->assertSee('partner-options')->assertDontSee('Télécharger la présentation (PDF)');
    $this->get(route('admin.cms.partnership.edit', $reason))->assertOk();
    $data['revision'] = CmsWorkController::revision($reason);
    $data['is_visible'] = 0;
    $this->put(route('admin.cms.partnership.update', $reason), $data)->assertSessionHasNoErrors();
    $this->get(route('partnership'))->assertDontSee($data['title']);
    $this->put(route('admin.cms.partnership.update', $reason), $data)->assertSessionHasErrors('revision');
    $this->get('/adpdh/devenir-partenaire.html')->assertRedirect('/devenir-partenaire');
});

test('pdf replacement serves latest bytes at the same URL and removal disables download', function () {
    Storage::fake('local');
    $url = route('partnership.download');
    $this->get($url)->assertNotFound();
    $this->post(route('admin.cms.partnership.document'), [])->assertRedirect('/login');
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $first = "%PDF-1.4\n% First presentation\n%%EOF";
    $second = "%PDF-1.4\n% Updated presentation\n%%EOF";
    $this->post(route('admin.cms.partnership.document'), ['pdf' => UploadedFile::fake()->createWithContent('presentation.pdf', $first)])->assertSessionHasNoErrors();
    $response = $this->get($url)->assertOk()->assertDownload('presentation-adpdh.pdf');
    expect($response->streamedContent())->toBe($first);
    $this->get(route('partnership'))->assertSee($url, false)->assertSee('Télécharger la présentation (PDF)');
    $this->post(route('admin.cms.partnership.document'), ['pdf' => UploadedFile::fake()->createWithContent('presentation.pdf', $second)])->assertSessionHasNoErrors();
    expect($this->get($url)->assertOk()->streamedContent())->toBe($second);
    $this->post(route('admin.cms.partnership.document'), ['pdf' => UploadedFile::fake()->createWithContent('bad.pdf', '<html>Invalid document</html>')->mimeType('text/html')])->assertSessionHasErrors('pdf');
    expect($this->get($url)->assertOk()->streamedContent())->toBe($second);
    $this->delete(route('admin.cms.partnership.document.remove'))->assertSessionHasNoErrors();
    $this->get($url)->assertNotFound();
    $this->get(route('partnership'))->assertDontSee('Télécharger la présentation (PDF)');
});
