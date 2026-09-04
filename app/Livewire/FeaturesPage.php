<?php

namespace App\Livewire;

use Livewire\Component;

class FeaturesPage extends Component
{
    public function render()
    {
        return view('livewire.features-page')->layout('layouts.guest');
    }
}