<?php

use App\Http\Controllers\CmsWorkController;
use App\Models\CmsPage;
use App\Models\InterventionAxis;
use App\Models\Pillar;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\WorkPageSeeder;
use Illuminate\Support\Facades\DB;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->withoutVite();
});

test('work page preserves template pillars axes complementary block and Laravel links', function () {
    $response = $this->get(route('work'))->assertOk()->assertSee('Quatre piliers.')->assertSee('neuf axes')->assertSee('Une même dignité.')->assertSee('DES ACTIONS COMPLÉMENTAIRES')->assertSee('Un projet peut relier plusieurs piliers.')->assertSee('Découvrir nos activités');
    foreach (['developpement', 'protection', 'gouvernance', 'droits'] as $key) {
        $response->assertSee('href="#'.$key.'"', false)->assertSee('id="'.$key.'"', false);
    }
    foreach ([1, 5, 7, 9] as $start) {
        $response->assertSee('class="axes-list" start="'.$start.'"', false);
    }
    expect(substr_count($response->getContent(), 'class="section container domain-section"'))->toBe(4);
    $this->get('/')->assertSee(route('work'), false)->assertDontSee('adpdh/que-faisons-nous.html', false);
    $this->get('/que-faisons-nous.html')->assertRedirect('/que-faisons-nous');
    $this->get('/adpdh/que-faisons-nous.html')->assertRedirect('/que-faisons-nous');
    expect(adpdh_url('que-faisons-nous.html#droits'))->toBe(route('work').'#droits');
});

test('work administration is protected and all editors render', function () {
    $this->get(route('admin.cms.work'))->assertRedirect('/login');
    $this->post(route('admin.cms.work.pillars.store'), [])->assertRedirect('/login');
    $this->actingAs(User::factory()->create());
    $this->get(route('admin.cms.work'))->assertForbidden();
    $this->post(route('admin.cms.work.pillars.store'), [])->assertForbidden();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $pillar = Pillar::firstOrFail();
    $axis = $pillar->axes()->firstOrFail();
    foreach ([route('admin.cms.work'), route('admin.cms.work.pillars.create'), route('admin.cms.work.pillars.edit', $pillar), route('admin.cms.work.axes.create', $pillar), route('admin.cms.work.axes.edit', [$pillar, $axis])] as $url) {
        $this->get($url)->assertOk();
    }
    $page = CmsPage::where('key', 'que-faisons-nous')->firstOrFail();
    foreach ($page->sections as $section) {
        $this->get(route('admin.cms.work.section', $section))->assertOk();
    }
    $this->get(route('admin.cms.titles.edit', $page))->assertRedirect(route('admin.cms.work'));
    $this->get(route('admin.cms.preview', $page))->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('pillars and axes grow shrink and reorder with automatic counts and numbering', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $data = ['title' => 'Nouveau pilier', 'description' => 'Notre nouvelle priorité', 'sort_order' => 0, 'is_visible' => 1];
    $this->post(route('admin.cms.work.pillars.store'), $data)->assertSessionHasNoErrors();
    $pillar = Pillar::where('title', $data['title'])->firstOrFail();
    $this->get(route('work'))->assertSee('Cinq piliers.')->assertSeeInOrder(['01 — Nouveau pilier', '02 — Développement']);
    $axisData = ['title' => 'Nouvel axe', 'description' => 'Description axe', 'sort_order' => 1, 'is_visible' => 1];
    $this->post(route('admin.cms.work.axes.store', $pillar), $axisData)->assertSessionHasNoErrors();
    $axis = $pillar->axes()->firstOrFail();
    $this->get(route('work'))->assertSee('dix axes')->assertSee('class="axes-list" start="2"', false)->assertSee('Nouvel axe');
    $axisData['revision'] = CmsWorkController::revision($axis);
    $axisData['title'] = '<script>Axe modifié</script>';
    $this->put(route('admin.cms.work.axes.update', [$pillar, $axis]), $axisData)->assertSessionHasNoErrors();
    $this->get(route('work'))->assertSee('&lt;script&gt;Axe modifié&lt;/script&gt;', false)->assertDontSee($axisData['title'], false);
    $this->put(route('admin.cms.work.axes.update', [$pillar, $axis]), $axisData)->assertSessionHasErrors('revision');
    $data['revision'] = CmsWorkController::revision($pillar->fresh());
    $data['is_visible'] = 0;
    $this->put(route('admin.cms.work.pillars.update', $pillar), $data)->assertSessionHasNoErrors();
    $this->get(route('work'))->assertSee('Quatre piliers.')->assertSee('neuf axes')->assertDontSee('Nouveau pilier')->assertDontSee('Axe modifié');
    $this->delete(route('admin.cms.work.pillars.destroy', $pillar), ['revision' => CmsWorkController::revision($pillar->fresh())])->assertSessionHasNoErrors();
    expect(Pillar::find($pillar->id))->toBeNull();
    $this->get(route('admin.cms.work.pillars.edit', $pillar->id))->assertNotFound();
});

test('axis writes cannot cross pillars and removing linked axes preserves activity associations', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $axis = InterventionAxis::whereHas('activities')->firstOrFail();
    $pillar = $axis->pillar;
    $other = Pillar::where('id', '!=', $pillar->id)->firstOrFail();
    $data = ['revision' => CmsWorkController::revision($axis), 'title' => 'Test', 'description' => 'Test', 'sort_order' => 0, 'is_visible' => 1];
    $this->get(route('admin.cms.work.axes.edit', [$other, $axis]))->assertNotFound();
    $this->put(route('admin.cms.work.axes.update', [$other, $axis]), $data)->assertNotFound();
    $this->delete(route('admin.cms.work.axes.destroy', [$other, $axis]), $data)->assertNotFound();
    $links = DB::table('activity_axis')->where('intervention_axis_id', $axis->id)->count();
    $this->delete(route('admin.cms.work.axes.destroy', [$pillar, $axis]), $data)->assertSessionHasNoErrors();
    expect(InterventionAxis::find($axis->id))->toBeNull();
    expect(DB::table('activity_axis')->where('intervention_axis_id', $axis->id)->count())->toBe($links);
    $this->get(route('work'))->assertSee('huit axes')->assertDontSee('id="'.$axis->key.'"', false);
    $this->seed(DatabaseSeeder::class);
    expect(InterventionAxis::find($axis->id))->toBeNull();
});

test('empty and single pillar pages render accurately and hidden axes do not count', function () {
    $keep = Pillar::firstOrFail();
    Pillar::where('id', '!=', $keep->id)->update(['is_visible' => false]);
    $keep->axes()->update(['is_visible' => false]);
    $this->get(route('work'))->assertOk()->assertSee('Un pilier.')->assertSee('aucun axe')->assertSee('un pilier complémentaire.')->assertDontSee('class="axes-list"', false);
    $keep->delete();
    $this->get(route('work'))->assertOk()->assertSee('Aucun pilier.')->assertDontSee('class="container pillar-jumps"', false)->assertSee('Un projet peut relier plusieurs piliers.');
    $this->seed(DatabaseSeeder::class);
    expect(Pillar::find($keep->id))->toBeNull();
});

test('page texts and final call to action are editable and protected from stale changes', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $page = CmsPage::where('key', 'que-faisons-nous')->firstOrFail();
    $section = $page->sections()->where('key', 'complementarite')->firstOrFail();
    $data = ['section_id' => $section->id, 'version' => $section->version, 'title' => 'Titre complémentaire personnalisé', 'title_accent' => 'Texte coloré', 'introduction' => 'Introduction personnalisée', 'body_text' => 'Paragraphe personnalisé', 'sort_order' => 1, 'is_visible' => 1, 'buttons' => [['label' => 'Voir notre organisation', 'url' => '/qui-sommes-nous']]];
    $this->put(route('admin.cms.work.section.update', $section), $data)->assertSessionHasNoErrors();
    $this->get(route('work'))->assertSee($data['title'])->assertSee('Texte coloré')->assertSee('Paragraphe personnalisé')->assertSee('Voir notre organisation');
    $this->put(route('admin.cms.work.section.update', $section), $data)->assertSessionHasErrors('version');
    $this->put(route('admin.cms.about.section.update', $section), $data)->assertNotFound();
    $this->seed(WorkPageSeeder::class);
    expect($section->fresh()->title)->toBe($data['title']);
    $this->put(route('admin.cms.work.seo'), ['version' => $page->version, 'seo_title' => 'Nos domaines', 'seo_description' => 'Découvrez {piliers} et {axes}.'])->assertSessionHasNoErrors();
    $this->get(route('work'))->assertSee('<title>Nos domaines</title>', false)->assertSee('Découvrez quatre piliers et neuf axes.');
});
