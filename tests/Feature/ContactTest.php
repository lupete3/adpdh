<?php

use App\Mail\ContactSubmission;
use App\Services\ContactDelivery;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('contact:'.hash('sha256', '127.0.0.1'));
});

function contactPayload(): array
{
    return ['name' => 'Marie Test', 'email' => 'marie@example.com', 'subject' => 'Une demande', 'message' => "Bonjour, voici ma demande.\nMerci pour votre réponse."];
}

test('contact page exposes a working form and legacy addresses redirect', function () {
    $this->get('/contact')->assertOk()->assertSee('name="_token"', false)->assertSee('name="message"', false)->assertDontSee('contact-demo');
    $this->get('/adpdh/contact.html')->assertRedirect('/contact');
    $this->get('/contact.html')->assertRedirect('/contact');
    expect(adpdh_url('contact.html'))->toBe(route('contact'));
});

test('contact sends all fields to ADPDH with the visitor as reply recipient', function () {
    Mail::fake();
    $this->post('/contact', contactPayload())->assertRedirect('/contact')->assertSessionHas('success')->assertSessionHasNoErrors();
    Mail::assertSent(ContactSubmission::class, function ($mail) {
        $mail->build();
        return $mail->hasTo('contact@adpdh.org') && $mail->hasReplyTo('marie@example.com') && $mail->submission === contactPayload();
    });
    Mail::assertSentCount(1);
    $mail = new ContactSubmission(contactPayload());
    $mail->assertSeeInText('Marie Test');
    $mail->assertSeeInText('marie@example.com');
    $mail->assertSeeInText('Une demande');
    $mail->assertSeeInText('Merci pour votre réponse.');
});

test('invalid or oversized contact fields do not send email', function () {
    Mail::fake();
    $this->from('/contact')->post('/contact', ['name' => '', 'email' => 'invalid', 'subject' => "Injected\r\nBcc: test@example.com", 'message' => str_repeat('x', 5001)])
        ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    Mail::assertNothingSent();
});

test('delivery failure keeps the visitor input and never claims success', function () {
    $this->mock(ContactDelivery::class)->shouldReceive('send')->once()->andThrow(new RuntimeException('SMTP unavailable'));
    $this->post('/contact', contactPayload())->assertRedirect('/contact')->assertSessionHasErrors('delivery')->assertSessionMissing('success')->assertSessionHasInput('message', contactPayload()['message']);
});

test('contact rate limit blocks further email', function () {
    Mail::fake();
    for ($i = 0; $i < 5; $i++) $this->post('/contact', contactPayload())->assertSessionHasNoErrors();
    $this->post('/contact', contactPayload())->assertSessionHasErrors('delivery');
    Mail::assertSentCount(5);
});

test('local mail logging cannot be mistaken for delivery outside testing', function () {
    config(['mail.default' => 'log']);
    app()->instance('env', 'production');
    expect(fn () => app(ContactDelivery::class)->send(contactPayload()))->toThrow(RuntimeException::class);
});
