<?php

namespace App\Livewire\Concerns;

use App\Services\MediaLibrary;

trait UsesMediaLibrary
{
    public array $mediaSelections = [];

    protected function mediaChanged(string $field): bool
    {
        return ! empty($this->$field) || array_key_exists($field, $this->mediaSelections);
    }

    protected function mediaPath(string $field): string
    {
        abort_unless(auth()->user()?->is_admin, 403);
        $library = app(MediaLibrary::class);
        if ($this->$field) return $library->legacyPath($library->upload($this->$field, auth()->id()));
        $id = $this->mediaSelections[$field] ?? null;
        return $id ? $library->legacyPath($library->image($id, $field)) : '';
    }

    protected function mediaRule(string $field, bool $required = false): array
    {
        return [($required && empty($this->mediaSelections[$field])) ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
    }
}
