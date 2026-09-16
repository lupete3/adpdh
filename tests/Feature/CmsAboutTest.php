<?php

use App\Http\Controllers\CmsAboutController;
use App\Models\CmsPage;
use App\Models\InterventionZone;
use App\Models\OrganizationValue;
use App\Models\TeamMember;
use App\Models\User;
use Database\Seeders\AboutPageSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('about image captions and cover notes are editable independently and can be cleared', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $sections = CmsPage::where('key', 'qui-sommes-nous')->firstOrFail()->sections()->get()->keyBy('key');
    $this->get(route('organization'))->assertSee('Depuis 2010')->assertSee('Développement · Protection · Droits humains')->assertSee('Image d’illustration générée par IA · à remplacer');
    foreach (['hero', 'valeurs'] as $key) {
        $section = $sections[$key];
        $this->get(route('admin.cms.about.section', $section))->assertOk()->assertSee('Légende en bas de l’image');
        $data = ['section_id' => $section->id, 'version' => $section->version, 'sort_order' => $section->sort_order, 'is_visible' => 1, 'image_caption' => 'Légende '.$key.' <script>alert(1)</script>'];
        if ($key === 'hero') $data += ['image_note_title' => 'Depuis notre création', 'image_note_text' => 'Nos engagements personnalisés'];
        $this->put(route('admin.cms.about.section.update', $section), $data)->assertSessionHasNoErrors();
    }
    $this->get(route('organization'))->assertSee('Depuis notre création')->assertSee('Nos engagements personnalisés')->assertSee('Légende hero &lt;script&gt;alert(1)&lt;/script&gt;', false)->assertSee('Légende valeurs &lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    $hero = $sections['hero']->fresh();
    $this->put(route('admin.cms.about.section.update', $hero), ['version' => $hero->version, 'sort_order' => $hero->sort_order, 'is_visible' => 1, 'image_caption' => '', 'image_note_title' => '', 'image_note_text' => ''])->assertSessionHasNoErrors();
    $this->seed(AboutPageSeeder::class);
    $this->get(route('organization'))->assertDontSee('Depuis notre création')->assertDontSee('Légende hero')->assertDontSee('class="cover-note"', false)->assertSee('Légende valeurs');
});

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->withoutVite();
});

test('organization route renders database sections collections and Laravel links', function () {
    $response = $this->get(route('organization'))->assertOk();
    foreach (['presentation', 'histoire', 'vision-mission', 'valeurs', 'statut', 'zones', 'equipe'] as $anchor) {
        $response->assertSee('id="'.$anchor.'"', false);
    }
    $response->assertSee('Perspectives d’extension')->assertSee('10 janvier 2010, à Bukavu');
    $this->get('/')->assertSee(route('organization').'#equipe', false)->assertDontSee('adpdh/qui-sommes-nous.html', false);
    $this->get('/qui-sommes-nous.html')->assertRedirect('/qui-sommes-nous');
    $this->get('/adpdh/qui-sommes-nous.html')->assertRedirect('/qui-sommes-nous');
    expect(adpdh_url('qui-sommes-nous.html#histoire'))->toBe(route('organization').'#histoire');
});

test('organization administration and writes are restricted to administrators', function () {
    $this->get(route('admin.cms.about'))->assertRedirect('/login');
    $this->actingAs(User::factory()->create());
    $this->get(route('admin.cms.about'))->assertForbidden();
    $this->post(route('admin.cms.about.store', 'valeurs'), [])->assertForbidden();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.cms.about'))->assertOk();
    foreach (CmsPage::where('key', 'qui-sommes-nous')->first()->sections as $section) {
        $this->get(route('admin.cms.about.section', $section))->assertOk();
    }
    foreach (array_keys(CmsAboutController::COLLECTIONS) as $kind) {
        $this->get(route('admin.cms.about.create', $kind))->assertOk();
    }
    $this->get(route('admin.cms.about.create', 'unknown'))->assertNotFound();
});

test('values can be added edited reordered hidden and deleted without stale overwrites', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $data = ['title' => 'Valeur personnalisée', 'description' => 'Description de valeur', 'sort_order' => 0, 'is_visible' => 1];
    $this->post(route('admin.cms.about.store', 'valeurs'), $data)->assertSessionHasNoErrors();
    $record = OrganizationValue::where('title', $data['title'])->firstOrFail();
    $this->get(route('organization'))->assertSee($data['title']);
    $data['revision'] = CmsAboutController::revision($record);
    $data['title'] = '<script>Valeur modifiée</script>';
    $this->put(route('admin.cms.about.update', ['valeurs', $record->id]), $data)->assertSessionHasNoErrors();
    $this->get(route('organization'))->assertSee('&lt;script&gt;Valeur modifiée&lt;/script&gt;', false)->assertDontSee($data['title'], false);
    $this->put(route('admin.cms.about.update', ['valeurs', $record->id]), $data)->assertSessionHasErrors('revision');
    $data['revision'] = CmsAboutController::revision($record->fresh());
    $data['is_visible'] = 0;
    $this->put(route('admin.cms.about.update', ['valeurs', $record->id]), $data)->assertSessionHasNoErrors();
    $this->get(route('organization'))->assertDontSee('Valeur modifiée');
    $this->delete(route('admin.cms.about.destroy', ['valeurs', $record->id]), ['revision' => CmsAboutController::revision($record->fresh())])->assertSessionHasNoErrors();
    expect($record->fresh())->toBeNull();
});

test('zones distinguish current and planned and support an empty collection', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $data = ['title' => 'Zone test', 'description' => 'Zone à ouvrir', 'sort_order' => 0, 'is_visible' => 1, 'zone_status' => 'planned'];
    $this->post(route('admin.cms.about.store', 'zones'), $data)->assertSessionHasNoErrors();
    $this->get(route('organization'))->assertSeeInOrder(['Implantations actuelles', 'Perspectives d’extension', 'Zone test']);
    $data['zone_status'] = 'invalid';
    $this->post(route('admin.cms.about.store', 'zones'), $data)->assertSessionHasErrors('zone_status');
    InterventionZone::query()->delete();
    $this->get(route('organization'))->assertOk()->assertDontSee('Perspectives d’extension');
});

test('team publication contact visibility portrait uploads and deletion are respected', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $data = ['name' => 'Membre de test', 'position' => 'Responsable test', 'email' => 'private@example.org', 'phone' => '+243 123456', 'show_contacts' => 0, 'publication_state' => 'draft', 'sort_order' => 0];
    $this->post(route('admin.cms.about.store', 'equipe'), $data + ['portrait_upload' => UploadedFile::fake()->image('portrait.jpg')])->assertSessionHasNoErrors();
    $record = TeamMember::where('name', $data['name'])->firstOrFail();
    Storage::disk('public')->assertExists($record->portrait->path);
    $this->get(route('organization'))->assertDontSee($data['name']);
    $data += ['photo_media_id' => $record->photo_media_id];
    $data['publication_state'] = 'published';
    $data['revision'] = CmsAboutController::revision($record);
    $this->put(route('admin.cms.about.update', ['equipe', $record->id]), $data)->assertSessionHasNoErrors();
    $this->get(route('organization'))->assertSee($data['name'])->assertDontSee($data['email']);
    $data['revision'] = CmsAboutController::revision($record->fresh());
    $data['show_contacts'] = 1;
    $this->put(route('admin.cms.about.update', ['equipe', $record->id]), $data)->assertSessionHasNoErrors();
    $this->get(route('organization'))->assertSee('mailto:'.$data['email'], false);
    $this->delete(route('admin.cms.about.destroy', ['equipe', $record->id]), ['revision' => CmsAboutController::revision($record->fresh())])->assertSessionHasNoErrors();
    $this->get(route('organization'))->assertDontSee($data['name']);
});

test('section edits are public escaped versioned and cannot cross pages', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $page = CmsPage::where('key', 'qui-sommes-nous')->firstOrFail();
    $section = $page->sections()->where('key', 'histoire')->firstOrFail();
    $data = ['section_id' => $section->id, 'version' => $section->version, 'title' => 'Histoire mise à jour', 'body_text' => 'Nouveau récit', 'sort_order' => 1, 'is_visible' => 1];
    $this->put(route('admin.cms.about.section.update', $section), $data)->assertSessionHasNoErrors();
    $this->get(route('organization'))->assertSee($data['title'])->assertSee('Nouveau récit');
    $this->put(route('admin.cms.about.section.update', $section), $data)->assertSessionHasErrors('version');
    $this->put(route('admin.cms.home.section', $section), $data)->assertNotFound();
    $this->get(route('admin.cms.titles.edit', $page))->assertRedirect(route('admin.cms.about'));
    $this->get(route('admin.cms.preview', $page))->assertOk()->assertSee('Nouveau récit');
    $this->seed(AboutPageSeeder::class);
    expect($section->fresh()->title)->toBe($data['title']);
    $section->update(['is_visible' => false]);
    $this->get(route('organization'))->assertDontSee('id="histoire"', false)->assertDontSee('href="#histoire"', false);
});

test('legal information and search presentation are editable and preserve removals on initialization', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $page = CmsPage::where('key', 'qui-sommes-nous')->firstOrFail();
    $section = $page->sections()->where('key', 'statut')->firstOrFail();
    $content = $section->contents()->firstOrFail();
    $this->get(route('admin.cms.sections.content.edit', [$section, $content]))->assertOk();
    $data = ['version' => $content->version, 'title' => 'Libellé juridique', 'description' => 'Texte juridique personnalisé', 'is_visible' => 1, 'is_demo' => 0, 'sort_order' => 0];
    $this->put(route('admin.cms.sections.update', [$section, $content]), $data)->assertSessionHasNoErrors();
    $this->get(route('organization'))->assertSee($data['title'])->assertSee($data['description']);
    $this->delete(route('admin.cms.sections.destroy', [$section, $content]), ['version' => $content->fresh()->version])->assertSessionHasNoErrors();
    $this->seed(AboutPageSeeder::class);
    $this->get(route('organization'))->assertDontSee($data['title']);
    $this->put(route('admin.cms.about.seo'), ['version' => $page->version, 'seo_title' => 'Présentation personnalisée', 'seo_description' => 'Description personnalisée'])->assertSessionHasNoErrors();
    $this->get(route('organization'))->assertSee('<title>Présentation personnalisée</title>', false);
});

test('history supports new milestones and invalid values do not reach storage', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $data = ['title' => 'Nouvelle étape', 'description' => 'Récit de cette étape', 'period_label' => '2026', 'sort_order' => 0, 'is_visible' => 1];
    $this->post(route('admin.cms.about.store', 'histoire'), $data)->assertSessionHasNoErrors();
    $this->get(route('organization'))->assertSee('Nouvelle étape');
    $data['description'] = '';
    $this->post(route('admin.cms.about.store', 'valeurs'), $data)->assertSessionHasErrors('description');
    $data['description'] = 'Description';
    $data['period_label'] = '';
    $this->post(route('admin.cms.about.store', 'histoire'), $data)->assertSessionHasErrors('period_label');
    $legacy = TeamMember::create(['name' => 'Ancien membre hors CMS', 'position' => 'Fonction', 'photo' => null]);
    $this->get(route('admin.cms.about.edit', ['equipe', $legacy->id]))->assertNotFound();
    $this->get(route('organization'))->assertDontSee('Ancien membre hors CMS');
});
