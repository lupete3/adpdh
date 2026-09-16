<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\CmsSectionContent;
use App\Models\Indicator;
use App\Models\MediaAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CmsSectionContentController extends Controller
{
    private function guard(CmsSection $section): void
    {
        abort_unless($section->page->key === 'index' || ($section->page->key === 'qui-sommes-nous' && $section->key === 'statut'), 404);
    }

    public function index()
    {
        return view('cms.home.sections', ['page' => CmsPage::where('key', 'index')->firstOrFail()->load('sections.contents')]);
    }

    public function section(CmsSection $section)
    {
        $this->guard($section);

        if ($section->page->key === 'qui-sommes-nous') return redirect()->route('admin.cms.about.section', $section);
        return view('cms.home.section', ['section' => $section, 'media' => MediaAsset::all()->filter(fn ($m) => $m->kind === 'image' && $m->isPubliclyAvailable())]);
    }

    public function edit(CmsSection $section, ?CmsSectionContent $content = null)
    {
        $this->guard($section);
        if ($content) {
            abort_unless($content->cms_section_id === $section->id, 404);
        }

        return view('cms.home.content', ['section' => $section, 'content' => $content ?? new CmsSectionContent(['is_visible' => true, 'sort_order' => ($section->contents()->max('sort_order') ?? 0) + 1]), 'media' => MediaAsset::all()->filter(fn ($m) => $m->kind === 'image' && $m->isPubliclyAvailable()), 'indicators' => Indicator::orderBy('sort_order')->get()]);
    }

    public function save(Request $request, CmsSection $section, ?CmsSectionContent $content = null)
    {
        $this->guard($section);
        if ($content) {
            abort_unless($content->cms_section_id === $section->id, 404);
        }
        $data = $request->validate(['version' => $content ? 'required|integer' : 'nullable|integer', 'title' => 'required|string|max:255', 'subtitle' => 'nullable|string|max:255', 'description' => 'nullable|string|max:10000', 'detail_title' => 'nullable|string|max:255', 'detail_text' => 'nullable|string|max:20000', 'link_label' => 'nullable|string|max:255', 'link_url' => ['nullable', 'string', 'max:1000', function ($a, $v, $fail) {
            if (! preg_match('~^(?:https?://[^\s<>]+|mailto:[^\s<>]+|tel:[+0-9 ()-]+|/(?!/)[a-zA-Z0-9/_\\-.#?=&%]*|#[a-zA-Z0-9_-]+|[a-z0-9-]+\\.html(?:#[a-zA-Z0-9_-]+)?)$~D', $v)) {
                $fail('Saisissez un lien valide (page du site, https, email ou téléphone).');
            }
        }], 'media_asset_id' => 'nullable|integer|exists:media_assets,id', 'indicator_id' => 'nullable|integer|exists:indicators,id', 'is_visible' => 'required|boolean', 'is_demo' => 'required|boolean', 'sort_order' => 'required|integer|min:0|max:100000']);
        if (! empty($data['media_asset_id']) && ! MediaAsset::findOrFail($data['media_asset_id'])->isPubliclyAvailable()) {
            throw ValidationException::withMessages(['media_asset_id' => 'Choisissez une image autorisée à la diffusion.']);
        }
        if ($data['is_demo'] && $data['is_visible']) {
            throw ValidationException::withMessages(['is_visible' => 'Un exemple fictif doit rester masqué.']);
        }
        if (in_array($section->key, ['chiffres-cles', 'impact']) && empty($data['indicator_id'])) {
            throw ValidationException::withMessages(['indicator_id' => 'Choisissez l’indicateur à afficher.']);
        }
        DB::transaction(function () use ($data, $content, $section, $request) {
            if ($content) {
                $content = CmsSectionContent::whereKey($content->id)->lockForUpdate()->firstOrFail();
                if ($content->version != (int) $data['version']) {
                    throw ValidationException::withMessages(['version' => 'Cet élément a changé. Rechargez sa fiche.']);
                }$content->revisions()->create(['snapshot' => $content->toArray(), 'user_id' => $request->user()->id, 'reason' => 'Modification de contenu']);
                $content->version++;
            } else {
                $content = $section->contents()->make(['key' => (string) Str::uuid()]);
            }
            unset($data['version']);
            $content->fill($data);
            $content->save();
        });

        return redirect()->route('admin.cms.sections.edit', $section)->with('status', 'Élément enregistré.');
    }

    public function destroy(Request $request, CmsSection $section, CmsSectionContent $content)
    {
        $this->guard($section);
        abort_unless($content->cms_section_id === $section->id, 404);
        $request->validate(['version' => 'required|integer']);
        DB::transaction(function () use ($request, $content) {
            $row = CmsSectionContent::whereKey($content->id)->lockForUpdate()->firstOrFail();
            if ($row->version != $request->integer('version')) {
                throw ValidationException::withMessages(['version' => 'Cet élément a changé. Rechargez la page.']);
            }$row->revisions()->create(['snapshot' => $row->toArray(), 'user_id' => $request->user()->id, 'reason' => 'Retrait de la section']);
            $row->delete();
        });

        return back()->with('status','Élément retiré. Son contenu reste conservé dans la base.');
    }

 public function createIndicator(Request $request,CmsSection $section){
  $this->guard($section);abort_unless(in_array($section->key,['chiffres-cles','impact']),404);
  $data=$request->validate(['title'=>'required|string|max:255','unit'=>'required|in:count,percent','value'=>'required|numeric','source'=>'required|string|max:2000','period_label'=>'nullable|string|max:255']);
  DB::transaction(function()use($data,$section,$request){
   $indicator=Indicator::create(['key'=>(string)Str::uuid(),'title'=>$data['title'],'unit'=>$data['unit'],'is_visible'=>true]);
   app(\App\Services\Cms\RecordIndicatorValue::class)->handle($indicator,$data,$request->user()->id);
   $section->contents()->create(['key'=>(string)Str::uuid(),'title'=>$data['title'],'indicator_id'=>$indicator->id,'is_visible'=>true,'sort_order'=>($section->contents()->max('sort_order')??0)+1]);
  });
  return back()->with('status','Nouvel indicateur ajouté à cette section.');
 }
}
