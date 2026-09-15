"""Build standalone previews from shared fragments; no third-party dependencies."""
from pathlib import Path
from html import escape
import json
import re

ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT / 'resources/adpdh'
OUTPUT = ROOT / 'public/adpdh'
DATA = json.loads((OUTPUT / 'data/demo-content.json').read_text(encoding='utf-8-sig'))

def read(path):
    return path.read_text(encoding='utf-8-sig')

def image(name, alt, eager=False, css=''):
    height = 1260 if name == 'workshop' else 945
    loading = 'fetchpriority="high"' if eager else 'loading="lazy"'
    return f'<img class="{css}" src="assets/{name}-1200.webp" srcset="assets/{name}-640.webp 640w, assets/{name}-1200.webp 1200w, assets/{name}-1680.webp 1680w" sizes="(max-width: 640px) 100vw, 75vw" width="1680" height="{height}" alt="{escape(alt)}" {loading} decoding="async">'

def photo(name, alt, css=''):
    return f'<figure class="editorial-photo {css}">{image(name, alt)}<figcaption>Image d’illustration générée par IA · à remplacer</figcaption></figure>'

def demos():
    cards=[]
    for i, item in enumerate(DATA['news']):
        url = 'actualite.html' if i == 0 else 'actualite-cycle-avec.html'
        asset = 'workshop' if i == 0 else 'community'
        cards.append('<article class="news-preview">' + photo(asset, 'Scène fictive de coopération et d’apprentissage') + '<div class="news-copy"><span class="demo-label">'+escape(item['label'])+'</span><p class="eyebrow">'+escape(item['category'])+'</p><h3>'+escape(item['title'])+'</h3><p>'+escape(item['excerpt'].replace('Exemple de contenu : ',''))+'</p><p class="source-note">Date à renseigner</p><a class="text-link" href="'+url+'">Lire l’article →</a></div></article>')
    return '\n'.join(cards)

def team_cards():
    members = json.loads(read(OUTPUT / 'data/team.json'))
    cards = []
    for member in members:
        photo = member.get('photo')
        candidate = (OUTPUT / photo).resolve() if photo else None
        valid = candidate and candidate.is_relative_to(OUTPUT.resolve()) and candidate.is_file()
        src = photo if valid else 'assets/avatar-default.svg'
        alt = 'Portrait de ' + member['name'] if valid else ''
        phone = member.get('phone')
        email = member.get('email')
        contact = '<details class="team-contact"><summary>Coordonnées</summary><div class="team-contact-links">'
        if phone:
            contact += f'<a href="tel:{escape(phone.replace(" ", ""), quote=True)}">{escape(phone)}</a>'
        if email:
            contact += f'<a href="mailto:{escape(email, quote=True)}">{escape(email)}</a>'
        else:
            contact += '<span>E-mail non renseigné</span>'
        contact += '</div></details>'
        cards.append(f'<article><div class="team-avatar"><img src="{escape(src, quote=True)}" alt="{escape(alt, quote=True)}" width="480" height="480" loading="lazy" decoding="async" data-team-photo></div><h3>{escape(member["name"])}</h3><p>{escape(member["role"])}</p>{contact}</article>')
    return ''.join(cards)

def expand(text, context):
    def replace(match):
        key=match.group(1).strip()
        if key in context: return context[key]
        path=SOURCE/'partials'/f'{key}.html'
        if path.is_file(): return expand(read(path), context)
        raise ValueError(f'Unknown template token: {key}')
    return re.sub(r'\{\{([^{}]+)\}\}', replace, text)

pages={
    'index.html': ('ADPDH — La dignité humaine au cœur de notre action', 'Développement, protection et droits humains : découvrez l’engagement d’ADPDH auprès des communautés du Sud-Kivu.'),
    'qui-sommes-nous.html': ('Qui sommes-nous ? — ADPDH', 'Notre histoire, notre mission, nos valeurs et notre ancrage dans les Grands Lacs. Découvrez ADPDH, créée à Bukavu en 2010.'),
}
pages.update({
    'que-faisons-nous.html': ('Que faisons-nous ? — ADPDH', 'Quatre piliers et neuf axes pour le développement, la protection et les droits humains.'),
    'activites.html': ('Nos activités — ADPDH', 'AVEC et AGR, résilience communautaire et projet THIMO STEP au Sud-Kivu.'),
    'activite-avec-agr.html': ('AVEC et AGR — ADPDH', 'Épargne communautaire et accompagnement des activités génératrices de revenus.'),
    'activite-resilience.html': ('Résilience et leadership — ADPDH', 'Leadership, dialogue et cohésion sociale au Sud-Kivu.'),
    'activite-thimo-step.html': ('THIMO — Projet STEP — ADPDH', 'Projet achevé en 2023–2024 : travaux publics et réintégration socio-économique.'),
})
pages.update({
    'impact.html': ('Notre impact — ADPDH', 'Indicateurs STEP, AVEC et AGR : chiffres, périodes et sources disponibles.'),
    'temoignages.html': ('Nos succès et témoignages — ADPDH', 'Premiers témoignages en cours de collecte. Découvrez le format des futurs récits.'),
    'temoignage-exemple.html': ('Récit fictif de démonstration — ADPDH', 'Exemple fictif à remplacer : mise en page des futurs témoignages ADPDH.'),
})
pages.update({
    'devenir-partenaire.html': ('Devenir partenaire — ADPDH', 'Financement, expertise, matériel et partenariats institutionnels avec ADPDH.'),
    'faire-un-don.html': ('Faire un don — ADPDH', 'Contactez ADPDH pour préparer votre soutien par virement.'),
    'contact.html': ('Contact — ADPDH', 'Adresse, téléphone et e-mail de l’équipe ADPDH à Bukavu.'),
})
pages.update({
    'ressources.html': ('Ressources et publications — ADPDH', 'Documents de référence et publications à venir.'),
    'actualites.html': ('Actualités — ADPDH', 'Les nouvelles d’ADPDH : exemples de publications pour la maquette.'),
    'actualite.html': ('Formation — Article fictif — ADPDH', 'Exemple fictif de publication sur la gestion d’une activité.'),
    'actualite-cycle-avec.html': ('Cycle AVEC — Article fictif — ADPDH', 'Exemple fictif de publication sur l’épargne communautaire.'),
    'mentions-legales.html': ('Mentions légales — ADPDH', 'Identification, confidentialité du formulaire et crédits de la maquette.'),
})
for filename,(title,description) in pages.items():
    context={
        'title':escape(title), 'description':escape(description),
        'hero_image':image('community', 'Illustration : des adultes échangent autour de cahiers dans un espace communautaire', True),
        'community_photo':photo('community','Illustration : un groupe échange et travaille ensemble'),
        'workshop_photo':photo('workshop','Illustration : des entrepreneures dans un atelier de couture'),
        'demo_news':demos(),
        'team_cards':team_cards(),
        'demo_quote':escape(DATA['testimonials'][0]['quote']),
    }
    context['content']=expand(read(SOURCE/'pages'/filename),context)
    html=expand(read(SOURCE/'layout.html'),context)
    if filename == 'qui-sommes-nous.html':
        html=html.replace('href="qui-sommes-nous.html">Présentation de l’organisation','href="qui-sommes-nous.html" aria-current="page">Présentation de l’organisation')
    if filename in ('que-faisons-nous.html', 'activites.html', 'impact.html', 'temoignages.html', 'devenir-partenaire.html', 'faire-un-don.html', 'contact.html'):
        html=html.replace(f'href="{filename}">', f'href="{filename}" aria-current="page">', 1)
    elif filename.startswith('activite-'):
        html=html.replace('href="activites.html">Activités', 'href="activites.html" aria-current="true">Activités', 1)
    if filename == 'temoignage-exemple.html':
        html=html.replace('<meta name="description"', '<meta name="robots" content="noindex, nofollow">\n  <meta name="description"', 1)
        html=html.replace('href="temoignages.html">Nos succès', 'href="temoignages.html" aria-current="true">Nos succès', 1)
    if filename in ('actualite.html', 'actualite-cycle-avec.html'):
        html=html.replace('<meta name="description"', '<meta name="robots" content="noindex, nofollow">\n  <meta name="description"', 1)
    (OUTPUT/filename).write_text(html,encoding='utf-8')
    print(f'Built {filename}')
