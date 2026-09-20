<?php

namespace App\Services;

use App\Models\MailSetting;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use RuntimeException;

class ContactSmtp
{
    public function configuration(MailSetting $settings): array
    {
        return [
            'transport' => 'smtp',
            'scheme' => $settings->encryption === 'ssl' ? 'smtps' : 'smtp',
            'host' => $settings->host,
            'port' => $settings->port,
            'username' => $settings->username,
            'password' => $settings->password,
            'timeout' => 15,
            'from' => ['address' => $settings->from_address, 'name' => $settings->from_name],
        ];
    }

    public function mailer(MailSetting $settings): Mailer
    {
        // A new mailer reads the current settings without altering other site mail.
        $mailer = Mail::build($this->configuration($settings));
        $mailer->alwaysFrom($settings->from_address, $settings->from_name);

        return $mailer;
    }

    public function test(MailSetting $settings): void
    {
        $transport = $this->mailer($settings)->getSymfonyTransport();
        if (! $transport instanceof SmtpTransport) {
            throw new RuntimeException('An SMTP transport is required.');
        }

        try {
            $transport->start();
        } finally {
            $transport->stop();
        }
    }
}
