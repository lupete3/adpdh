<?php

namespace App\Livewire;

use App\Models\Slider;
use Livewire\Component;

class ShowSliders extends Component
{
    public function render()
    {
        $sliders = Slider::orderBy('order')->get();
        return view('livewire.show-sliders', [
            'sliders' => $sliders
        ]);
    }
}