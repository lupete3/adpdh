<?php

namespace App\Livewire;

use App\Models\ContactMessage;
use Livewire\Component;
use Livewire\WithPagination;

class ContactMessagesManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function delete(ContactMessage $message)
    {
        $message->delete();
        session()->flash('success', 'Message supprimé avec succès.');
    }

    public function render()
    {
        $messages = ContactMessage::latest()->paginate(10);
        return view('livewire.contact-messages-manager', [
            'messages' => $messages,
        ]);
    }
}
