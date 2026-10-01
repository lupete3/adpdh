"""Validate PHP and package only deployable source; no runtime, DB or credentials."""
import json, pathlib, subprocess, zipfile
from PIL import Image, ImageChops

root = pathlib.Path(__file__).resolve().parents[2]
out = root / 'wordpress-artifacts'
release = out / 'releases'
release.mkdir(parents=True, exist_ok=True)
php = r'C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe'
files = list((root / 'wordpress').rglob('*.php')) + list((root / 'scripts/wordpress').glob('*.php'))
failures = []
for file in files:
    result = subprocess.run([php, '-l', str(file)], capture_output=True, text=True)
    if result.returncode:
        failures.append({'file':str(file.relative_to(root)), 'error':result.stdout + result.stderr})
lint = {'php_files':len(files), 'failures':failures}
(out / 'lint-report.json').write_text(json.dumps(lint, indent=2), encoding='utf8')
if failures:
    raise SystemExit(json.dumps(lint))

pixels = []
for before in out.glob('laravel-*.png'):
    after = out / before.name.replace('laravel-', 'wordpress-', 1)
    if not after.exists():
        continue
    with Image.open(before) as a, Image.open(after) as b:
        entry = {'file':before.name, 'same_size':a.size == b.size}
        if a.size == b.size:
            difference = ImageChops.difference(a.convert('RGB'), b.convert('RGB'))
            entry['identical'] = difference.getbbox() is None
            entry['difference_box'] = difference.getbbox()
            histogram = difference.histogram()
            entry['mean_channel_difference'] = round(sum((i % 256) * v for i,v in enumerate(histogram))/(a.width*a.height*3), 5)
        else:
            entry['sizes'] = [a.size, b.size]
        pixels.append(entry)
(out / 'pixel-report.json').write_text(json.dumps(pixels, indent=2), encoding='utf8')

archives = []
for directory, name in [('themes/adpdh','adpdh-theme.zip'), ('plugins/adpdh-core','adpdh-core.zip')]:
    source = root / 'wordpress/wp-content' / directory
    destination = release / name
    with zipfile.ZipFile(destination, 'w', zipfile.ZIP_DEFLATED) as archive:
        for file in source.rglob('*'):
            if file.is_file():
                archive.write(file, file.relative_to(source.parent))
    archives.append({'file':str(destination.relative_to(root)), 'bytes':destination.stat().st_size})
print(json.dumps({'lint':lint,'screenshots':len(pixels),'pixel_identical':sum(p.get('identical',False) for p in pixels),'archives':archives},indent=2))
integration=json.loads((out/'integration-report.json').read_text(encoding='utf8'))
browser=json.loads((out/'browser-report.json').read_text(encoding='utf8'))['results']
links=json.loads((out/'links-report.json').read_text(encoding='utf8'))
admin=json.loads((out/'admin-report.json').read_text(encoding='utf8'))
report=f'''# Vérification de la conversion ADPDH

Référence : version Laravel actuelle du dépôt. Installation indépendante WordPress {integration['wordpress']}.

| Contrôle | Résultat |
|---|---|
| Syntaxe PHP | {lint['php_files']} fichiers, {len(failures)} erreur |
| Permissions, import, champs, révisions, formulaire et PDF | {integration['checks']} contrôles, {len(integration['failures'])} échec |
| Pages et fiches comparées à 390, 768 et 1440 px | {len(browser)} comparaisons |
| Textes principaux identiques | {sum(r['sameMainText'] for r in browser)} / {len(browser)} |
| Géométrie des sections identique | {sum(r['sameSectionGeometry'] for r in browser)} / {len(browser)} |
| Captures identiques pixel par pixel (pages complètes et cadrages d’accueil) | {sum(p.get('identical',False) for p in pixels)} / {len(pixels)} |
| Erreurs JavaScript sur le site WordPress | {sum(len(r['versions']['wordpress']['errors']) for r in browser)} |
| Liens internes contrôlés | {len(links)}, dont {sum(r['status']>=400 for r in links)} en erreur |
| Éditeur WordPress chargé et visible | {'Oui' if admin.get('editorReady') and admin.get('editorVisible') else 'Non confirmé'} |
| Champs détectés sur la page d’accueil | {admin['fields']} |

Les tests d’autorisations ont été exécutés avec PublishPress Capabilities et PublishPress Permissions activés. Les modifications temporaires des tests ont été supprimées ou restaurées.

Les captures proviennent d’Edge dans un profil isolé, en mode de mouvement réduit pour stabiliser la comparaison. Les images sont chargées avant capture. Les scripts et animations de Laravel sont conservés ; la comparaison ne représente pas une certification de tous les navigateurs ou de futurs contenus modifiés.

Restent propres à la mise en production : configuration SMTP, informations légales de l’hébergement, URL/HTTPS, sauvegardes et validation de la politique de conservation. L’option ACF Pro n’a pas été testée avec une licence ; l’éditeur de champs intégré fonctionne avec les extensions gratuites installées.

Rapports détaillés et captures : `../wordpress-artifacts/`. Archives installables : `../wordpress-artifacts/releases/`.
'''
(root/'wordpress/VALIDATION.md').write_text(report,encoding='utf8')
