<?php

namespace App\Livewire;

use Livewire\Component;

class AchievementsPage extends Component
{
    public function render()
    {
        return view('livewire.achievements-page')->layout('layouts.guest');
    }
}