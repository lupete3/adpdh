<?php
namespace App\Http\Controllers;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CmsTitlesController extends Controller {
 private function editablePage(CmsPage $page): CmsPage {
  $page->load('titles');
  if($page->key==='activites') {
   $labels=['text-1'=>'Titre SEO', 'text-2'=>'Description SEO', 'text-8'=>'Surtitre de la page', 'text-3'=>'Titre principal — première ligne', 'text-4'=>'Titre principal — partie en couleur', 'introduction'=>'Texte d’introduction sous le titre', 'list-heading'=>'Titre au-dessus des activités'];
   $page->setRelation('titles', $page->titles->filter(fn($title)=>isset($labels[$title->key]))->each(function($title) use($labels) {$title->label=$labels[$title->key];}));
  }
  if($page->key==='impact') {
   $labels=['text-1'=>'Titre SEO', 'text-3'=>'Titre principal — première ligne', 'text-4'=>'Titre principal — partie en couleur', 'text-19'=>'Surtitre de la page', 'text-23'=>'Surtitre du bloc Au-delà des chiffres', 'text-17'=>'Au-delà des chiffres — titre', 'text-18'=>'Au-delà des chiffres — partie en couleur'];
   $labels += ['text-21'=>'Surtitre des indicateurs', 'text-6'=>'Titre des indicateurs — première ligne', 'text-7'=>'Titre des indicateurs — partie en couleur'];
   $page->setRelation('titles', $page->titles->filter(fn($title)=>isset($labels[$title->key]))->each(function($title) use($labels) {$title->label=$labels[$title->key];}));
  }
  if($page->key==='devenir-partenaire') $page->setRelation('titles', $page->titles->whereIn('key', ['text-1','text-3','text-4','text-9','text-10','text-11','text-12','text-13','text-14','text-15','text-16']));
  return $page;
 }
 public function index() { return view('cms.titles.index', ['pages'=>CmsPage::withCount('titles')->orderBy('id')->get()]); }
 public function edit(CmsPage $page) { if($page->key==='que-faisons-nous')return redirect()->route('admin.cms.work'); if($page->key==='qui-sommes-nous')return redirect()->route('admin.cms.about'); if($page->key==='index')return redirect()->route('admin.cms.home'); return view('cms.titles.edit', ['page'=>$this->editablePage($page)]); }
 public function update(Request $request, CmsPage $page) {
  if($page->key==='que-faisons-nous')return redirect()->route('admin.cms.work');
  if($page->key==='qui-sommes-nous')return redirect()->route('admin.cms.about');
  if($page->key==='index')return redirect()->route('admin.cms.home')->with('status','L’accueil se gère désormais par sections. Aucune modification de l’ancien formulaire n’a été appliquée.');
  $this->editablePage($page);
  $rules=['version'=>['required','integer'], 'titles'=>['required','array']];
  foreach($page->titles as $title) $rules['titles.'.$title->id]=['required','string','max:500'];
  $data=$request->validate($rules);
  DB::transaction(function() use($data,$page) {
   $locked=CmsPage::whereKey($page->id)->lockForUpdate()->firstOrFail();
   if ((int)$locked->version !== (int)$data['version']) {
    throw \Illuminate\Validation\ValidationException::withMessages(['version'=>'Cette page a été modifiée ailleurs. Rechargez-la avant de réessayer.']);
   }
   foreach($page->titles as $title) $title->update(['value'=>$data['titles'][$title->id]]);
   $locked->increment('version');
  });
  return redirect()->route('admin.cms.titles.edit',$page)->with('status','Titres enregistrés. Consultez l’aperçu pour voir le résultat.');
 }
 public function preview(CmsPage $page) {
  if($page->key==='devenir-partenaire')return response(app(CmsPartnershipController::class)->show()->render())->header('X-Robots-Tag','noindex, nofollow')->header('Cache-Control','private, no-store');
  if($page->key==='impact')return response(app(CmsImpactController::class)->show()->render())->header('X-Robots-Tag','noindex, nofollow')->header('Cache-Control','private, no-store');
  if($page->key==='temoignages')return response(app(CmsSuccessController::class)->show()->render())->header('X-Robots-Tag','noindex, nofollow')->header('Cache-Control','private, no-store');
  if($page->key==='activites')return response(app(CmsActivityController::class)->index()->render())->header('X-Robots-Tag','noindex, nofollow')->header('Cache-Control','private, no-store');
  if($page->key==='que-faisons-nous')return response(app(CmsWorkController::class)->show()->render())->header('X-Robots-Tag','noindex, nofollow')->header('Cache-Control','private, no-store');
  if($page->key==='qui-sommes-nous')return response(app(CmsAboutController::class)->show()->render())->header('X-Robots-Tag','noindex, nofollow')->header('Cache-Control','private, no-store');
  if($page->key==='index')return response(app(CmsHomeController::class)->show()->render())->header('X-Robots-Tag','noindex, nofollow')->header('Cache-Control','private, no-store');
  abort_unless(preg_match('/^[a-z0-9-]+$/',$page->key),404);
  abort_unless(view()->exists('adpdh-preview.'.$page->key),404);
  return response()->view('adpdh-preview.'.$page->key,['titles'=>$page->titles()->pluck('value','key')->all(), 'previewPages'=>CmsPage::pluck('id','key')->all()])
   ->header('X-Robots-Tag','noindex, nofollow')->header('Cache-Control','private, no-store');
 }
}
