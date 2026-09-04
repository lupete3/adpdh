<?php

namespace App\Livewire;

use App\Models\Service;
use Livewire\Component;

class ShowServices extends Component
{
    public function render()
    {
        $services = \App\Models\Service::orderBy('order')->get();
        $header = \App\Models\ServiceHeader::first();
        return view('livewire.show-services', [
            'services' => $services,
            'header' => $header
        ]);
    }
}
