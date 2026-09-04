<?php

namespace App\View\Components\Layouts;

use App\Models\Setting;
use Illuminate\View\Component;
use Illuminate\View\View;

class Guest extends Component
{
    public $settings;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->settings = Setting::all()->keyBy('key');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('layouts.guest', ['settings' => $this->settings]);
    }
}
