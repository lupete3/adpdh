<?php

namespace App\Livewire;

use App\Models\ContactMessage;
use Livewire\Attributes\On;
use Livewire\Component;

class ShowContactMessage extends Component
{
    public ContactMessage $message;

    public function mount(ContactMessage $message)
    {
        $this->message = $message;
    }

    #[On('delete-message')]
    public function delete($messageId)
    {
        $message = ContactMessage::findOrFail($messageId);
        $message->delete();
        session()->flash('success', 'Message supprimé avec succès.');
        // Redirect is handled by the frontend after the call
    }

    public function render()
    {
        return view('livewire.show-contact-message');
    }
}
