<?php

namespace App\Livewire;

use App\Models\Stat;
use Livewire\Component;

class ShowAchievements extends Component
{
    public function render()
    {
        $stats = Stat::all();
        $header = \App\Models\SectionHeader::where('section_key', 'achievements')->first();
        return view('livewire.show-achievements', [
            'stats' => $stats,
            'header' => $header
        ]);
    }
}