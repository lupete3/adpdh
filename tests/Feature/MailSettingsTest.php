<?php

use App\Mail\ContactSubmission;
use App\Models\MailSetting;
use App\Models\User;
use App\Services\ContactDelivery;
use App\Services\ContactSmtp;
use Illuminate\Mail\Transport\ArrayTransport;
use Illuminate\Support\Facades\DB;

function smtpSettingsPayload(array $overrides = []): array
{
    return array_merge([
        'host' => 'smtp.hostinger.com', 'port' => 465, 'encryption' => 'ssl',
        'username' => 'contact@adpdh.org', 'password' => ' secret-with-spaces ',
        'from_address' => 'contact@adpdh.org', 'from_name' => 'ADPDH', 'action' => 'save',
    ], $overrides);
}

test('only administrators may read save or test mail settings', function () {
    $this->get('/admin/cms/mail')->assertRedirect('/login');
    $this->put('/admin/cms/mail', smtpSettingsPayload())->assertRedirect('/login');
    $this->actingAs(User::factory()->create());
    $this->get('/admin/cms/mail')->assertForbidden();
    $this->put('/admin/cms/mail', smtpSettingsPayload())->assertForbidden();
    $this->put('/admin/cms/mail', smtpSettingsPayload(['action' => 'test']))->assertForbidden();
    expect(MailSetting::count())->toBe(0);
});

test('admin saves encrypted credentials without showing the password again', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]))->withoutVite();
    $this->get('/admin/cms/mail')->assertOk()->assertSee('Tester la connexion')->assertSee('smtp.hostinger.com');
    $this->put('/admin/cms/mail', smtpSettingsPayload())->assertRedirect('/admin/cms/mail')->assertSessionHasNoErrors();
    $settings = MailSetting::findOrFail(1);
    expect($settings->password)->toBe(' secret-with-spaces ')
        ->and(DB::table('mail_settings')->value('password'))->not->toBe(' secret-with-spaces ')
        ->and($settings->toArray())->not->toHaveKey('password');
    $this->get('/admin/cms/mail')->assertOk()->assertDontSee('secret-with-spaces')->assertSee('Laissez ce champ vide');
    $this->assertDatabaseCount('settings', 0);
});

test('blank password preserves credentials and a new password replaces them', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->put('/admin/cms/mail', smtpSettingsPayload());
    $this->put('/admin/cms/mail', smtpSettingsPayload(['host' => 'smtp.example.com', 'password' => '']))->assertSessionHasNoErrors();
    expect(MailSetting::find(1)->password)->toBe(' secret-with-spaces ');
    expect(MailSetting::find(1)->host)->toBe('smtp.example.com');
    $this->put('/admin/cms/mail', smtpSettingsPayload(['password' => 'new-secret']))->assertSessionHasNoErrors();
    expect(MailSetting::find(1)->password)->toBe('new-secret')->and(MailSetting::count())->toBe(1);
});

test('invalid settings never flash the password or write configuration', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->from('/admin/cms/mail')->put('/admin/cms/mail', smtpSettingsPayload(['host' => 'https://bad/path', 'port' => 99999, 'encryption' => 'none', 'from_address' => 'invalid']))
        ->assertSessionHasErrors(['host', 'port', 'encryption', 'from_address'])->assertSessionMissing('_old_input.password');
    expect(MailSetting::count())->toBe(0);
    $this->put('/admin/cms/mail', smtpSettingsPayload(['password' => '']))->assertSessionHasErrors('password');
});

test('connection test uses unsaved values without saving or sending mail', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->mock(ContactSmtp::class)->shouldReceive('test')->once()->withArgs(fn (MailSetting $settings) => $settings->host === 'smtp.example.com' && $settings->password === ' secret-with-spaces ');
    $this->from('/admin/cms/mail')->put('/admin/cms/mail', smtpSettingsPayload(['host' => 'smtp.example.com', 'action' => 'test']))
        ->assertRedirect('/admin/cms/mail')->assertSessionHas('status')->assertSessionHasNoErrors()->assertSessionMissing('_old_input.password');
    expect(MailSetting::count())->toBe(0);
});

test('connection failure is redacted and does not overwrite saved settings', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->put('/admin/cms/mail', smtpSettingsPayload());
    $this->mock(ContactSmtp::class)->shouldReceive('test')->once()->withArgs(fn (MailSetting $settings) => $settings->password === ' secret-with-spaces ')->andThrow(new RuntimeException('Password leaked: secret-with-spaces'));
    $this->from('/admin/cms/mail')->put('/admin/cms/mail', smtpSettingsPayload(['host' => 'smtp.example.com', 'password' => '', 'action' => 'test']))
        ->assertSessionHasErrors('connection')->assertSessionMissing('_old_input.password');
    expect(session('errors')->first('connection'))->not->toContain('secret-with-spaces');
    expect(MailSetting::find(1)->host)->toBe('smtp.hostinger.com');
});

test('configured contact mail uses the saved sender and latest credentials', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->put('/admin/cms/mail', smtpSettingsPayload(['from_address' => 'sender@example.com']));
    $settings = MailSetting::find(1);
    $smtp = new ContactSmtp;
    $mailer = $smtp->mailer($settings);
    $transport = new ArrayTransport;
    $mailer->setSymfonyTransport($transport);
    $this->mock(ContactSmtp::class)->shouldReceive('mailer')->once()->withArgs(fn ($current) => $current->from_address === 'sender@example.com')->andReturn($mailer);
    app(ContactDelivery::class)->send(['name' => 'Marie', 'email' => 'marie@example.com', 'subject' => 'Demande', 'message' => 'Bonjour ADPDH']);
    $message = $transport->messages()->first()->getOriginalMessage();
    expect($message->getFrom()[0]->getAddress())->toBe('sender@example.com')
        ->and($message->getTo()[0]->getAddress())->toBe('contact@adpdh.org')
        ->and($message->getReplyTo()[0]->getAddress())->toBe('marie@example.com');
    $this->put('/admin/cms/mail', smtpSettingsPayload(['password' => 'replacement', 'port' => 587, 'encryption' => 'tls']));
    $configuration = $smtp->configuration(MailSetting::find(1));
    expect($configuration['password'])->toBe('replacement')->and($configuration['scheme'])->toBe('smtp')->and($configuration['port'])->toBe(587);
});

test('connection test starts and closes the smtp session without sending a message', function () {
    $transport = Mockery::mock(\Symfony\Component\Mailer\Transport\Smtp\SmtpTransport::class);
    $transport->shouldReceive('start')->once();
    $transport->shouldReceive('stop')->once();
    $transport->shouldNotReceive('send');
    $mailer = Mockery::mock(\Illuminate\Mail\Mailer::class);
    $mailer->shouldReceive('getSymfonyTransport')->once()->andReturn($transport);
    $smtp = Mockery::mock(ContactSmtp::class)->makePartial();
    $smtp->shouldReceive('mailer')->once()->andReturn($mailer);
    $smtp->test(new MailSetting);
});
