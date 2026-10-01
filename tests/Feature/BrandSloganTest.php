<?php

use App\Livewire\SettingsManager;
use App\Models\User;
use App\Support\BrandSlogan;
use Livewire\Livewire;

test('settings slogan is saved and rendered on two escaped header lines', function () {
    $this->withoutVite();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    Livewire::test(SettingsManager::class)
        ->set('slogan', "Action pour nos communautés\nPromotion des droits humains")
        ->call('save')->assertHasNoErrors();
    $this->get(route('contact'))->assertOk()
        ->assertSee('<span class="brand-name-line">Action pour nos communautés</span>', false)
        ->assertSee('<span class="brand-name-line">Promotion des droits humains</span>', false);
    Livewire::test(SettingsManager::class)->set('slogan', '<script>alert(1)</script>')->call('save')->assertHasNoErrors();
    $this->get(route('contact'))->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
    expect(BrandSlogan::lines(null))->toBe(explode("\n", BrandSlogan::DEFAULT));
    expect(BrandSlogan::lines(str_replace("\n", ' ', BrandSlogan::DEFAULT)))->toBe(explode("\n", BrandSlogan::DEFAULT));
});
