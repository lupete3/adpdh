<?php

namespace App\Livewire;

use App\Models\Cta;
use App\Models\Stat;
use Livewire\Component;

class CallToAction extends Component
{
    public function render()
    {
        $cta = Cta::first();
        $stats = Stat::orderBy('order')->take(4)->get();
        
        return view('livewire.call-to-action', [
            'cta' => $cta,
            'stats' => $stats
        ]);
    }
}
