<?php

namespace App\Livewire;

use App\Models\ContactMessage;
use Livewire\Component;

class ContactForm extends Component
{
    public $name;
    public $email;
    public $subject;
    public $message;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'subject' => 'required|min:5',
        'message' => 'required|min:10',
    ];

    public function save()
    {
        $this->validate();

        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        session()->flash('success', 'Votre message a été envoyé avec succès!');

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