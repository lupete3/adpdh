from pathlib import Path
import re

def read(path): return Path(path).read_text(encoding='utf-8-sig')
def write(path, text): Path(path).write_text(text, encoding='utf-8', newline='\n')
def sub(text, pattern, replacement, count=1):
    result, n = re.subn(pattern, lambda m: replacement(m) if callable(replacement) else replacement, text, count=count, flags=re.S)
    assert n == count, (pattern, n)
    return result

p='app/Models/MediaAsset.php'; s=read(p).replace(" protected $guarded = [];\n",'').replace("['key', 'media_folder_id'", "['checksum', 'key', 'media_folder_id'"); write(p,s)
p='app/Http/Controllers/MediaLibraryController.php'; s=read(p).replace("->latest('id')->paginate(24);", "->latest('id')->paginate(24)->withQueryString();"); write(p,s)

for name, model in [('CmsNewsController','post'),('CmsActivityController','activity'),('CmsResourceController','resource')]:
    p=f'app/Http/Controllers/{name}.php'; s=read(p)
    marker = '        $paths = [];' if model=='activity' else ('        $uploads = [];' if model=='resource' else '        $path = null;')
    code = f"        $library = app(\\App\\Services\\MediaLibrary::class);\n        $coverId = $library->selection($request, 'cover_media_id', 'cover', ${model}?->cover_media_id);\n        if ($request->boolean('remove_cover') && ! $request->hasFile('cover')) $coverId = null;\n"
    if model=='activity':
        code += "        $request->validate(['photo_ids' => 'nullable|array|max:12', 'photo_ids.*' => 'integer']);\n        $galleryIds = [];\n        foreach ($request->input('photo_ids', []) as $id) $galleryIds[] = $library->image($id, 'photo_ids')->id;\n        foreach ($request->file('photos', []) as $file) $galleryIds[] = $library->upload($file, $request->user()->id)->id;\n        $galleryIds = array_values(array_unique($galleryIds));\n"
    assert marker in s; s=s.replace(marker,code+marker)
    if model=='post':
        s=s.replace('$content, &$path)', '$content, &$path, $coverId)')
        s=sub(s, r"                if \(\$request->boolean\('remove_cover'\)\).*?(?=                \$record->save\(\);)", '                $record->cover_media_id = $coverId;\n')
    elif model=='activity':
        s=s.replace('$data, &$paths)', '$data, &$paths, $coverId, $galleryIds)')
        s=sub(s,r"                if \(\$request->boolean\('remove_cover'\)\).*?(?=                // Each activity)",'                $record->cover_media_id = $coverId;\n')
        s=s.replace("if ($request->hasFile('photos') && ! $record->gallery_id)","if ($galleryIds && ! $record->gallery_id)")
        s=sub(s, r"                    foreach \(\$request->file\('photos', \[\]\) as \$file\) \{.*?\n                    \}", "                    foreach ($galleryIds as $id) {\n                        $gallery->items()->firstOrCreate(['media_asset_id' => $id], ['alt' => $record->title, 'sort_order' => $order++]);\n                    }")
    else:
        s=s.replace('$data, &$uploads)', '$data, &$uploads, $coverId)')
        s=s.replace("['document' => ['local', 'file_media_id', 'document'], 'cover' => ['public', 'cover_media_id', 'image']]", "['document' => ['local', 'file_media_id', 'document']]")
        s=sub(s,r"                if \(\$request->boolean\('remove_cover'\).*?\n                \}", '                $record->cover_media_id = $coverId;')
    write(p,s)

p='app/Http/Controllers/CmsAboutController.php'; s=read(p)
s=sub(s,r"        if \(! empty\(\$data\['photo_media_id'\]\)\) \{.*?\n        \}\n", "        if ($kind === 'equipe') {\n            $data['photo_media_id'] = app(\\App\\Services\\MediaLibrary::class)->selection($request, 'photo_media_id', 'portrait_upload');\n        }\n")
s=sub(s,r"            if \(\$request->hasFile\('portrait_upload'\)\) \{.*?\n            \}\n",'')
write(p,s)
for p in ['app/Http/Controllers/CmsHomeController.php','app/Http/Controllers/CmsSectionContentController.php']:
    s=read(p)
    s=sub(s,r"        if \(! empty\(\$data\['media_asset_id'\]\) && ! MediaAsset::findOrFail\(\$data\['media_asset_id'\]\)->isPubliclyAvailable\(\)\) \{.*?\n        \}","        if (! empty($data['media_asset_id'])) app(\\App\\Services\\MediaLibrary::class)->image($data['media_asset_id'], 'media_asset_id');")
    write(p,s)

for section,var in [('news','post'),('resources','resource'),('activities','activity')]:
    p=f'resources/views/cms/{section}/edit.blade.php'; s=read(p)
    s=sub(s,rf"@if\(\${var}->cover\?->publicUrl\(\)\).*?@endif",'')
    s=sub(s,r'<label[^>]*for="cover"[^>]*>.*?<input[^>]*name="cover"[^>]*>', f'<x-media-picker name="cover_media_id" :value="old(\'cover_media_id\', ${var}->cover_media_id)" label="Image de couverture" />')
    s=re.sub(r'<(?:p|small) class="text-muted">JPEG, PNG ou WebP[^<]*</(?:p|small)>','',s)
    if section=='activities':
        s=sub(s,r'<label[^>]*for="photos"[^>]*>.*?<div id="photo-previews".*?</div>','<x-media-picker name="photo_ids[]" :multiple="true" label="Ajouter des images à la galerie" /><p class="text-muted">Sélectionnez jusqu’à 12 images. Les images déjà présentes ne seront pas dupliquées.</p>')
    write(p,s)

for p in ['resources/views/cms/home/presentation.blade.php','resources/views/cms/home/edit.blade.php']:
    s=read(p)
    s=sub(s,r'<label class="form-label" for="media-.*?</select>', '<x-media-picker name="media_asset_id" :value="$editing ? old(\'media_asset_id\') : $section->media_asset_id" label="Image de la section" />')
    write(p,s)
p='resources/views/cms/home/content.blade.php'; s=read(p)
s=sub(s,r'<label class="form-label" for="media_asset_id">.*?</select>', '<x-media-picker name="media_asset_id" :value="old(\'media_asset_id\', $content->media_asset_id)" label="Image facultative" />'); write(p,s)
p='resources/views/cms/about/record.blade.php'; s=read(p)
s=sub(s,r'<label for="photo_media_id".*?La nouvelle photo remplacera la sélection ci-dessus.</p>', '<x-media-picker name="photo_media_id" :value="old(\'photo_media_id\', $record->photo_media_id)" label="Portrait" />'); write(p,s)

print('CMS image selection integrated')
