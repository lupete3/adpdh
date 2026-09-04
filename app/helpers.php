<?php

if (!function_exists('media_url')) {
    /**
     * Resolve a media path to a public URL.
     *
     * - Paths starting with 'http' or '//' → returned as-is.
     * - Paths starting with 'flexbiz/' or any other public/ sub-path → asset($path).
     * - Everything else → asset('storage/' . $path) for Laravel storage.
     */
    function media_url(?string $path): string
    {
        if (empty($path)) {
            return asset('flexbiz/assets/img/placeholder.webp');
        }

        // Absolute URLs
        if (str_starts_with($path, 'http') || str_starts_with($path, '//')) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        // Public directory assets (FlexBiz template files, etc.)
        if (
            str_starts_with($path, 'flexbiz/') ||
            str_starts_with($path, 'assets/') ||
            str_starts_with($path, 'images/') ||
            str_starts_with($path, 'template/')
        ) {
            return asset($path);
        }

        // Laravel storage (uploaded files)
        return asset('storage/' . $path);
    }
}
