<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;

class ShowPosts extends Component
{
    public function render()
    {
        $projects = Project::latest('created_at')->get();
        $header = \App\Models\SectionHeader::where('section_key', 'portfolio')->first();
        return view('livewire.show-posts', [
            'projects' => $projects,
            'header' => $header
        ]);
    }
}