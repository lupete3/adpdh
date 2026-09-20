<?php

namespace App\Services;

use App\Mail\ContactSubmission;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class ContactDelivery
{
    public const RULES = [
        'name' => ['required', 'string', 'min:2', 'max:120', 'not_regex:/[\r\n]/'],
        'email' => ['required', 'string', 'email', 'max:254'],
        'subject' => ['required', 'string', 'min:3', 'max:180', 'not_regex:/[\r\n]/'],
        'message' => ['required', 'string', 'min:10', 'max:5000'],
    ];

    public function send(array $data): void
    {
        if ($settings = \App\Models\MailSetting::find(1)) {
            app(ContactSmtp::class)->mailer($settings)->send(new ContactSubmission($data));
            return;
        }

        // Do not report success for a local mail sink that cannot deliver mail.
        $transport = config('mail.mailers.'.config('mail.default').'.transport');
        if (! app()->environment('testing') && ! in_array($transport, ['smtp', 'sendmail', 'ses', 'ses-v2', 'postmark', 'resend', 'mailgun'], true)) {
            throw new RuntimeException('Contact mail delivery requires a real mail transport.');
        }

        Mail::send(new ContactSubmission($data));
    }
}
