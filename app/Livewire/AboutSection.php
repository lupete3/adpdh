<?php

namespace App\Livewire;

use App\Models\About;
use Livewire\Component;

class AboutSection extends Component
{
    public function render()
    {
        $about = About::first();
        return view('livewire.about-section', [
            'about' => $about
        ]);
    }
}