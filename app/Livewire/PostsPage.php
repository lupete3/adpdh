<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class PostsPage extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $posts = \App\Models\Post::where('status', 'published')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(9);

        return view('livewire.posts-page', [
            'posts' => $posts
        ])->layout('layouts.guest');
    }
}