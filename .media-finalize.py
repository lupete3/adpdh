from pathlib import Path
import re
paths=list(Path('resources/views/livewire/admin').rglob('*.blade.php'))+[Path('resources/views/livewire/settings-manager.blade.php'),Path('resources/views/livewire/settings/profile.blade.php')]
for p in paths:
    s=p.read_text(encoding='utf-8-sig'); t=s
    if '<x-media-picker' not in s: continue
    t=t.replace(':current-url="media_url(null)"', ':current-url="null"')
    t=re.sub(r':current-url="media_url\(([^()]+)\)"',lambda m: ':current-url="('+m[1]+' ?? null) ? media_url('+m[1]+') : null"',t)
    if p.name=='settings-manager.blade.php':
        t=re.sub(r'\s*@if \(\$existing_(?:logo|feature_image)\)\s*<img[^>]*>\s*@endif', '', t)
    if s!=t: p.write_text(t,encoding='utf-8',newline='\n')
p=Path('resources/views/components/layouts/menu/vertical.blade.php'); p.write_text(p.read_text(encoding='utf-8-sig'),encoding='utf-8',newline='\n')
