<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\HistoryEvent;
use App\Models\InterventionZone;
use App\Models\MediaAsset;
use App\Models\OrganizationValue;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CmsAboutController extends Controller
{
    public const COLLECTIONS = [
        'histoire' => HistoryEvent::class,
        'valeurs' => OrganizationValue::class,
        'zones' => InterventionZone::class,
        'equipe' => TeamMember::class,
    ];

    public const LABELS = ['hero' => 'Présentation de l’organisation', 'histoire' => 'Notre histoire', 'vision' => 'Vision', 'mission' => 'Mission', 'valeurs' => 'Nos valeurs', 'statut' => 'Statut juridique', 'zones' => 'Zones d’intervention', 'equipe' => 'Notre équipe'];

    private function page(): CmsPage
    {
        return CmsPage::where('key', 'qui-sommes-nous')->firstOrFail();
    }

    private function query(string $kind)
    {
        abort_unless(isset(self::COLLECTIONS[$kind]), 404);
        $query = (self::COLLECTIONS[$kind])::query();

        return $kind === 'equipe' ? $query->forCms() : $query;
    }

    public static function revision($record): string
    {
        return hash('sha256', json_encode($record->getAttributes()));
    }

    public function show()
    {
        $collections = [];
        foreach (self::COLLECTIONS as $kind => $model) {
            $query = $this->query($kind);
            $query = $kind === 'equipe' ? $query->with('portrait')->where('publication_state', 'published') : $query->where('is_visible', true);
            $collections[$kind] = $query->orderBy('sort_order')->orderBy('id')->get();
        }

        return view('adpdh.about', [
            'page' => $this->page(),
            'sections' => $this->page()->sections()->with(['media', 'contents'])->get()->keyBy('key'),
            'collections' => $collections,
            'settings' => DB::table('settings')->where('key', 'like', 'adpdh.%')->pluck('value', 'key'),
            'footerSections' => CmsPage::where('key', 'index')->firstOrFail()->sections()->with('contents')->get(),
        ]);
    }

    public function index()
    {
        return view('cms.about.index', ['page' => $this->page()->load('sections'), 'labels' => self::LABELS]);
    }

    public function seo(Request $request)
    {
        $data = $request->validate(['version' => 'required|integer', 'seo_title' => 'required|string|max:255', 'seo_description' => 'required|string|max:500']);
        DB::transaction(function () use ($data) {
            $page = CmsPage::where('key', 'qui-sommes-nous')->lockForUpdate()->firstOrFail();
            if ($page->version != $data['version']) {
                throw ValidationException::withMessages(['version' => 'La page a changé. Rechargez-la.']);
            }
            $page->fill(collect($data)->except('version')->all());
            $page->version++;
            $page->save();
        });

        return back()->with('status', 'Présentation de la page enregistrée.');
    }

    public function section(CmsSection $section)
    {
        abort_unless($section->page->key === 'qui-sommes-nous', 404);

        return view('cms.about.section', [
            'section' => $section,
            'label' => self::LABELS[$section->key] ?? $section->label,
            'records' => isset(self::COLLECTIONS[$section->key]) ? $this->query($section->key)->orderBy('sort_order')->orderBy('id')->get() : null,
            'media' => MediaAsset::where('kind', 'image')->get()->filter(fn ($m) => $m->isPubliclyAvailable()),
        ]);
    }

    public function editRecord(string $kind, ?int $id = null)
    {
        $query = $this->query($kind);
        $class = self::COLLECTIONS[$kind];
        $record = $id ? $query->findOrFail($id) : new $class(['sort_order' => ($query->max('sort_order') ?? 0) + 1, 'is_visible' => true, 'show_contacts' => false, 'publication_state' => 'draft', 'zone_status' => 'current']);

        return view('cms.about.record', ['record' => $record, 'kind' => $kind, 'label' => self::LABELS[$kind], 'section' => $this->page()->sections()->where('key', $kind)->firstOrFail(), 'media' => MediaAsset::where('kind', 'image')->get()->filter(fn ($m) => $m->isPubliclyAvailable())]);
    }

    public function saveRecord(Request $request, string $kind, ?int $id = null)
    {
        $this->query($kind);
        $rules = ['revision' => $id ? 'required|string' : 'nullable|string', 'description' => 'nullable|string|max:10000', 'sort_order' => 'required|integer|min:0|max:100000'];
        if ($kind === 'equipe') {
            $rules += ['name' => 'required|string|max:255', 'position' => 'required|string|max:255', 'phone' => ['nullable', 'string', 'max:50', 'regex:/^\+?[0-9 ()-]+$/'], 'email' => 'nullable|email|max:255', 'show_contacts' => 'required|boolean', 'publication_state' => 'required|in:draft,published', 'photo_media_id' => 'nullable|integer|exists:media_assets,id', 'portrait_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120'];
        } else {
            $rules += ['title' => 'required|string|max:255', 'is_visible' => 'required|boolean'];
            $rules['description'] = 'required|string|max:10000';
        }
        if ($kind === 'histoire') {
            $rules['period_label'] = 'required|string|max:255';
        }
        if ($kind === 'zones') {
            $rules['zone_status'] = 'required|in:current,planned';
        }
        $data = $request->validate($rules);
        if (! empty($data['photo_media_id'])) {
            $media = MediaAsset::findOrFail($data['photo_media_id']);
            if ($media->kind !== 'image' || ! $media->isPubliclyAvailable()) {
                throw ValidationException::withMessages(['photo_media_id' => 'Choisissez une image disponible pour le site.']);
            }
        }
        DB::transaction(function () use ($data, $kind, $id, $request) {
            $record = $id ? $this->query($kind)->lockForUpdate()->findOrFail($id) : new (self::COLLECTIONS[$kind]);
            if ($id && ! hash_equals(self::revision($record), $data['revision'])) {
                throw ValidationException::withMessages(['revision' => 'Cet élément a changé. Rechargez sa fiche avant de réessayer.']);
            }
            unset($data['revision'], $data['portrait_upload']);
            if (! $id) {
                $data[$kind === 'equipe' ? 'cms_key' : 'key'] = (string) Str::uuid();
            }
            if ($request->hasFile('portrait_upload')) {
                $file = $request->file('portrait_upload');
                $path = $file->store('team', 'public');
                if (! $path) {
                    throw ValidationException::withMessages(['portrait_upload' => 'Impossible d’enregistrer la photo. Vérifiez les droits du dossier de stockage.']);
                }
                $asset = MediaAsset::create(['name' => $data['name'], 'kind' => 'image', 'disk' => 'public', 'path' => $path, 'visibility' => 'public', 'mime_type' => $file->getMimeType(), 'size' => $file->getSize(), 'alt' => $data['name'], 'publication_allowed' => true, 'uploaded_by' => $request->user()->id]);
                $data['photo_media_id'] = $asset->id;
            }
            $record->fill($data)->save();
        });

        return redirect()->route('admin.cms.about.section', $this->page()->sections()->where('key', $kind)->firstOrFail())->with('status', 'Élément enregistré.');
    }

    public function destroyRecord(Request $request, string $kind, int $id)
    {
        $request->validate(['revision' => 'required|string']);
        DB::transaction(function () use ($request, $kind, $id) {
            $record = $this->query($kind)->lockForUpdate()->findOrFail($id);
            if (! hash_equals(self::revision($record), $request->input('revision'))) {
                throw ValidationException::withMessages(['revision' => 'Cet élément a changé. Rechargez la page.']);
            }
            $record->delete();
        });

        return back()->with('status', 'Élément supprimé.');
    }
}
