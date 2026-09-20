<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ContactSubmission extends Mailable
{
    public function __construct(public array $submission) {}

    public function build(): static
    {
        return $this->to('contact@adpdh.org')
            ->replyTo($this->submission['email'], $this->submission['name'])
            ->subject('[Contact ADPDH] '.$this->submission['subject'])
            ->text('emails.contact-submission');
    }
}
