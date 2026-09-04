<?php

namespace App\Livewire;

use App\Models\Partner;
use Livewire\Component;

class ShowPartners extends Component
{
    public function render()
    {
        $partners = Partner::all();
        return view('livewire.show-partners', [
            'partners' => $partners
        ]);
    }
}
