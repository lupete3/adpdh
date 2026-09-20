<?php

namespace App\Http\Controllers;

use App\Models\MediaAsset;
use App\Services\MediaLibrary;
use Illuminate\Http\Request;

class MediaLibraryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['q' => 'nullable|string|max:150']);
        $query = MediaAsset::where('kind', 'image')->whereIn('disk', ['public', 'builtin']);
        if ($request->filled('q')) $query->where('name', 'like', '%'.$request->input('q').'%');
        if ($request->expectsJson()) {
            $query->where('visibility', 'public')->where('publication_allowed', true)->where('is_demo', false);
            $page = $query->latest('id')->paginate(24)->withQueryString();
            return response()->json(['items' => $page->getCollection()->filter(fn ($asset) => $asset->isPubliclyAvailable())->map(fn ($asset) => $this->item($asset))->values(), 'next' => $page->nextPageUrl()]);
        }
        return view('cms.media.index', ['assets' => $query->latest('id')->paginate(24)->withQueryString()]);
    }

    public function store(Request $request, MediaLibrary $library)
    {
        $request->validate(['image' => MediaLibrary::IMAGE_RULES]);
        $asset = $library->upload($request->file('image'), $request->user()->id);
        if ($request->expectsJson()) return response()->json($this->item($asset), 201);
        return redirect()->route('admin.cms.media.show', $asset)->with('status', 'Image disponible dans la médiathèque. Un fichier identique est réutilisé automatiquement.');
    }

    public function show(MediaAsset $asset, MediaLibrary $library)
    {
        abort_unless($asset->kind === 'image' && in_array($asset->disk, ['public', 'builtin']), 404);
        return view('cms.media.show', ['asset' => $asset, 'usages' => $library->usages($asset)]);
    }

    public function update(Request $request, MediaAsset $asset)
    {
        abort_unless($asset->kind === 'image' && in_array($asset->disk, ['public', 'builtin']), 404);
        $asset->update($request->validate(['name' => 'required|string|max:240', 'alt' => 'nullable|string|max:1000', 'caption' => 'nullable|string|max:2000']));
        return back()->with('status', 'Informations de l’image enregistrées.');
    }

    public function destroy(MediaAsset $asset, MediaLibrary $library)
    {
        $library->delete($asset);
        return redirect()->route('admin.cms.media.index')->with('status', 'Image supprimée de la médiathèque et du stockage.');
    }

    private function item(MediaAsset $asset): array
    {
        return ['id' => $asset->id, 'name' => $asset->name, 'url' => $asset->publicUrl(), 'width' => $asset->width, 'height' => $asset->height];
    }
}
