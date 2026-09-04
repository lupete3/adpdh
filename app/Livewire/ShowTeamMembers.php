<?php

namespace App\Livewire;

use App\Models\TeamMember;
use Livewire\Component;

class ShowTeamMembers extends Component
{
    public function render()
    {
        $teamMembers = TeamMember::all();
        $header = \App\Models\SectionHeader::where('section_key', 'team')->first();
        return view('livewire.show-team-members', [
            'teamMembers' => $teamMembers,
            'header' => $header
        ]);
    }
}
