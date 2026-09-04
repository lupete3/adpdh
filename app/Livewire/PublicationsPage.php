<?php

namespace App\Livewire;

use Livewire\Component;

class PublicationsPage extends Component
{
    public string $category = '';
    public string $search = '';

    public function mount($category = '')
    {
        $this->category = $category;
    }

    public function render()
    {
        $query = \App\Models\Publication::query();

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->search) {
            $query->where(function ($sub) {
                $sub->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $publications = $query->latest()->get();

        return view('livewire.publications-page', [
            'publications' => $publications,
            'category'     => $this->category,
        ])->layout('layouts.guest');
    }
}
