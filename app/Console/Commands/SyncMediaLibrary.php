<?php

namespace App\Console\Commands;

use App\Models\MediaAsset;
use App\Services\MediaLibrary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SyncMediaLibrary extends Command
{
    protected $signature = 'media:sync';
    protected $description = 'Index existing editorial images without copying, moving or deleting their files';

    public function handle(MediaLibrary $library): int
    {
        $added = 0;
        foreach (MediaLibrary::LEGACY_IMAGES as $table => $columns) {
            foreach ($columns as $column) {
                foreach (DB::table($table)->whereNotNull($column)->distinct()->pluck($column) as $path) $added += $this->register($path, $library);
            }
        }
        foreach (DB::table('settings')->whereIn('key', ['logo', 'feature_image'])->pluck('value') as $path) $added += $this->register($path, $library);

        foreach (MediaAsset::where('kind', 'image')->whereNull('checksum')->cursor() as $asset) {
            if (! $asset->isPubliclyAvailable()) continue;
            $path = $asset->disk === 'builtin' ? public_path($asset->path) : Storage::disk('public')->path($asset->path);
            $hash = hash_file('sha256', $path);
            if (! MediaAsset::where('checksum', $hash)->exists()) $asset->update(['checksum' => $hash]);
        }
        $this->info($added.' image(s) référencée(s), sans copie ni suppression de fichier.');
        return self::SUCCESS;
    }

    private function register(?string $path, MediaLibrary $library): int
    {
        if (! $path) return 0;
        if (preg_match('~^https?://~', $path)) {
            if (parse_url($path, PHP_URL_HOST) !== parse_url(config('app.url'), PHP_URL_HOST)) return 0;
            $path = ltrim(parse_url($path, PHP_URL_PATH) ?? '', '/');
        }
        $path = preg_replace('~^/?storage/~', '', $path);
        try { $library->safePath($path); } catch (\Throwable) { return 0; }
        $disk = Storage::disk('public')->exists($path) ? 'public' : 'builtin';
        $file = $disk === 'public' ? Storage::disk('public')->path($path) : public_path($path);
        if (! is_file($file)) return 0;
        $dimensions = @getimagesize($file);
        if (! $dimensions || ! in_array($dimensions['mime'], ['image/jpeg', 'image/png', 'image/webp', 'image/gif'])) return 0;
        $asset = MediaAsset::firstOrCreate(['disk' => $disk, 'path' => $path], [
            'name' => basename($path), 'kind' => 'image', 'visibility' => 'public', 'publication_allowed' => true,
            'mime_type' => $dimensions['mime'], 'size' => filesize($file), 'width' => $dimensions[0], 'height' => $dimensions[1],
        ]);
        return $asset->wasRecentlyCreated ? 1 : 0;
    }
}
