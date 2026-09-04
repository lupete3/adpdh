<?php

namespace App\Livewire;

use App\Models\Faq;
use Livewire\Component;

class ShowFaqs extends Component
{
    public function render()
    {
        $faqs = Faq::orderBy('order', 'asc')->get();
        $header = \App\Models\SectionHeader::where('section_key', 'faq')->first();
        return view('livewire.show-faqs', [
            'faqs' => $faqs,
            'header' => $header
        ]);
    }
}
