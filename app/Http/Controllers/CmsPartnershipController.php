<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\PartnershipType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CmsPartnershipController extends Controller
{
    public const TEXTS = [
        'introduction' => 'Institutions, bailleurs, organisations et partenaires techniques : construisons des collaborations au service des communautés.',
        'contact_text' => 'Présentez votre organisation, le domaine qui vous intéresse et la forme de contribution envisagée. Notre équipe pourra échanger avec vous sur les possibilités de collaboration.',
        'document_text' => 'Vision, mission et domaines d’intervention : les repères essentiels pour préparer un échange.',
        'donation_text' => 'Retrouvez les modalités de soutien et le contact dédié aux dons.',
    ];

    private function data(): array
    {
        $settings = DB::table('settings')->where('key', 'like', 'adpdh.%')->pluck('value', 'key');
        $copy = collect(self::TEXTS)->map(fn ($default, $key) => $settings['adpdh.partnership.'.$key] ?? $default);
        $pdf = $settings['adpdh.partnership.pdf'] ?? null;
        $available = $pdf && str_starts_with($pdf, 'partnership/') && ! str_contains($pdf, '..') && Storage::disk('local')->exists($pdf);

        return ['settings' => $settings, 'copy' => $copy, 'pdfAvailable' => (bool) $available, 'page' => CmsPage::where('key', 'devenir-partenaire')->first()];
    }

    public function show()
    {
        $data = $this->data();

        return view('adpdh.partnership', $data + [
            'reasons' => PartnershipType::where('is_visible', true)->orderBy('sort_order')->orderBy('id')->get(),
            'titles' => $data['page']?->titles()->pluck('value', 'key') ?? collect(),
            'footerSections' => CmsPage::where('key', 'index')->first()?->sections()->with('contents')->get() ?? collect(),
        ]);
    }

    public function index()
    {
        return view('cms.partnership.index', $this->data() + ['reasons' => PartnershipType::orderBy('sort_order')->orderBy('id')->get()]);
    }

    public function edit(?PartnershipType $reason = null)
    {
        return view('cms.partnership.edit', ['reason' => $reason ?? new PartnershipType(['is_visible' => true, 'sort_order' => (PartnershipType::max('sort_order') ?? 0) + 1])]);
    }

    public function save(Request $request, ?PartnershipType $reason = null)
    {
        $data = $request->validate(['revision' => $reason ? 'required|string' : 'nullable|string', 'title' => 'required|string|max:255', 'description' => 'required|string|max:5000', 'is_visible' => 'required|boolean', 'sort_order' => 'required|integer|min:0|max:100000']);
        DB::transaction(function () use ($data, $reason) {
            $record = $reason ? PartnershipType::lockForUpdate()->findOrFail($reason->id) : new PartnershipType(['key' => 'partnership-'.Str::uuid()]);
            if ($reason && ! hash_equals(CmsWorkController::revision($record), $data['revision'])) {
                throw ValidationException::withMessages(['revision' => 'Cet élément a changé. Rechargez sa fiche.']);
            }
            unset($data['revision']);
            $record->fill($data)->save();
        });

        return redirect()->route('admin.cms.partnership')->with('status', 'Élément enregistré.');
    }

    public function presentation(Request $request)
    {
        $rules = array_fill_keys(array_keys(self::TEXTS), 'required|string|max:2000');
        $rules['email'] = 'required|email|max:255';
        $rules['phone'] = ['nullable', 'string', 'max:50', 'regex:/^[+0-9 ()-]+$/'];
        $data = $request->validate($rules);
        DB::transaction(function () use ($data) {
            foreach ($data as $key => $value) {
                DB::table('settings')->updateOrInsert(['key' => 'adpdh.partnership.'.$key], ['value' => $value, 'updated_at' => now()]);
            }
        });

        return back()->with('status', 'Textes et contact enregistrés.');
    }

    public function document(Request $request)
    {
        $request->validate(['pdf' => 'required|file|mimes:pdf|extensions:pdf|max:20480']);
        $path = $request->file('pdf')->store('partnership', 'local');
        if (! $path) {
            throw ValidationException::withMessages(['pdf' => 'Le document n’a pas pu être enregistré. Réessayez.']);
        }
        try {
            DB::table('settings')->updateOrInsert(['key' => 'adpdh.partnership.pdf'], ['value' => $path, 'updated_at' => now()]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        return back()->with('status', 'Présentation PDF mise à jour. Le bouton public télécharge désormais ce nouveau document.');
    }

    public function removeDocument()
    {
        DB::table('settings')->where('key', 'adpdh.partnership.pdf')->update(['value' => null, 'updated_at' => now()]);

        return back()->with('status', 'Le PDF a été retiré du téléchargement public.');
    }

    public function download()
    {
        $data = $this->data();
        abort_unless($data['pdfAvailable'], 404);

        return Storage::disk('local')->download($data['settings']['adpdh.partnership.pdf'], 'presentation-adpdh.pdf', ['Content-Type' => 'application/pdf', 'Cache-Control' => 'no-store, private', 'X-Content-Type-Options' => 'nosniff']);
    }
}
