<?php

use App\Models\User;
use App\Support\ContactAddresses;
use Illuminate\Support\Facades\DB;

test('administrators save one address per line for contact and footer', function () {
    $this->seed(\Database\Seeders\DatabaseSeeder::class);
    $this->withoutVite();
    $address = "22, avenue Galilee, Kinshasa\n305, avenue Patrice Emery Lumumba, Bukavu, Sud-Kivu\n48, quartier Namyanda, Uvira";
    $data = ['email' => 'contact@adpdh.org', 'phone' => '+243 896 263 558', 'address' => $address];
    $this->actingAs(User::factory()->create())->put(route('admin.cms.home.contact'), $data)->assertForbidden();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.cms.home'))->assertOk()->assertSee('Adresses — une par ligne')->assertSee('<textarea', false);
    $this->put(route('admin.cms.home.contact'), $data)->assertSessionHasNoErrors();
    expect(DB::table('settings')->where('key', 'adpdh.contact.address')->value('value'))->toBe($address);
    foreach (['contact', 'home', 'donation'] as $route) {
        $this->get(route($route))->assertOk()->assertSee($address)->assertSee('white-space:pre-line', false);
    }
    $this->put(route('admin.cms.home.contact'), array_merge($data, ['address' => '<script>alert(1)</script>']))->assertSessionHasNoErrors();
    $this->get(route('contact'))->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
});

test('legacy address separators become lines without splitting Sud-Kivu', function () {
    expect(ContactAddresses::text('22, Kinshasa - 305, Bukavu, Sud-Kivu - 48, Uvira, Sud-Kivu'))
        ->toBe("22, Kinshasa\n305, Bukavu, Sud-Kivu\n48, Uvira, Sud-Kivu");
    expect(ContactAddresses::text("22, Kinshasa\r\n\r\n 48, Uvira "))->toBe("22, Kinshasa\n48, Uvira");
});

test('administrators edit the topbar independently with bullet separators and no newlines', function () {
    $this->withoutVite();
    $data = ['topbar' => "Av Galilee : Kinshasa\nAv Lumumba : Bukavu"];
    $this->actingAs(User::factory()->create())->put(route('admin.cms.home.topbar'), $data)->assertForbidden();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->put(route('admin.cms.home.topbar'), $data)->assertSessionHasNoErrors()->assertRedirect(route('admin.cms.home').'#bandeau-contact');
    expect(DB::table('settings')->where('key', 'adpdh.contact.topbar')->value('value'))->toBe('Av Galilee • Kinshasa Av Lumumba • Bukavu');
    $this->get(route('contact'))->assertOk()->assertSee('Av Galilee • Kinshasa Av Lumumba • Bukavu');
    $this->assertDatabaseMissing('settings', ['key' => 'adpdh.contact.address']);
    $this->put(route('admin.cms.home.topbar'), ['topbar' => ''])->assertSessionHasErrors('topbar');
});
