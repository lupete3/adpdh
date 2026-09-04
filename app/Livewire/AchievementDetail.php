<?php

namespace App\Livewire;

use App\Models\Achievement;
use Livewire\Component;

class AchievementDetail extends Component
{
    public Achievement $achievement;

    public function mount($id)
    {
        $this->achievement = Achievement::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.achievement-detail')->layout('layouts.guest');
    }
}