<?php

namespace App\Livewire;

use Livewire\Component;

class CareersPage extends Component
{
    public function render()
    {
        $jobs = \App\Models\JobOpening::where('is_active', true)->latest()->get();

        return view('livewire.careers-page', [
            'jobs' => $jobs
        ])->layout('layouts.guest');
    }
}
