<?php

namespace App\Livewire;

use Livewire\Component;

class TeamPage extends Component
{
    public function render()
    {
        return view('livewire.team-page')->layout('layouts.guest');
    }
}