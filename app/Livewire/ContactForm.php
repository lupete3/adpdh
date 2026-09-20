<?php

namespace App\Livewire;

use App\Services\ContactDelivery;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ContactForm extends Component
{
    public $name;
    public $email;
    public $subject;
    public $message;

    protected $rules = ContactDelivery::RULES;

    public function save()
    {
        $key = 'contact:'.hash('sha256', (string) request()->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('delivery', 'Trop de tentatives. Veuillez patienter quelques minutes.');
            return;
        }
        RateLimiter::hit($key, 600);
        $data = $this->validate();
        try {
            app(ContactDelivery::class)->send($data);
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Log::error('Contact email delivery failed.', ['exception_type' => get_class($exception)]);
            $this->addError('delivery', 'Envoi impossible. Réessayez plus tard ou écrivez à contact@adpdh.org.');
            return;
        }

        session()->flash('success', 'Votre message a été transmis à notre service de messagerie.');

        $this->reset();
    }

    public function render()
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
        $header = \App\Models\SectionHeader::where('section_key', 'contact')->first();
        return view('livewire.contact-form', [
            'settings' => $settings,
            'header' => $header
        ]);
    }
}
