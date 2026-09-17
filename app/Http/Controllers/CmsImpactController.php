<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\Indicator;
use App\Services\Cms\RecordIndicatorValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CmsImpactController extends Controller
{
    public static function revision(Indicator $indicator): string
    {
        return hash('sha256', json_encode([$indicator->getAttributes(), $indicator->values()->max('id')]));
    }

    public function show()
    {
        return view('adpdh.impact', [
            'indicators' => Indicator::where('is_visible', true)->whereHas('currentValue')->with('currentValue')->orderBy('sort_order')->orderBy('id')->get(),
            'titles' => CmsPage::where('key', 'impact')->first()?->titles()->pluck('value', 'key') ?? collect(),
            'settings' => DB::table('settings')->where('key', 'like', 'adpdh.%')->pluck('value', 'key'),
            'footerSections' => CmsPage::where('key', 'index')->first()?->sections()->with('contents')->get() ?? collect(),
        ]);
    }

    public function index()
    {
        return view('cms.impact.index', ['indicators' => Indicator::with('currentValue')->orderBy('sort_order')->orderBy('id')->get(), 'page' => CmsPage::where('key', 'impact')->first(), 'introduction' => DB::table('settings')->where('key', 'adpdh.impact.introduction')->value('value')]);
    }

    public function presentation(Request $request)
    {
        $data = $request->validate(['introduction' => 'required|string|max:1000']);
        DB::table('settings')->updateOrInsert(['key' => 'adpdh.impact.introduction'], ['value' => $data['introduction'], 'updated_at' => now()]);

        return back()->with('status', 'Introduction enregistrée.');
    }

    public function edit(?Indicator $indicator = null)
    {
        return view('cms.impact.edit', ['indicator' => $indicator ?? new Indicator(['unit' => 'count', 'is_visible' => true, 'sort_order' => (Indicator::max('sort_order') ?? 0) + 1]), 'history' => $indicator?->values()->paginate(15)]);
    }

    public function save(Request $request, RecordIndicatorValue $recorder, ?Indicator $indicator = null)
    {
        $data = $request->validate([
            'revision' => $indicator ? 'required|string' : 'nullable|string',
            'title' => 'required|string|max:255', 'description' => 'nullable|string|max:2000',
            'unit' => 'required|in:count,percent', 'is_visible' => 'required|boolean',
            'sort_order' => 'required|integer|min:0|max:100000',
            'value' => $indicator ? 'nullable|numeric' : 'required|numeric',
        ]);
        $record = DB::transaction(function () use ($request, $recorder, $indicator, $data) {
            $record = $indicator ? Indicator::lockForUpdate()->findOrFail($indicator->id) : new Indicator(['key' => 'impact-'.Str::uuid()]);
            if ($indicator && ! hash_equals(self::revision($record), $data['revision'])) {
                throw ValidationException::withMessages(['revision' => 'Cet indicateur a changé. Rechargez sa fiche avant de réessayer.']);
            }
            if ($indicator && $record->unit !== $data['unit']) {
                throw ValidationException::withMessages(['unit' => 'L’unité ne peut pas être changée : créez un nouvel indicateur pour conserver la cohérence de son historique.']);
            }
            $record->fill(collect($data)->only(['title', 'description', 'unit', 'is_visible', 'sort_order'])->all())->save();
            if ($request->filled('value')) {
                $recorder->handle($record, $request->all(), $request->user()->id);
            }

            return $record;
        });

        return redirect()->route('admin.cms.impact.edit', $record)->with('status', 'Indicateur enregistré. Les nouvelles valeurs sont appliquées partout où cet indicateur est utilisé.');
    }
}
