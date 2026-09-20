from pathlib import Path
import re
for p in Path('resources/views').rglob('*.blade.php'):
    s=p.read_text(encoding='utf-8-sig')
    t=re.sub(r"asset\('storage/'\s*\.\s*([^()]+)\)",lambda m: m[0] if 'file_path' in m[1] else 'media_url('+m[1]+')',s)
    t=re.sub(r"\n\s*if \([^\n]+\) \{\n\s*// Shared images are deleted only from the media library\.\n\s*\}", '', t)
    if s!=t: p.write_text(t,encoding='utf-8',newline='\n')
