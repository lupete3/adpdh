<?php

if (!function_exists('adpdh_url')) {
    function adpdh_url(string $path): string
    {
        if (preg_match('~^/?(?:adpdh/)?devenir-partenaire(?:\.html)?([?#].*)?$~D', $path, $match)) return route('partnership').($match[1] ?? '');
        if (preg_match('~^/?(?:adpdh/)?devenir-partenaire(?:\.html)?([?#].*)?$~D', $path, $match)) return route('partnership').($match[1] ?? '');
        if (preg_match('~^/?(?:adpdh/)?impact(?:\.html)?([?#].*)?$~D', $path, $match)) return route('impact').($match[1] ?? '');
        if (preg_match('~^/?(?:adpdh/)?temoignages(?:\.html)?([?#].*)?$~D', $path, $match)) return route('success').($match[1] ?? '');
        if (preg_match('~^/?(?:adpdh/)?activites(?:\.html)?([?#].*)?$~D', $path, $match)) return route('activities').($match[1] ?? '');
        if (preg_match('~^/?(?:adpdh/)?activite-([a-z0-9-]+)\.html([?#].*)?$~D', $path, $match)) {
            $activity = \App\Models\Project::forCms()->where('cms_key', $match[1])->first();
            if ($activity) return route('activities.show', $activity->slug).($match[2] ?? '');
        }
        if (preg_match('~^/?(?:adpdh/)?que-faisons-nous(?:\.html)?([?#].*)?$~D', $path, $match)) {
            return route('work').($match[1] ?? '');
        }
        if (preg_match('~^(?:/?(?:adpdh/)?)?qui-sommes-nous(?:\.html)?([?#].*)?$~D', $path, $match)) {
            return route('organization').($match[1] ?? '');
        }
        if (preg_match('~^[a-z0-9-]+\.html(?:[?#].*)?$~D', $path)) return asset('adpdh/'.$path);
        return $path;
    }
}

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
