<?php

namespace App\Livewire;

use App\Models\Stat;
use Livewire\Component;

class ShowStats extends Component
{
    public function render()
    {
        $stats = Stat::orderBy('order', 'asc')->orderBy('title', 'asc')->get();
        return view('livewire.show-stats', [
            'stats' => $stats
        ]);
    }
}
