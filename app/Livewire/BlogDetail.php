<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class BlogDetail extends Component
{
    public Post $post;

    public function mount($id)
    {
        $this->post = Post::where('status', 'published')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.blog-detail')->layout('layouts.guest');
    }
}