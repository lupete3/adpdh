<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use RuntimeException;
use App\Exceptions\ContactMailException;

class ContactPhpMail
{
    public const ADDRESS = 'contact@adpdh.org';

    public function send(array $data): void
    {
        $data = Validator::make($data, ContactDelivery::RULES)->validate();
        foreach (['name', 'email', 'subject'] as $field) {
            if (preg_match('/[\r\n\x00]/', $data[$field])) {
                throw new RuntimeException('Invalid contact header.');
            }
        }
        $body = "Nouveau message depuis adpdh.org\r\n\r\n"
            ."Nom : {$data['name']}\r\nE-mail : {$data['email']}\r\nSujet : {$data['subject']}\r\n\r\nMessage :\r\n{$data['message']}";
        $headers = [
            'From' => 'ADPDH <'.self::ADDRESS.'>',
            'Reply-To' => $data['email'],
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Transfer-Encoding' => 'quoted-printable',
        ];
        if (! function_exists('mb_encode_mimeheader')) {
            throw new ContactMailException('PHP_MBSTRING_UNAVAILABLE');
        }
        if (! $this->submit(mb_encode_mimeheader('[Contact ADPDH] '.$data['subject'], 'UTF-8', 'B', "\r\n"), quoted_printable_encode($body), $headers)) {
            throw new ContactMailException('PHP_MAIL_REFUSED');
        }
    }

    protected function submit(string $subject, string $body, array $headers): bool
    {
        if (! function_exists('mail')) {
            throw new ContactMailException('PHP_MAIL_UNAVAILABLE');
        }
        $warning = null;
        // Keep the warning for the protected admin test, never the public response.
        set_error_handler(function (int $severity, string $message) use (&$warning) {
            $warning = mb_substr(preg_replace('/[\x00-\x1F\x7F]+/', ' ', $message), 0, 1000);
            return true;
        });
        try {
            $accepted = $this->invokeMail($subject, $body, $headers);
        } catch (\Throwable $exception) {
            throw new ContactMailException('PHP_MAIL_EXCEPTION');
        } finally {
            restore_error_handler();
        }
        if (! $accepted && $warning) throw new ContactMailException('PHP_MAIL_WARNING', $warning);

        return $accepted;
    }

    protected function invokeMail(string $subject, string $body, array $headers): bool
    {
        return mail(self::ADDRESS, $subject, $body, $headers, '-f'.self::ADDRESS);
    }

    public function test(): void
    {
        $this->send([
            'name' => 'Administration ADPDH', 'email' => self::ADDRESS,
            'subject' => 'Test de messagerie PHP',
            'message' => 'Ce message confirme la réception du test envoyé depuis l’administration ADPDH avec le service mail de l’hébergement.',
        ]);
    }
}
