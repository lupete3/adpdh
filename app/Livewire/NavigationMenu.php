<?php

namespace App\Livewire;

use Livewire\Component;

class NavigationMenu extends Component
{
    public $settings;

    public function mount($settings = [])
    {
        $this->settings = $settings;
    }

    public function render()
    {
        return view('livewire.navigation-menu');
    }
}
