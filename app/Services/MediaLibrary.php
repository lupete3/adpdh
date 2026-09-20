<?php

namespace App\Services;

use App\Models\MediaAsset;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MediaLibrary
{
    public const IMAGE_RULES = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=8000,max_height=8000'];

    public const REFERENCES = [
        'cms_sections' => ['media_asset_id'], 'cms_section_contents' => ['media_asset_id'],
        'gallery_items' => ['media_asset_id'], 'projects' => ['cover_media_id'],
        'posts' => ['cover_media_id'], 'team_members' => ['photo_media_id'],
        'testimonials' => ['photo_media_id', 'consent_media_id'], 'publications' => ['cover_media_id', 'file_media_id'],
    ];

    public const LEGACY_IMAGES = [
        'posts' => ['image'], 'projects' => ['image'], 'services' => ['image'],
        'team_members' => ['photo'], 'testimonials' => ['author_photo'], 'partners' => ['logo'],
        'sliders' => ['image', 'secondary_image'], 'achievements' => ['image'],
        'abouts' => ['image'], 'ctas' => ['image'], 'why_us' => ['intro_image'],
        'publications' => ['thumbnail'], 'gallery_photos' => ['image_path'],
    ];

    public function upload(UploadedFile $file, ?int $userId = null): MediaAsset
    {
        Validator::make(['image' => $file], ['image' => self::IMAGE_RULES])->validate();
        $hash = hash_file('sha256', $file->getRealPath());

        return Cache::lock('media-upload-'.$hash, 30)->block(10, function () use ($file, $userId, $hash) {
            $existing = MediaAsset::where('checksum', $hash)->first();
            if ($existing) {
                if ($existing->kind !== 'image' || $existing->visibility !== 'public' || ! $existing->publication_allowed || $existing->is_demo) {
                    throw ValidationException::withMessages(['image' => 'Cette image existe déjà mais n’est pas autorisée à la diffusion.']);
                }
                if ($existing->isPubliclyAvailable()) return $existing;
                if ($existing->disk === 'public') {
                    $this->safePath($existing->path);
                    if (! Storage::disk('public')->put($existing->path, file_get_contents($file->getRealPath()))) {
                        throw ValidationException::withMessages(['image' => 'Impossible de restaurer le fichier de cette image.']);
                    }
                    return $existing;
                }
            }
            $extension = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$file->getMimeType()];
            $path = 'media/'.$hash.'.'.$extension;
            if (! Storage::disk('public')->put($path, file_get_contents($file->getRealPath()))) {
                throw ValidationException::withMessages(['image' => 'Impossible d’enregistrer l’image. Réessayez.']);
            }
            [$width, $height] = getimagesize($file->getRealPath());

            return MediaAsset::create([
                'key' => 'media-'.Str::uuid(), 'name' => Str::limit($file->getClientOriginalName(), 240, ''),
                'kind' => 'image', 'disk' => 'public', 'path' => $path, 'checksum' => $hash,
                'visibility' => 'public', 'mime_type' => $file->getMimeType(), 'size' => $file->getSize(),
                'width' => $width, 'height' => $height, 'alt' => '', 'publication_allowed' => true,
                'is_demo' => false, 'uploaded_by' => $userId,
            ]);
        });
    }

    public function image($id, string $field = 'image'): MediaAsset
    {
        $asset = filter_var($id, FILTER_VALIDATE_INT) ? MediaAsset::find($id) : null;
        if (! $asset || $asset->kind !== 'image' || ! $asset->isPubliclyAvailable()) {
            throw ValidationException::withMessages([$field => 'Choisissez une image disponible et autorisée dans la médiathèque.']);
        }
        return $asset;
    }

    public function selection(Request $request, string $idField, string $uploadField, ?int $current = null): ?int
    {
        if ($request->hasFile($uploadField)) return $this->upload($request->file($uploadField), $request->user()?->id)->id;
        if ($request->exists($idField)) return $request->filled($idField) ? $this->image($request->input($idField), $idField)->id : null;
        return $current;
    }

    public function legacyPath(MediaAsset $asset): string
    {
        return $asset->path;
    }

    public function safePath(string $path): void
    {
        if (! $path || str_contains($path, '..') || str_contains($path, '\\') || str_contains($path, ':') || str_starts_with($path, '/')) {
            throw ValidationException::withMessages(['image' => 'Chemin de fichier invalide.']);
        }
    }

    public function usages(MediaAsset $asset): array
    {
        $usages = [];
        $labels = ['cms_sections' => 'Section', 'cms_section_contents' => 'Contenu de section', 'gallery_items' => 'Photo de galerie', 'projects' => 'Activité', 'posts' => 'Actualité', 'team_members' => 'Membre de l’équipe', 'testimonials' => 'Témoignage', 'publications' => 'Ressource', 'services' => 'Service', 'partners' => 'Partenaire', 'sliders' => 'Bannière', 'achievements' => 'Réalisation', 'abouts' => 'Présentation', 'ctas' => 'Appel à l’action', 'why_us' => 'Présentation des atouts', 'gallery_photos' => 'Photo de galerie', 'users' => 'Profil utilisateur'];
        foreach (self::REFERENCES as $table => $columns) {
            foreach ($columns as $column) {
                foreach (DB::table($table)->where($column, $asset->id)->pluck('id') as $id) $usages[] = $labels[$table].' n° '.$id;
            }
        }
        $paths = [$asset->path, 'storage/'.$asset->path, '/storage/'.$asset->path, Storage::disk('public')->url($asset->path), asset($asset->path)];
        foreach (self::LEGACY_IMAGES + ['users' => ['photo']] as $table => $columns) {
            foreach ($columns as $column) {
                foreach (DB::table($table)->whereIn($column, $paths)->pluck('id') as $id) $usages[] = $labels[$table].' n° '.$id;
            }
        }
        foreach (DB::table('settings')->whereIn('value', $paths)->pluck('key') as $key) $usages[] = 'Réglage : '.$key;
        // Preserve media needed by the existing content revision history.
        foreach (DB::table('content_revisions')->select('id', 'snapshot')->cursor() as $revision) {
            $snapshot = json_decode($revision->snapshot, true) ?? [];
            foreach (['media_asset_id', 'photo_media_id', 'cover_media_id'] as $column) {
                if (($snapshot[$column] ?? null) == $asset->id) $usages[] = 'Historique #'.$revision->id;
            }
        }
        return array_values(array_unique($usages));
    }

    public function delete(MediaAsset $asset): void
    {
        DB::transaction(function () use ($asset) {
            $asset = MediaAsset::lockForUpdate()->findOrFail($asset->id);
            if ($asset->disk !== 'public' || $asset->kind !== 'image') {
                throw ValidationException::withMessages(['image' => 'Les images intégrées au thème sont conservées. Vous pouvez les retirer des sections.']);
            }
            if ($this->usages($asset)) {
                throw ValidationException::withMessages(['image' => 'Cette image est encore utilisée. Retirez ses associations avant de la supprimer.']);
            }
            $paths = $asset->variants->pluck('path')->push($asset->path)->unique();
            foreach ($paths as $path) {
                $this->safePath($path);
                if (Storage::disk('public')->exists($path) && ! Storage::disk('public')->delete($path)) {
                    throw ValidationException::withMessages(['image' => 'Le fichier ne peut pas être supprimé. Vérifiez les droits du stockage.']);
                }
            }
            $asset->delete();
        });
    }
}
