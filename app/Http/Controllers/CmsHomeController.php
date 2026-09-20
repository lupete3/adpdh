<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\Indicator;
use App\Models\MediaAsset;
use App\Models\PartnershipType;
use App\Models\Pillar;
use App\Models\Post;
use App\Models\Project;
use App\Models\Testimonial;
use App\Services\Cms\RecordIndicatorValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CmsHomeController extends Controller
{
    private function data(): array
    {
        $page = CmsPage::where('key', 'index')->firstOrFail();

        return ['page' => $page, 'sections' => $page->sections()->with(['media','contents.media','contents.indicator.currentValue'])->orderBy('id')->get(),
            'pillars' => Pillar::with('axes')->orderBy('sort_order')->get(),
            'activities' => Project::publiclyVisible()->with('cover')->orderBy('sort_order')->get(),
            'indicators' => Indicator::with('currentValue')->where('is_visible', true)->orderBy('sort_order')->get(),
            'partnerships' => PartnershipType::where('is_visible', true)->orderBy('sort_order')->get(),
            'news' => Post::publiclyVisible()->with('cover')->latest('published_at')->take(2)->get(),
            'stories' => Testimonial::publiclyVisible()->orderBy('sort_order')->take(1)->get(),
            'settings' => DB::table('settings')->where('key', 'like', 'adpdh.%')->pluck('value', 'key')];
    }

    public function show()
    {
        return view('adpdh.home', $this->data());
    }

    public function edit()
    {
        return app(CmsSectionContentController::class)->index();
    }

    public function updateSection(Request $request, CmsSection $section)
    {
        $isAbout = $request->routeIs('admin.cms.about.section.update');
        $isWork = $request->routeIs('admin.cms.work.section.update');
        abort_unless($section->page->key === ($isWork ? 'que-faisons-nous' : ($isAbout ? 'qui-sommes-nous' : 'index')), 404);
        $data = $request->validate(['version' => 'required|integer', 'eyebrow' => 'nullable|string|max:500', 'title' => 'nullable|string|max:500', 'title_accent' => 'nullable|string|max:500', 'introduction' => 'nullable|string|max:4000', 'body_text' => 'nullable|string|max:20000', 'is_visible' => 'required|boolean', 'sort_order' => 'required|integer|min:0|max:1000', 'media_asset_id' => 'nullable|integer|exists:media_assets,id', 'buttons' => 'nullable|array|max:2', 'buttons.*.label' => 'nullable|string|max:200', 'buttons.*.url' => ['nullable', 'string', 'max:500', function ($a, $v, $fail) {
            if (! preg_match('~^(?:#[a-zA-Z][a-zA-Z0-9_-]*|/(?!/)[a-zA-Z0-9/_\\-.#?=&%]*|[a-z0-9-]+\\.html(?:#[a-zA-Z0-9_-]+)?)$~D', $v)) {
                $fail('Utilisez un lien vers une page du site ou une ancre.');
            }
        }]]);
        if ($isAbout || $isWork) {
            $data += $request->validate([
                'image_caption' => 'nullable|string|max:1000',
                'image_note_title' => 'nullable|string|max:255',
                'image_note_text' => 'nullable|string|max:500',
            ]);
        }
        if (! empty($data['media_asset_id'])) app(\App\Services\MediaLibrary::class)->image($data['media_asset_id'], 'media_asset_id');
        DB::transaction(function () use ($section, $data, $request) {
            $locked = CmsSection::whereKey($section->id)->lockForUpdate()->firstOrFail();
            if ($locked->version != (int) $data['version']) {
                throw ValidationException::withMessages(['version' => 'Cette section a changé. Rechargez la page avant de réessayer.']);
            }
            $locked->revisions()->create(['snapshot' => $locked->toArray(), 'reason' => 'Modification depuis la page Accueil', 'user_id' => $request->user()->id]);
            $body = $locked->body;
            if (array_key_exists('body_text', $data)) {
                $body = ['version' => 1, 'blocks' => collect(preg_split('/\R\s*\R/', trim($data['body_text'] ?? '')))->filter()->map(fn ($text) => ['type' => 'paragraph', 'text' => $text])->values()->all()];
            }
            unset($data['version'],$data['body_text']);
            $data['buttons'] = collect($data['buttons'] ?? [])->filter(fn ($b) => ! empty($b['label']) && ! empty($b['url']))->values()->all();
            $locked->fill($data);
            $locked->body = $body;
            $locked->version++;
            $locked->save();
        });

        return redirect()->to(route($isWork ? 'admin.cms.work.section' : ($isAbout ? 'admin.cms.about.section' : 'admin.cms.sections.edit'), $section).'?tab=titles')->with('status', 'Présentation enregistrée.');
    }

    public function updateSeo(Request $request)
    {
        $data = $request->validate(['version' => 'required|integer', 'seo_title' => 'required|string|max:255', 'seo_description' => 'required|string|max:500']);
        DB::transaction(function () use ($data) {
            $page = CmsPage::where('key', 'index')->lockForUpdate()->firstOrFail();
            if ($page->version != (int) $data['version']) {
                throw ValidationException::withMessages(['version' => 'La page a changé. Rechargez-la.']);
            }$page->update(collect($data)->except('version')->all());
            $page->increment('version');
        });

        return back()->with('status', 'Présentation de la page enregistrée.');
    }

    public function indicator(Request $request, Indicator $indicator, RecordIndicatorValue $recorder)
    {
        $recorder->handle($indicator, $request->all(), $request->user()->id);

        return back()->with('status', 'Nouveau relevé enregistré.');
    }

    public function collection(Request $request, string $kind, int $id)
    {
        return redirect()->route('admin.cms.home')->with('status','Les éléments de l’accueil se gèrent désormais dans les données de chaque section.');
    }

    public function contact(Request $request)
    {
        $data = $request->validate(['email' => 'required|email|max:255', 'phone' => ['required', 'string', 'max:50', 'regex:/^\+?[0-9 ()-]+$/'], 'address' => 'required|string|max:2000']);
        DB::transaction(function () use ($data) {
            foreach ($data as $key => $value) {
                DB::table('settings')->where('key', 'adpdh.contact.'.$key)->update(['value' => $value, 'updated_at' => now()]);
            }
        });

        return back()->with('status','Coordonnées enregistrées.');
    }
}
