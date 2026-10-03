<?php

use App\Models\User;
use App\Services\{ContactPhpMail, ContactSmtp};
use Illuminate\Support\Facades\{DB, RateLimiter};

beforeEach(function () {
    $this->withoutVite();
    RateLimiter::clear('contact:'.hash('sha256', '127.0.0.1'));
});

test('only administrators can activate or test PHP mail without SMTP credentials', function () {
    foreach (['php_mail_save', 'php_mail_test'] as $action) {
        $this->put(route('admin.cms.mail.update'), compact('action'))->assertRedirect('/login');
    }
    $this->actingAs(User::factory()->create());
    foreach (['php_mail_save', 'php_mail_test'] as $action) $this->put(route('admin.cms.mail.update'), compact('action'))->assertForbidden();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->put(route('admin.cms.mail.update'), ['action' => 'php_mail_save'])->assertSessionHasNoErrors();
    expect(DB::table('settings')->where('key', 'adpdh.contact.mail_transport')->value('value'))->toBe('php_mail');
    $this->get(route('admin.cms.mail'))->assertOk()->assertSee('Mail PHP de l’hébergement');
});

test('PHP mode submits all contact fields with UTF8 headers and a fixed envelope sender', function () {
    DB::table('settings')->insert(['key' => 'adpdh.contact.mail_transport', 'value' => 'php_mail']);
    $php = Mockery::mock(ContactPhpMail::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $php->shouldReceive('submit')->once()->withArgs(function ($subject, $body, $headers) {
        expect(mb_decode_mimeheader($subject))->toBe('[Contact ADPDH] Demande de coopération');
        $decoded = quoted_printable_decode($body);
        expect($decoded)->toContain('Marie Test', 'marie@example.com', 'Demande de coopération', "Bonjour, voici ma demande.\nMerci.");
        expect($headers['From'])->toBe('ADPDH <contact@adpdh.org>');
        expect($headers['Reply-To'])->toBe('marie@example.com');
        expect($headers['Content-Type'])->toBe('text/plain; charset=UTF-8');
        expect($headers['Content-Transfer-Encoding'])->toBe('quoted-printable');
        return true;
    })->andReturn(true);
    $this->app->instance(ContactPhpMail::class, $php);
    $this->mock(ContactSmtp::class)->shouldNotReceive('mailer');
    $this->post(route('contact.send'), ['name' => 'Marie Test', 'email' => 'marie@example.com', 'subject' => 'Demande de coopération', 'message' => "Bonjour, voici ma demande.\nMerci."])
        ->assertSessionHasNoErrors()->assertSessionHas('success');
});

test('PHP transport refusal preserves contact input and does not claim success', function () {
    DB::table('settings')->insert(['key' => 'adpdh.contact.mail_transport', 'value' => 'php_mail']);
    $php = Mockery::mock(ContactPhpMail::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $php->shouldReceive('submit')->once()->andReturn(false);
    $this->app->instance(ContactPhpMail::class, $php);
    $this->post(route('contact.send'), ['name' => 'Marie Test', 'email' => 'marie@example.com', 'subject' => 'Bonjour', 'message' => 'Un message à conserver'])
        ->assertSessionHasErrors('delivery')->assertSessionMissing('success')->assertSessionHasInput('message', 'Un message à conserver');
});

test('PHP test sends through the same transport without activating it', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $php = Mockery::mock(ContactPhpMail::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $php->shouldReceive('submit')->once()->withArgs(fn ($subject, $body, $headers) => $headers['Reply-To'] === ContactPhpMail::ADDRESS && str_contains(quoted_printable_decode($body), 'Administration ADPDH'))->andReturn(true);
    $this->app->instance(ContactPhpMail::class, $php);
    $this->put(route('admin.cms.mail.update'), ['action' => 'php_mail_test'])->assertSessionHasNoErrors()->assertSessionHas('status');
    $this->assertDatabaseMissing('settings', ['key' => 'adpdh.contact.mail_transport']);
});

test('PHP test failure is explicit and does not change the transport', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->mock(ContactPhpMail::class)->shouldReceive('test')->once()->andThrow(new RuntimeException('Server failure'));
    $this->put(route('admin.cms.mail.update'), ['action' => 'php_mail_test'])->assertSessionHasErrors('connection')->assertSessionMissing('status');
    $this->assertDatabaseMissing('settings', ['key' => 'adpdh.contact.mail_transport']);
});

test('native mail rejects header injection before calling the transport', function () {
    $php = Mockery::mock(ContactPhpMail::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $php->shouldNotReceive('submit');
    expect(fn () => $php->send(['name' => "Marie\0Test", 'email' => 'marie@example.com', 'subject' => 'Bonjour', 'message' => 'Bonjour à votre équipe']))->toThrow(RuntimeException::class);
});

test('PHP mail test identifies transport failures without exposing internal error details', function ($code) {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->mock(ContactPhpMail::class)->shouldReceive('test')->once()->andThrow(new \App\Exceptions\ContactMailException($code));
    $this->put(route('admin.cms.mail.update'), ['action' => 'php_mail_test'])->assertSessionHasErrors('connection');
    expect(session('errors')->first('connection'))->toContain('['.$code.']');
})->with(['PHP_MAIL_UNAVAILABLE', 'PHP_MBSTRING_UNAVAILABLE', 'PHP_MAIL_REFUSED', 'PHP_MAIL_WARNING', 'PHP_MAIL_EXCEPTION']);

test('application errors are not misreported as hosting refusals', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->mock(ContactPhpMail::class)->shouldReceive('test')->once()->andThrow(new Error('Private server details'));
    $this->put(route('admin.cms.mail.update'), ['action' => 'php_mail_test'])->assertSessionHasErrors('connection');
    expect(session('errors')->first('connection'))->toContain('[PHP_MAIL_APPLICATION_ERROR]')->not->toContain('Private server details');
});

test('native warning detail reaches only the administrator test and handler is restored', function () {
    $php = Mockery::mock(ContactPhpMail::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $php->shouldReceive('invokeMail')->twice()->andReturnUsing(function () {
        trigger_error('mail(): Test transport warning', E_USER_WARNING);
        return false;
    });
    $this->app->instance(ContactPhpMail::class, $php);
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->put(route('admin.cms.mail.update'), ['action' => 'php_mail_test'])->assertSessionHasErrors('connection');
    expect(session('errors')->first('connection'))->toContain('Détail PHP : mail(): Test transport warning');
    session()->forget('errors');
    DB::table('settings')->insert(['key' => 'adpdh.contact.mail_transport', 'value' => 'php_mail']);
    $this->post(route('contact.send'), ['name' => 'Marie Test', 'email' => 'marie@example.com', 'subject' => 'Bonjour', 'message' => 'Un message à conserver'])->assertSessionHasErrors('delivery');
    expect(session('errors')->first('delivery'))->not->toContain('Test transport warning');
});
