<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\InterventionAxis;
use App\Models\MediaAsset;
use App\Models\Pillar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CmsWorkController extends Controller
{
    private function page(): CmsPage
    {
        return CmsPage::where('key', 'que-faisons-nous')->firstOrFail();
    }

    public static function revision($record): string
    {
        return hash('sha256', json_encode($record->getAttributes()));
    }

    public function show()
    {
        $page = $this->page();
        $pillars = Pillar::where('is_visible', true)->with(['axes' => fn ($q) => $q->where('is_visible', true)])->orderBy('sort_order')->orderBy('id')->get();
        $count = function (int $n, string $singular, string $plural): string {
            $words = ['aucun', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize'];

            return ($words[$n] ?? (string) $n).' '.($n <= 1 ? $singular : $plural);
        };
        $pillarLabel = $count($pillars->count(), 'pilier', 'piliers');
        $axisLabel = $count($pillars->sum(fn ($pillar) => $pillar->axes->count()), 'axe', 'axes');
        $tokens = ['{complementaires}' => $pillars->count() <= 1 ? 'complémentaire' : 'complémentaires', '{piliers}' => $pillarLabel, '{Piliers}' => Str::ucfirst($pillarLabel), '{axes}' => $axisLabel, '{Axes}' => Str::ucfirst($axisLabel)];

        return view('adpdh.work', [
            'page' => $page,
            'sections' => $page->sections()->with('media')->get()->keyBy('key'),
            'pillars' => $pillars,
            'text' => fn (?string $text) => strtr($text ?? '', $tokens),
            'settings' => DB::table('settings')->where('key', 'like', 'adpdh.%')->pluck('value', 'key'),
            'footerSections' => CmsPage::where('key', 'index')->firstOrFail()->sections()->with('contents')->get(),
        ]);
    }

    public function index()
    {
        return view('cms.work.index', ['page' => $this->page()->load('sections'), 'pillars' => Pillar::withCount('axes')->orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function section(CmsSection $section)
    {
        abort_unless($section->page->key === 'que-faisons-nous', 404);

        return view('cms.work.section', ['section' => $section, 'media' => MediaAsset::where('kind', 'image')->get()->filter(fn ($m) => $m->isPubliclyAvailable())]);
    }

    public function seo(Request $request)
    {
        $data = $request->validate(['version' => 'required|integer', 'seo_title' => 'required|string|max:255', 'seo_description' => 'required|string|max:500']);
        DB::transaction(function () use ($data) {
            $page = CmsPage::where('key', 'que-faisons-nous')->lockForUpdate()->firstOrFail();
            if ($page->version != $data['version']) {
                throw ValidationException::withMessages(['version' => 'La page a changé. Rechargez-la.']);
            }
            $page->fill(collect($data)->except('version')->all());
            $page->version++;
            $page->save();
        });

        return back()->with('status', 'Présentation de la page enregistrée.');
    }

    public function editPillar(?Pillar $pillar = null)
    {
        return view('cms.work.record', ['kind' => 'pillar', 'pillar' => $pillar, 'record' => $pillar ?? new Pillar(['is_visible' => true, 'sort_order' => (Pillar::max('sort_order') ?? 0) + 1])]);
    }

    public function editAxis(Pillar $pillar, ?InterventionAxis $axis = null)
    {
        if ($axis) {
            abort_unless($axis->pillar_id === $pillar->id, 404);
        }

        return view('cms.work.record', ['kind' => 'axis', 'pillar' => $pillar, 'record' => $axis ?? new InterventionAxis(['is_visible' => true, 'sort_order' => ($pillar->axes()->max('sort_order') ?? 0) + 1])]);
    }

    private function validated(Request $request, bool $exists): array
    {
        return $request->validate(['revision' => $exists ? 'required|string' : 'nullable|string', 'title' => 'required|string|max:255', 'description' => 'required|string|max:10000', 'sort_order' => 'required|integer|min:0|max:100000', 'is_visible' => 'required|boolean']);
    }

    private function checkRevision($record, ?string $revision): void
    {
        if (! hash_equals(self::revision($record), $revision ?? '')) {
            throw ValidationException::withMessages(['revision' => 'Cet élément a changé. Rechargez sa fiche avant de réessayer.']);
        }
    }

    public function savePillar(Request $request, ?Pillar $pillar = null)
    {
        $data = $this->validated($request, $pillar !== null);
        $id = DB::transaction(function () use ($data, $pillar) {
            $record = $pillar ? Pillar::lockForUpdate()->findOrFail($pillar->id) : new Pillar(['key' => 'pilier-'.Str::uuid()]);
            if ($pillar) {
                $this->checkRevision($record, $data['revision']);
            }
            unset($data['revision']);
            $record->fill($data)->save();

            return $record->id;
        });

        return redirect()->route('admin.cms.work.pillars.edit', $id)->with('status', 'Pilier enregistré. Vous pouvez gérer ses axes ci-dessous.');
    }

    public function saveAxis(Request $request, Pillar $pillar, ?InterventionAxis $axis = null)
    {
        if ($axis) {
            abort_unless($axis->pillar_id === $pillar->id, 404);
        }
        $data = $this->validated($request, $axis !== null);
        DB::transaction(function () use ($data, $pillar, $axis) {
            $parent = Pillar::lockForUpdate()->findOrFail($pillar->id);
            $record = $axis ? $parent->axes()->lockForUpdate()->findOrFail($axis->id) : $parent->axes()->make(['key' => 'axe-'.Str::uuid()]);
            if ($axis) {
                $this->checkRevision($record, $data['revision']);
            }
            unset($data['revision']);
            $record->fill($data)->save();
        });

        return redirect()->route('admin.cms.work.pillars.edit', $pillar)->with('status', 'Axe enregistré.');
    }

    public function destroyPillar(Request $request, Pillar $pillar)
    {
        $request->validate(['revision' => 'required|string']);
        DB::transaction(function () use ($request, $pillar) {
            $record = Pillar::lockForUpdate()->findOrFail($pillar->id);
            $this->checkRevision($record, $request->input('revision'));
            $record->delete();
        });

        return redirect()->route('admin.cms.work')->with('status', 'Pilier retiré de la page avec ses axes.');
    }

    public function destroyAxis(Request $request, Pillar $pillar, InterventionAxis $axis)
    {
        abort_unless($axis->pillar_id === $pillar->id, 404);
        $request->validate(['revision' => 'required|string']);
        DB::transaction(function () use ($request, $pillar, $axis) {
            Pillar::lockForUpdate()->findOrFail($pillar->id);
            $record = InterventionAxis::lockForUpdate()->findOrFail($axis->id);
            $this->checkRevision($record, $request->input('revision'));
            $record->delete();
        });

        return redirect()->route('admin.cms.work.pillars.edit', $pillar)->with('status', 'Axe retiré.');
    }
}
