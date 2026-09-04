<?php

namespace App\Livewire;

use App\Models\Testimonial;
use Livewire\Component;

class ShowTestimonials extends Component
{
    public function render()
    {
        $testimonials = Testimonial::all();
        return view('livewire.show-testimonials', [
            'testimonials' => $testimonials
        ]);
    }
}
