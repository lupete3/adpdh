<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GalleryPhoto;

class ShowGallery extends Component
{
    public function render()
    {
        $featured = GalleryPhoto::where('is_featured', true)
                        ->orderBy('order')
                        ->orderByDesc('created_at')
                        ->take(6)
                        ->get();

        if ($featured->isEmpty()) {
            $featured = GalleryPhoto::orderBy('order')
                            ->orderByDesc('created_at')
                            ->take(6)
                            ->get();
        }

        return view('livewire.show-gallery', [
            'photos'     => $featured,
            'totalCount' => GalleryPhoto::count(),
        ]);
    }
}
