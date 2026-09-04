<?php

namespace App\Livewire;

use App\Models\Skill;
use Livewire\Component;

class ShowSkills extends Component
{
    public function render()
    {
        $skills = Skill::orderBy('order', 'asc')->orderBy('title', 'asc')->get();
        $skillHeader = \App\Models\SkillHeader::first();
        
        return view('livewire.show-skills', [
            'skills' => $skills,
            'skillHeader' => $skillHeader
        ]);
    }
}
