<?php

use App\Http\Controllers\CmsDonationController;
use App\Models\{CmsPage, User};
use Illuminate\Support\Facades\DB;

beforeEach(function () { $this->withoutVite(); });

test('donation page shows existing bank details and legacy links reach the dynamic page', function () {
    $this->get(route('donation'))->assertOk()->assertSee('Equity BCDC')->assertSee('400200086445762');
    DB::table('settings')->insert(['key' => 'adpdh.donation.account_number', 'value' => '0012345678']);
    $this->get(route('donation'))->assertOk()->assertSee('0012345678')->assertDontSee('400200086445762');
    foreach (['/faire-un-don.html', '/adpdh/faire-un-don.html'] as $path) $this->get($path)->assertRedirect('/faire-un-don');
    expect(adpdh_url('faire-un-don.html#questions'))->toBe(route('donation').'#questions');
});

test('only administrators can update donation copy and bank details with validation and escaping', function () {
    $data = array_merge(CmsDonationController::DEFAULTS, ['account_number' => '000123456789', 'heading' => 'Votre soutien change des vies', 'answer_1' => '<script>alert(1)</script>', 'email' => 'dons@example.org']);
    $this->put(route('admin.cms.donation.update'), $data)->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->put(route('admin.cms.donation.update'), $data)->assertForbidden();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.cms.donation'))->assertOk()->assertSee('Numéro de compte')->assertSee('Questions fréquentes');
    $this->put(route('admin.cms.donation.update'), array_merge($data, ['email' => 'bad', 'account_number' => '']))->assertSessionHasErrors(['email', 'account_number']);
    $this->assertDatabaseMissing('settings', ['key' => 'adpdh.donation.heading']);
    $this->put(route('admin.cms.donation.update'), $data)->assertSessionHasNoErrors()->assertRedirect(route('admin.cms.donation'));
    $this->get(route('donation'))->assertOk()->assertSee($data['heading'])->assertSee('000123456789')->assertSee('mailto:dons@example.org', false)->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
    $page = CmsPage::create(['key' => 'faire-un-don', 'label' => 'Faire un don', 'path' => 'faire-un-don.html']);
    $this->get(route('admin.cms.titles.edit', $page))->assertRedirect(route('admin.cms.donation'));
    $this->get(route('admin.cms.preview', $page))->assertOk()->assertSee($data['heading']);
});
