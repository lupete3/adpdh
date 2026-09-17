<?php

use App\Http\Controllers\CmsImpactController;
use App\Models\Indicator;
use App\Models\User;
use App\Services\Cms\RecordIndicatorValue;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->withoutVite();
});

test('impact reads latest visible values without sources section and keeps human stories', function () {
    $indicator = Indicator::create(['key' => 'test-impact', 'title' => 'Indicateur évolutif', 'unit' => 'percent', 'is_visible' => true, 'sort_order' => 0]);
    $recorder = app(RecordIndicatorValue::class);
    $recorder->handle($indicator, ['value' => 81.25, 'source' => 'Source interne', 'period_label' => 'Septembre 2026']);
    $recorder->handle($indicator, ['value' => 79.5, 'source' => 'Correction interne', 'period_label' => 'Octobre 2026']);
    $this->get(route('impact'))->assertOk()->assertSee('Indicateur évolutif')->assertSee('79,5 %')->assertSee('Octobre 2026')->assertDontSee('81,25')->assertDontSee('Source interne')->assertDontSee('SOURCES ET PÉRIMÈTRES')->assertDontSee('Lire les chiffres avec leur contexte')->assertSee('AU-DELÀ DES CHIFFRES')->assertSee(route('success'), false);
    $indicator->update(['is_visible' => false]);
    $this->get(route('impact'))->assertDontSee('Indicateur évolutif');
    $this->get('/adpdh/impact.html')->assertRedirect('/notre-impact');
});

test('impact administrators manage visibility and append validated historical values', function () {
    $this->get(route('admin.cms.impact'))->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->post(route('admin.cms.impact.store'), [])->assertForbidden();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.cms.impact'))->assertOk();
    $this->get(route('admin.cms.impact.create'))->assertOk();
    $data = ['title' => 'Participation des femmes', 'unit' => 'percent', 'is_visible' => 1, 'sort_order' => 2, 'value' => 60, 'source' => 'Rapport annuel', 'period_label' => '2026'];
    $this->post(route('admin.cms.impact.store'), $data)->assertSessionHasNoErrors();
    $indicator = Indicator::where('title', $data['title'])->firstOrFail();
    $this->get(route('admin.cms.impact.edit', $indicator))->assertOk()->assertSee('Historique des relevés');
    $data['revision'] = CmsImpactController::revision($indicator);
    $data['value'] = 101;
    $this->put(route('admin.cms.impact.update', $indicator), $data)->assertSessionHasErrors('value');
    expect($indicator->values()->count())->toBe(1);
    $data['value'] = 55;
    $this->put(route('admin.cms.impact.update', $indicator), $data)->assertSessionHasNoErrors();
    expect($indicator->values()->count())->toBe(2);
    $this->get(route('impact'))->assertSee('55 %');
    $this->put(route('admin.cms.impact.update', $indicator), $data)->assertSessionHasErrors('revision');
    $data['revision'] = CmsImpactController::revision($indicator->fresh());
    $data['value'] = '';
    $data['is_visible'] = 0;
    $this->put(route('admin.cms.impact.update', $indicator), $data)->assertSessionHasNoErrors();
    expect($indicator->values()->count())->toBe(2);
    $this->get(route('impact'))->assertDontSee('Participation des femmes');
});
