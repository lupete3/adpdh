<?php

namespace App\Livewire;

use App\Models\Feature;
use Livewire\Component;

class WhyUsSection extends Component
{
    public function render()
    {
        $features = Feature::orderBy('order', 'asc')->orderBy('title', 'asc')->get();
        $whyUs = \App\Models\WhyUs::first();
        
        return view('livewire.why-us-section', [
            'features' => $features,
            'whyUs' => $whyUs
        ]);
    }
}
