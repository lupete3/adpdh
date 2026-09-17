<?php

use App\Http\Controllers\CmsActivityController;
use App\Models\Project;
use App\Models\User;
use App\Support\ActivityHtml;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->withoutVite();
});

function activityData(array $overrides = []): array
{
    return array_merge(['title' => 'Une activité', 'description' => 'Résumé sur le terrain', 'content' => '<h2>Notre action</h2><p><strong>Ensemble</strong></p>', 'activity_status' => 'ongoing', 'publication_state' => 'published', 'published_at' => now()->subDay()->format('Y-m-d H:i:s')], $overrides);
}

test('activities are paginated six at a time newest first and hidden records stay private', function () {
    for ($i = 1; $i <= 8; $i++) {
        Project::create(activityData(['title' => 'Activité numéro '.$i, 'slug' => 'action-'.$i, 'cms_key' => 'action-'.$i, 'published_at' => now()->subDays(10 - $i)]));
    }
    $draft = Project::create(activityData(['title' => 'Article secret', 'slug' => 'secret', 'cms_key' => 'secret', 'publication_state' => 'draft']));
    Project::create(activityData(['title' => 'Article futur', 'slug' => 'futur', 'cms_key' => 'futur', 'published_at' => now()->addDay()]));
    Project::create(activityData(['title' => 'Article démo', 'slug' => 'demo', 'cms_key' => 'demo', 'is_demo' => true]));
    $this->get('/activites')->assertOk()->assertSeeInOrder(['Activité numéro 8', 'Activité numéro 7', 'Activité numéro 6', 'Activité numéro 5', 'Activité numéro 4', 'Activité numéro 3'])->assertDontSee('Activité numéro 2')->assertDontSee('Article secret')->assertDontSee('Article futur')->assertDontSee('Article démo')->assertSee('Page 1 sur 2');
    $this->get('/activites?page=2')->assertOk()->assertSee('Activité numéro 2')->assertSee('Activité numéro 1')->assertDontSee('Activité numéro 3');
    foreach (['secret', 'futur', 'demo', 'inexistant'] as $slug) {
        $this->get('/activites/'.$slug)->assertNotFound();
    }
    $this->get('/activites/action-8')->assertOk()->assertSee('<strong>Ensemble</strong>', false);
    $this->get('/activites.html')->assertRedirect('/activites');
    $this->get('/adpdh/activite-action-8.html')->assertRedirect(route('activities.show', 'action-8'));
});

test('administrators create edit and remove gallery images with stale edit protection', function () {
    Storage::fake('public');
    $this->get(route('admin.cms.activities'))->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->post(route('admin.cms.activities.store'), activityData())->assertForbidden();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.cms.activities.create'))->assertOk();
    $this->post(route('admin.cms.activities.store'), activityData(['cover' => UploadedFile::fake()->image('cover.jpg'), 'photos' => [UploadedFile::fake()->image('terrain.jpg')]]))->assertSessionHasNoErrors()->assertRedirect();
    $activity = Project::firstOrFail();
    $photo = $activity->gallery->items()->firstOrFail();
    Storage::disk('public')->assertExists($photo->media->path);
    expect($photo->media->publicUrl())->toBe('/storage/'.$photo->media->path);
    $this->get(route('admin.cms.activities.edit', $activity))->assertOk()->assertSee('Description détaillée');
    $this->get(route('activities.show', $activity->slug))->assertOk()->assertSee('L’activité en images')->assertSee($photo->media->publicUrl(), false);
    $galleryRevision = CmsActivityController::revision($activity->fresh());
    $this->put(route('admin.cms.activities.update', $activity), activityData(['revision' => $galleryRevision, 'captions' => [$photo->id => 'Une légende mise à jour']]))->assertSessionHasNoErrors();
    expect($photo->fresh()->caption)->toBe('Une légende mise à jour');
    $this->put(route('admin.cms.activities.update', $activity), activityData(['revision' => $galleryRevision, 'captions' => [$photo->id => 'Édition périmée']]))->assertSessionHasErrors('revision');
    $data = activityData(['revision' => CmsActivityController::revision($activity->fresh()), 'remove_images' => [$photo->id], 'remove_cover' => 1, 'publication_state' => 'draft']);
    $this->put(route('admin.cms.activities.update', $activity), $data)->assertSessionHasNoErrors();
    expect($activity->fresh()->gallery->items)->toHaveCount(0);
    expect($activity->fresh()->cover_media_id)->toBeNull();
    $this->get(route('activities.show', $activity->slug))->assertNotFound();
    $this->put(route('admin.cms.activities.update', $activity), $data)->assertSessionHasErrors('revision');
    $data['revision'] = CmsActivityController::revision($activity->fresh());
    $data['remove_images'] = [99999];
    $this->put(route('admin.cms.activities.update', $activity), $data)->assertSessionHasErrors('photos');
});

test('rich text rejects active markup and unsafe links while preserving formatting', function () {
    $clean = ActivityHtml::clean('<h2>Titre</h2><p onclick="alert(1)"><strong>Texte</strong><script>alert(1)</script><img src=x onerror=alert(1)><a href="javascript:alert(1)">Lien</a><a href="https://example.org">Site</a></p>');
    expect($clean)->toContain('<h2>Titre</h2>', '<strong>Texte</strong>', 'href="https://example.org"')->not->toContain('script', 'onclick', 'onerror', 'javascript:', '<img');
});

test('activity header editor saves all public heading text without legacy card fields', function () {
    $this->seed(\Database\Seeders\CmsTitlesSeeder::class);
    $page = \App\Models\CmsPage::where('key', 'activites')->firstOrFail();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.cms.activities'))->assertOk()->assertSee('Personnaliser l’en-tête');
    $this->get(route('admin.cms.titles.edit', $page))->assertOk()->assertSee('Texte d’introduction sous le titre')->assertDontSee('value="AVEC &amp; AGR"', false);
    $values = ['text-1' => 'Activités ADPDH', 'text-2' => 'Actions communautaires', 'text-8' => 'SUR LE TERRAIN', 'text-3' => 'Nos engagements.', 'text-4' => 'Nos résultats.', 'introduction' => 'Une introduction personnalisée.', 'list-heading' => 'Découvrez nos interventions'];
    $titles = $page->titles->whereIn('key', array_keys($values))->mapWithKeys(fn ($title) => [$title->id => $values[$title->key]])->all();
    $this->put(route('admin.cms.titles.update', $page), ['version' => $page->version, 'titles' => $titles])->assertSessionHasNoErrors()->assertRedirect();
    $response = $this->get(route('activities'))->assertOk()->assertDontSee('class="container activities-section"', false);
    foreach ($values as $value) $response->assertSee($value);
});
