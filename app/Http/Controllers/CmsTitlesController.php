<?php
namespace App\Http\Controllers;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CmsTitlesController extends Controller {
 public function index() { return view('cms.titles.index', ['pages'=>CmsPage::withCount('titles')->orderBy('id')->get()]); }
 public function edit(CmsPage $page) { if($page->key==='index')return redirect()->route('admin.cms.home'); return view('cms.titles.edit', ['page'=>$page->load('titles')]); }
 public function update(Request $request, CmsPage $page) {
  if($page->key==='index')return redirect()->route('admin.cms.home')->with('status','L’accueil se gère désormais par sections. Aucune modification de l’ancien formulaire n’a été appliquée.');
  $page->load('titles');
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
  if($page->key==='index')return response(app(CmsHomeController::class)->show()->render())->header('X-Robots-Tag','noindex, nofollow')->header('Cache-Control','private, no-store');
  abort_unless(preg_match('/^[a-z0-9-]+$/',$page->key),404);
  abort_unless(view()->exists('adpdh-preview.'.$page->key),404);
  return response()->view('adpdh-preview.'.$page->key,['titles'=>$page->titles()->pluck('value','key')->all(), 'previewPages'=>CmsPage::pluck('id','key')->all()])
   ->header('X-Robots-Tag','noindex, nofollow')->header('Cache-Control','private, no-store');
 }
}
