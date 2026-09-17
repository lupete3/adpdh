<?php

use App\Http\Controllers\CmsSuccessController;
use App\Models\CmsPage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->withoutVite();
});

test('success page displays only visible genuine testimonials without detail links', function () {
    $section = CmsPage::where('key', 'index')->firstOrFail()->sections()->where('key', 'temoignages')->firstOrFail();
    $section->contents()->create(['key' => 'visible-success', 'title' => 'Un parcours publié', 'description' => 'Une expérience vécue.', 'subtitle' => 'Une participante', 'is_visible' => true, 'is_demo' => false, 'link_url' => '/un-detail-interdit', 'link_label' => 'Lire le récit']);
    $hidden = $section->contents()->create(['key' => 'hidden-success', 'title' => 'Témoignage masqué', 'is_visible' => false, 'is_demo' => false]);
    $section->contents()->create(['key' => 'demo-success', 'title' => 'Témoignage fictif', 'is_visible' => true, 'is_demo' => true]);
    $this->get(route('success'))->assertOk()->assertSee('Un parcours publié')->assertSee('Une expérience vécue.')->assertSee('Une participante')->assertDontSee('Témoignage masqué')->assertDontSee('Témoignage fictif')->assertDontSee('/un-detail-interdit')->assertDontSee('Lire le récit')->assertDontSee('Retrouvez-nous sur les réseaux sociaux');
    $hidden->update(['is_visible' => true]);
    $this->get(route('success'))->assertSee('Témoignage masqué');
    $this->get('/adpdh/temoignages.html')->assertRedirect('/nos-succes');
    expect(adpdh_url('temoignages.html'))->toBe(route('success'));
    $this->get('/')->assertSee(route('success'), false)->assertDontSee('/un-detail-interdit');
});

test('social links can be configured enabled and hidden individually by administrators', function () {
    $this->get(route('admin.cms.success'))->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->put(route('admin.cms.success.save'), [])->assertForbidden();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.cms.success'))->assertOk()->assertSee('Afficher TikTok');
    $data = ['introduction' => 'Nos récits et nos résultats.', 'social' => []];
    foreach (CmsSuccessController::NETWORKS as $key => $label) {
        $data['social'][$key] = ['enabled' => 1, 'url' => 'https://example.org/'.$key];
    }
    $this->put(route('admin.cms.success.save'), $data)->assertSessionHasNoErrors();
    $response = $this->get(route('success'))->assertOk()->assertSee($data['introduction']);
    foreach (array_keys(CmsSuccessController::NETWORKS) as $key) {
        $response->assertSee('https://example.org/'.$key, false);
    }
    $data['social']['facebook']['enabled'] = 0;
    $this->put(route('admin.cms.success.save'), $data)->assertSessionHasNoErrors();
    $this->get(route('success'))->assertDontSee('https://example.org/facebook', false)->assertSee('https://example.org/x', false);
    expect(DB::table('settings')->where('key', 'adpdh.success.social.facebook.url')->value('value'))->toBe('https://example.org/facebook');
    $data['social']['x']['url'] = 'javascript:alert(1)';
    $this->put(route('admin.cms.success.save'), $data)->assertSessionHasErrors('social.x.url');
    $data['social']['x']['url'] = '';
    $this->put(route('admin.cms.success.save'), $data)->assertSessionHasErrors('social.x.url');
});
