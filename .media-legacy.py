from pathlib import Path
import re

files=list(Path('resources/views/livewire/admin').rglob('*.blade.php'))+[Path('app/Livewire/SettingsManager.php')]
count=0
for p in files:
    s=p.read_text(encoding='utf-8-sig')
    php, sep, html=s.partition('?>')
    # Image replacement and record deletion must only detach the shared file.
    php=re.sub(r"Storage::disk\('public'\)->delete\([^;]+\);", lambda m: m[0] if 'file_path' in m[0] else '// Shared images are deleted only from the media library.', php)
    stores=list(re.finditer(r"\$this->(\w+)->store\('[^']+', 'public'\)",php))
    fields=[m[1] for m in stores if m[1] != 'file']
    if fields:
        assert 'use WithFileUploads;' in php, str(p)
        php=php.replace('use WithFileUploads;', 'use WithFileUploads;\n    use \\App\\Livewire\\Concerns\\UsesMediaLibrary;')
        models=re.findall(r'public\s+\w+\s+\$(\w+);',php)
        for field in fields:
            # Keep optional legacy image uploads supported, but register all of them centrally.
            php=re.sub(rf"\$this->{field}->store\('[^']+', 'public'\)", f"$this->mediaPath('{field}')",php)
            php=php.replace(f'if ($this->{field})', f"if ($this->mediaChanged('{field}'))")
            pattern=rf"('{field}'\s*=>\s*)(\[[^\]\n]*'image'[^\]\n]*\]|'[^'\n]*\bimage\b[^'\n]*')"
            php,n=re.subn(pattern,lambda m: m[1]+f"$this->mediaRule('{field}', "+('true' if 'required' in m[2] else 'false')+')',php)
            if p.name!='SettingsManager.php': assert n, (p,field)
            # Current preview is taken from the same field used by the legacy form.
            current='null'
            column={'new_image':'image','new_secondary_image':'secondary_image','new_photo':('author_photo' if 'testimonials' in str(p) else 'photo'),'new_logo':'logo'}.get(field,field)
            if 'gallery-photos' in str(p): column='image_path'
            if p.name=='SettingsManager.php': current=f'$existing_{field}'
            elif p.name=='edit.blade.php' and models: current=f'${models[0]}->{column}'
            html,n=re.subn(rf'<input\b(?=[^>]*type="file")(?=[^>]*wire:model="{field}")[^>]*>',f'<x-media-picker wire-field="{field}" :current-url="media_url({current})" label="Image" />',html)
            if p.name!='SettingsManager.php': assert n==1,(p,field,n)
            # The picker now owns the current/new preview; remove duplicate legacy preview blocks.
            html=re.sub(rf'<div class="mt-2">\s*@if\s*\(\${field}\).*?@endif\s*</div>', '', html,flags=re.S)
        if p.name=='SettingsManager.php':
            php=php.replace('    public function save()\n    {', "    public function save()\n    {\n        abort_unless(auth()->user()?->is_admin, 403);\n        $this->validate(['logo' => $this->mediaRule('logo'), 'feature_image' => $this->mediaRule('feature_image')]);")
        count+=len(fields)
    p.write_text(php+sep+html,encoding='utf-8',newline='\n')

p=Path('resources/views/livewire/settings-manager.blade.php');s=p.read_text(encoding='utf-8-sig')
for field in ['logo','feature_image']:
    s,n=re.subn(rf'<input\b(?=[^>]*type="file")(?=[^>]*wire:model="{field}")[^>]*>',f'<x-media-picker wire-field="{field}" :current-url="media_url($existing_{field})" label="Image" />',s)
    assert n==1
p.write_text(s,encoding='utf-8',newline='\n')
print(f'Integrated {count} legacy image fields')
