<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\MediaAsset;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CmsResourceController extends Controller
{
    public static function revision(Publication $resource): string
    {
        return hash('sha256', json_encode($resource->getAttributes()));
    }

    private function context(): array
    {
        return [
            'settings' => DB::table('settings')->where('key', 'like', 'adpdh.%')->pluck('value', 'key'),
            'footerSections' => CmsPage::where('key', 'index')->first()?->sections()->with('contents')->get() ?? collect(),
        ];
    }

    public function index(Request $request)
    {
        $filters = $request->validate(['q' => 'nullable|string|max:200', 'category' => 'nullable|string|max:255']);
        $query = Publication::publiclyVisible()->with(['file', 'cover']);
        if ($search = trim($filters['q'] ?? '')) {
            $query->where(fn ($q) => $q->where('title', 'like', '%'.$search.'%')->orWhere('description', 'like', '%'.$search.'%'));
        }
        if ($category = $filters['category'] ?? '') {
            $query->where('category', $category);
        }

        return view('adpdh.resources', $this->context() + [
            'resources' => $query->latest('id')->paginate(6)->withQueryString(),
            'categories' => Publication::publiclyVisible()->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    private function published(string $slug): Publication
    {
        $resource = Publication::publiclyVisible()->with(['file', 'cover'])->where('slug', $slug)->firstOrFail();
        abort_unless($resource->hasReadableFile(), 404);

        return $resource;
    }

    public function show(string $slug)
    {
        return view('adpdh.resource', $this->context() + ['resource' => $this->published($slug)]);
    }

    public function read(string $slug)
    {
        $resource = $this->published($slug);

        return response()->file(Storage::disk('local')->path($resource->file->path), [
            'Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline; filename="document.pdf"',
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(string $slug)
    {
        $resource = $this->published($slug);
        abort_unless($resource->canDownload(), 403);

        return response()->download(Storage::disk('local')->path($resource->file->path), (Str::slug($resource->title) ?: 'ressource').'.pdf', [
            'Content-Type' => 'application/pdf', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function manage()
    {
        return view('cms.resources.index', ['resources' => Publication::forCms()->with('file')->latest('id')->paginate(20)]);
    }

    public function edit(?Publication $resource = null)
    {
        abort_if($resource && ! $resource->cms_key, 404);

        return view('cms.resources.edit', ['resource' => $resource ?? new Publication(['publication_state' => 'draft', 'distribution_allowed' => false])]);
    }

    public function save(Request $request, ?Publication $resource = null)
    {
        abort_if($resource && ! $resource->cms_key, 404);
        $data = $request->validate([
            'revision' => $resource ? 'required|string' : 'nullable|string',
            'title' => 'required|string|max:255', 'description' => 'required|string|max:5000', 'category' => 'required|string|max:255',
            'publication_state' => 'required|in:draft,published', 'distribution_allowed' => 'required|boolean',
            'document' => 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:20480',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', 'remove_cover' => 'nullable|boolean',
        ]);
        $library = app(\App\Services\MediaLibrary::class);
        $coverId = $library->selection($request, 'cover_media_id', 'cover', $resource?->cover_media_id);
        if ($request->boolean('remove_cover') && ! $request->hasFile('cover')) $coverId = null;
        $uploads = [];
        try {
            $record = DB::transaction(function () use ($request, $resource, $data, &$uploads, $coverId) {
                $record = $resource ? Publication::lockForUpdate()->findOrFail($resource->id) : new Publication([
                    'cms_key' => 'resource-'.Str::uuid(), 'slug' => (Str::slug($data['title']) ?: 'ressource').'-'.Str::lower(Str::random(8)), 'is_demo' => false,
                ]);
                if ($resource && ! hash_equals(self::revision($record), $data['revision'])) {
                    throw ValidationException::withMessages(['revision' => 'Cette ressource a changé. Rechargez la fiche avant de réessayer.']);
                }
                $record->fill(collect($data)->only(['title', 'description', 'category', 'publication_state', 'distribution_allowed'])->all());
                foreach (['document' => ['local', 'file_media_id', 'document']] as $field => [$disk, $column, $kind]) {
                    if (! $request->hasFile($field)) {
                        continue;
                    }
                    $file = $request->file($field);
                    $path = $file->store('resources', $disk);
                    if (! $path) {
                        throw new \RuntimeException('Impossible d’enregistrer le fichier.');
                    }
                    $uploads[] = [$disk, $path];
                    $record->$column = MediaAsset::create([
                        'key' => 'resource-'.Str::uuid(), 'name' => $file->getClientOriginalName(), 'kind' => $kind,
                        'disk' => $disk, 'path' => $path, 'visibility' => $disk === 'local' ? 'private' : 'public',
                        'mime_type' => $file->getMimeType(), 'size' => $file->getSize(), 'alt' => $record->title,
                        'publication_allowed' => true, 'is_demo' => false, 'uploaded_by' => $request->user()->id,
                    ])->id;
                }
                $record->cover_media_id = $coverId;
                $record->unsetRelation('file');
                $record->availability = $record->hasReadableFile() ? 'available' : 'pending';
                if ($record->publication_state === 'published' && $record->availability !== 'available') {
                    throw ValidationException::withMessages(['document' => 'Ajoutez un document PDF avant de publier cette ressource.']);
                }
                $record->save();

                return $record;
            });
        } catch (\Throwable $exception) {
            foreach ($uploads as [$disk, $path]) {
                Storage::disk($disk)->delete($path);
            }
            throw $exception;
        }

        return redirect()->route('admin.cms.resources.edit', $record)->with('status', 'Ressource enregistrée.');
    }

    public function destroy(Request $request, Publication $resource)
    {
        abort_unless($resource->cms_key, 404);
        $data = $request->validate(['revision' => 'required|string']);
        DB::transaction(function () use ($resource, $data) {
            $record = Publication::lockForUpdate()->findOrFail($resource->id);
            if (! hash_equals(self::revision($record), $data['revision'])) {
                throw ValidationException::withMessages(['revision' => 'Cette ressource a changé. Rechargez la liste avant de la supprimer.']);
            }
            $record->delete();
        });

        return redirect()->route('admin.cms.resources')->with('status', 'Ressource supprimée du catalogue.');
    }
}
