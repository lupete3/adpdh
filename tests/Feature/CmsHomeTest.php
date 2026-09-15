<?php

use App\Models\CmsPage;
use App\Models\Indicator;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});
test('home renders structured content and excludes draft examples', function () {
    $this->get('/')->assertOk()->assertSee('Quatre piliers.')->assertSee('1 400')->assertDontSee('Témoignage fictif');
    $page = CmsPage::where('key', 'index')->firstOrFail();
    $section = $page->sections()->where('key', 'piliers')->firstOrFail();
    $section->update(['title' => 'Un titre modifié', 'introduction' => 'Texte saisi par administration']);
    $this->get('/')->assertSee('Un titre modifié')->assertSee('Texte saisi par administration');
    $section->update(['is_visible' => false]);
    $this->get('/')->assertDontSee('Un titre modifié');
});
test('home management is admin only and section writes retain revisions and reject stale updates', function () {
    $this->get(route('admin.cms.home'))->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->get(route('admin.cms.home'))->assertForbidden();
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin)->withoutVite()->get(route('admin.cms.home'))->assertOk();
    $section = CmsPage::where('key', 'index')->firstOrFail()->sections()->where('key', 'piliers')->firstOrFail();
    $data = ['section_id' => $section->id, 'version' => $section->version, 'title' => '<script>test</script>', 'is_visible' => 1, 'sort_order' => 3];
    $this->put(route('admin.cms.home.section', $section), $data)->assertSessionHasNoErrors()->assertRedirect();
    expect($section->revisions()->count())->toBe(1);
    $this->get('/')->assertSee('&lt;script&gt;test&lt;/script&gt;', false)->assertDontSee('<script>test</script>', false);
    $this->put(route('admin.cms.home.section', $section), $data)->assertSessionHasErrors('version');
    $data['version'] = $section->fresh()->version;
    $data['buttons'] = [['label' => 'Lien', 'url' => 'javascript:alert(1)']];
    $this->put(route('admin.cms.home.section', $section), $data)->assertSessionHasErrors('buttons.0.url');
});
test('admin updates impact and shared titles on the home page', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $indicator = Indicator::where('key', 'avec-membres')->firstOrFail();
    $this->post(route('admin.cms.home.indicator', $indicator), ['value' => 118, 'source' => 'Rapport validé'])->assertSessionHasNoErrors();
    expect($indicator->values()->count())->toBe(2);
    $this->get('/')->assertSee('118');
    $section=CmsPage::where('key','index')->firstOrFail()->sections()->where('key','piliers')->firstOrFail();
    $item=$section->contents()->first();
    $data=['version'=>$item->version,'title'=>'Notre pilier personnalisé','description'=>'Description actualisée','sort_order'=>1,'is_visible'=>1,'is_demo'=>0];
    $this->put(route('admin.cms.sections.update',[$section,$item]),$data)->assertSessionHasNoErrors();
    $this->get('/')->assertSee('Notre pilier personnalisé');
    $this->put(route('admin.cms.sections.update',[$section,$item]),$data)->assertSessionHasErrors('version');
});

test('home limits collections and renders all visible testimonials', function () {
    $page = CmsPage::where('key', 'index')->firstOrFail();
    foreach (['activites' => 3, 'actualites' => 4, 'temoignages' => 6] as $key => $limit) {
        $section = $page->sections()->where('key', $key)->firstOrFail();
        $section->update(['is_visible' => true]);
        $section->contents()->delete();
        foreach (range(1, 6) as $n) {
            $section->contents()->create(['key' => 'check-'.$key.'-'.$n, 'title' => 'check-'.$key.'-'.$n, 'is_visible' => true, 'is_demo' => false, 'sort_order' => $n]);
        }
        $section->contents()->create(['key' => 'hidden-'.$key, 'title' => 'hidden-'.$key, 'is_visible' => false]);
        $section->contents()->create(['key' => 'demo-'.$key, 'title' => 'demo-'.$key, 'is_visible' => true, 'is_demo' => true]);
    }
    $response = $this->get('/')->assertOk();
    foreach (['activites' => 3, 'actualites' => 4, 'temoignages' => 6] as $key => $limit) {
        foreach (range(1, 6) as $n) {
            if ($n > 6 - $limit) {
                $response->assertSee('check-'.$key.'-'.$n);
            } else {
                $response->assertDontSee('check-'.$key.'-'.$n);
            }
        }
        $response->assertDontSee('hidden-'.$key)->assertDontSee('demo-'.$key);
    }
    $response->assertSee('data-testimonial-carousel', false)->assertSee('data-next', false);
});
