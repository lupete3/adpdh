<?php

namespace App\Livewire;

use Livewire\Component;

class FooterSection extends Component
{
    public $settings;

    public function mount($settings = [])
    {
        $this->settings = $settings;
    }

    public function render()
    {
        return view('livewire.footer-section');
    }
}
