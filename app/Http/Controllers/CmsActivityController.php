<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\Gallery;
use App\Models\MediaAsset;
use App\Models\Project;
use App\Support\ActivityHtml;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CmsActivityController extends Controller
{
    public static function revision(Project $activity): string
    {
        return hash('sha256', json_encode([
            $activity->getAttributes(),
            $activity->gallery?->items()->get()->map(fn ($item) => $item->getAttributes())->all(),
        ]));
    }

    private function context(): array
    {
        return [
            'settings' => DB::table('settings')->where('key', 'like', 'adpdh.%')->pluck('value', 'key'),
            'footerSections' => CmsPage::where('key', 'index')->first()?->sections()->with('contents')->get() ?? collect(),
        ];
    }

    public function index()
    {
        return view('adpdh.activities', $this->context() + ['titles' => CmsPage::where('key', 'activites')->first()?->titles()->pluck('value', 'key') ?? collect(), 'activities' => Project::publiclyVisible()->with('cover')->orderByRaw('COALESCE(published_at, created_at) DESC')->orderByDesc('id')->paginate(6)]);
    }

    public function show(string $slug)
    {
        $activity = Project::publiclyVisible()->where('slug', $slug)->with(['cover', 'gallery.items.media', 'steps'])->firstOrFail();

        return view('adpdh.activity', $this->context() + ['activity' => $activity]);
    }

    public function legacy(string $key)
    {
        $activity = Project::publiclyVisible()->where('cms_key', $key)->firstOrFail();

        return redirect()->route('activities.show', $activity->slug, 301);
    }

    public function manage()
    {
        return view('cms.activities.index', ['page' => CmsPage::where('key', 'activites')->first(), 'activities' => Project::forCms()->with('gallery.items')->latest('id')->paginate(20)]);
    }

    public function edit(?Project $activity = null)
    {
        if ($activity) {
            abort_unless($activity->cms_key, 404);
        }
        $activity = $activity?->load('gallery.items.media', 'cover', 'steps') ?? new Project(['publication_state' => 'draft', 'activity_status' => 'ongoing', 'published_at' => now()]);
        $editorContent = $activity->content ?? '';
        if (! $editorContent && $activity->exists) {
            foreach (['objective' => 'Notre objectif', 'audience' => 'Les personnes accompagnées', 'results_note' => 'Résultats'] as $field => $label) {
                if ($activity->$field) {
                    $editorContent .= '<h2>'.e($label).'</h2><p>'.nl2br(e($activity->$field)).'</p>';
                }
            }
            foreach ($activity->body['blocks'] ?? [] as $block) {
                $editorContent .= '<p>'.e($block['text'] ?? '').'</p>';
            }
            foreach ($activity->steps as $step) {
                $editorContent .= '<h2>'.e($step->title).'</h2><p>'.nl2br(e($step->description)).'</p>';
            }
        }

        return view('cms.activities.edit', compact('activity', 'editorContent'));
    }

    public function destroy(Request $request, Project $activity)
    {
        abort_unless($activity->cms_key, 404);
        $data = $request->validate(['revision' => 'required|string']);
        DB::transaction(function () use ($activity, $data) {
            $record = Project::lockForUpdate()->findOrFail($activity->id);
            if (! hash_equals(self::revision($record), $data['revision'])) {
                throw ValidationException::withMessages(['revision' => 'Ce contenu a changé. Rechargez la page avant de le supprimer.']);
            }
            // Media and galleries can be reused elsewhere; preserve their files.
            $record->delete();
        });

        return redirect()->route('admin.cms.activities')->with('status', 'Activité supprimée.');
    }

    public function save(Request $request, ?Project $activity = null)
    {
        if ($activity) {
            abort_unless($activity->cms_key, 404);
        }
        $data = $request->validate([
            'revision' => $activity ? 'required|string' : 'nullable|string',
            'title' => 'required|string|max:255', 'description' => 'required|string|max:2000',
            'content' => 'nullable|string|max:100000', 'location' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255', 'period_label' => 'nullable|string|max:255',
            'activity_status' => 'required|in:ongoing,completed,planned',
            'publication_state' => 'required|in:draft,published', 'published_at' => 'required|date',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_cover' => 'nullable|boolean', 'photos' => 'nullable|array|max:12',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_images' => 'nullable|array', 'remove_images.*' => 'integer',
            'captions' => 'nullable|array', 'captions.*' => 'nullable|string|max:500',
        ]);
        $library = app(\App\Services\MediaLibrary::class);
        $coverId = $library->selection($request, 'cover_media_id', 'cover', $activity?->cover_media_id);
        if ($request->boolean('remove_cover') && ! $request->hasFile('cover')) $coverId = null;
        $request->validate(['photo_ids' => 'nullable|array|max:12', 'photo_ids.*' => 'integer']);
        $galleryIds = [];
        foreach ($request->input('photo_ids', []) as $id) $galleryIds[] = $library->image($id, 'photo_ids')->id;
        foreach ($request->file('photos', []) as $file) $galleryIds[] = $library->upload($file, $request->user()->id)->id;
        $galleryIds = array_values(array_unique($galleryIds));
        $paths = [];
        try {
            $record = DB::transaction(function () use ($request, $activity, $data, &$paths, $coverId, $galleryIds) {
                $record = $activity ? Project::lockForUpdate()->findOrFail($activity->id) : new Project(['cms_key' => 'activite-'.Str::uuid(), 'slug' => (Str::slug($data['title']) ?: 'activite').'-'.Str::lower(Str::random(8))]);
                if ($activity && ! hash_equals(self::revision($record), $data['revision'])) {
                    throw ValidationException::withMessages(['revision' => 'Cette activité a changé. Rechargez sa fiche avant de réessayer.']);
                }
                $record->fill(collect($data)->only(['title', 'description', 'location', 'category', 'period_label', 'activity_status', 'publication_state', 'published_at'])->all());
                $record->content = ActivityHtml::clean($data['content'] ?? '');
                $record->cover_media_id = $coverId;
                // Each activity owns its gallery; reject foreign IDs rather than editing another article.
                $items = $record->gallery?->items ?? collect();
                $submittedIds = array_merge($data['remove_images'] ?? [], array_keys($data['captions'] ?? []));
                foreach ($submittedIds as $id) {
                    if (! $items->contains('id', (int) $id)) {
                        throw ValidationException::withMessages(['photos' => 'Une image ne correspond pas à cette activité.']);
                    }
                }
                if ($record->gallery_id && Project::where('gallery_id', $record->gallery_id)->where('id', '!=', $record->id)->exists()) {
                    throw ValidationException::withMessages(['photos' => 'Cette galerie est partagée. Créez une galerie propre à cette activité avant de la modifier.']);
                }
                if ($galleryIds && ! $record->gallery_id) {
                    $record->gallery_id = Gallery::create(['key' => 'activity-'.Str::uuid(), 'title' => $record->title])->id;
                }
                $record->save();
                if ($record->gallery_id) {
                    $gallery = Gallery::findOrFail($record->gallery_id);
                    $gallery->items()->whereIn('id', $data['remove_images'] ?? [])->delete();
                    foreach ($data['captions'] ?? [] as $id => $caption) {
                        $gallery->items()->whereKey($id)->update(['caption' => $caption]);
                    }
                    $order = ($gallery->items()->max('sort_order') ?? 0) + 1;
                    foreach ($galleryIds as $id) {
                        $gallery->items()->firstOrCreate(['media_asset_id' => $id], ['alt' => $record->title, 'sort_order' => $order++]);
                    }
                }

                return $record;
            });
        } catch (\Throwable $exception) {
            foreach ($paths as $path) {
                Storage::disk('public')->delete($path);
            }
            throw $exception;
        }

        return redirect()->route('admin.cms.activities.edit', $record)->with('status', 'Activité enregistrée.');
    }
}
