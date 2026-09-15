"""One-time source inventory. Review the resulting manifest before seeding; never parse HTML at runtime."""
from pathlib import Path
from html.parser import HTMLParser
import json,re,hashlib,mimetypes
from PIL import Image
ROOT=Path(__file__).resolve().parents[2]
class Node:
 def __init__(self,tag='',attrs=None,parent=None,text=''): self.tag=tag;self.attrs=dict(attrs or []);self.parent=parent;self.children=[];self.text=text;self.legacy=None
 def all(self,tag=None,cls=None):
  result=[]
  for c in self.children:
   if (tag is None or c.tag==tag) and (cls is None or cls in c.attrs.get('class','').split()):result.append(c)
   result.extend(c.all(tag,cls))
  return result
 def first(self,tag=None,cls=None):return next(iter(self.all(tag,cls)),None)
 def plain(self):return self.text if not self.tag else ''.join('\n' if c.tag=='br' else c.plain() for c in self.children)
 def parts(self,skip_em=False):
  if not self.tag:
   if self.legacy:return [{'legacy':self.legacy,'prefix':re.match(r'^\s*',self.text)[0],'suffix':re.search(r'\s*$',self.text)[0]}]
   return [{'literal':self.text}]
  if self.tag=='br':return [{'literal':'\n'}]
  if skip_em and self.tag=='em':return []
  return sum([c.parts(skip_em) for c in self.children],[])
class Parser(HTMLParser):
 def __init__(self):super().__init__(convert_charrefs=True);self.root=Node('root');self.stack=[self.root]
 def handle_starttag(self,t,a):
  n=Node(t,a,self.stack[-1]);self.stack[-1].children.append(n)
  if t not in ['area','base','br','col','embed','hr','img','input','link','meta','param','source','track','wbr']:self.stack.append(n)
 def handle_endtag(self,t):
  for i in range(len(self.stack)-1,0,-1):
   if self.stack[i].tag==t:self.stack=self.stack[:i];break
 def handle_data(self,d):self.stack[-1].children.append(Node(parent=self.stack[-1],text=d))
def plain(n):return n.plain().strip() if n else ''
def body(paras):return {'version':1,'blocks':[{'type':'paragraph','text':plain(n)} for n in paras if plain(n)]}
def first(n,tag=None,cls=None):return n.first(tag,cls) if n else None
pages={}
for f in (ROOT/'public/adpdh').glob('*.html'):
 x=Parser();x.feed(f.read_text(encoding='utf-8'));main=x.root.first('main');pages[f.stem]=main
 i=3
 for h in [n for n in main.all() if n.tag in ('h1','h2','h3')]+main.all('p','eyebrow'):
  for n in h.all():
   if not n.tag and n.text.strip():n.legacy=f'text-{i}';i+=1
out={'sections':[],'pillars':[],'activities':[],'history':[],'values':[],'zones':[],'partnerships':[],'publications':[],'news':[],'testimonials':[],'media':[],'faq':[]}
def add_section(page,n,key,collection=None,label=None):
 if n is None:return
 h=next((x for x in n.all() if x.tag in ('h1','h2','h3')),None);ey=n.first('p','eyebrow');accent=first(h,'em')
 heading=n.first(cls='section-heading')
 intro=next((x for x in (heading or n).all('p') if 'eyebrow' not in x.attrs.get('class','') and not x.first('strong')),None)
 # Introduction for a section with a collection stops before the repeated records.
 if collection and not heading:
  intro=next((x for x in n.all('p') if 'eyebrow' not in x.attrs.get('class','') and x.parent==n),None) or intro
 fields={'eyebrow':ey.parts() if ey else [],'title':h.parts(True) if h else [],'title_accent':accent.parts() if accent else [],'introduction':[{'literal':plain(intro)}]}
 buttons=[{'label':plain(a).rstrip(' →↗'),'url':a.attrs['href']} for a in n.all('a') if any(x in a.attrs.get('class','') for x in ('button','text-link'))][:2]
 paras=[] if collection else [x for x in n.all('p') if 'eyebrow' not in x.attrs.get('class','') and x!=intro]
 out['sections'].append({'page':page,'key':key,'label':label or plain(h) or key,'template':'collection' if collection else ('hero' if key=='hero' else 'editorial'),'collection':collection,'fields':fields,'body':body(paras),'buttons':buttons})
home=pages['index']; collections={'piliers':'pillars','activites':'activities','impact':'indicators','temoignages':'testimonials','actualites':'posts','ressources':'publications','partenariat':'partnership_types','contact':'contact_settings'}
for n in home.children:
 if n.tag!='section':continue
 key=n.attrs.get('id') or ('hero' if 'photo-hero' in n.attrs.get('class','') else 'chiffres-cles')
 add_section('index',n,key,collections.get(key,'indicators' if key=='chiffres-cles' else None))
# Plain page headers, excluding entity titles, belong to the page editor.
details=['activite-avec-agr','activite-resilience','activite-thimo-step','actualite','actualite-cycle-avec','temoignage-exemple']
for page,main in pages.items():
 if page=='index' or page in details:continue
 add_section(page,main.first('section','about-heading'),'hero')
about=pages['qui-sommes-nous']
for key,col in [('histoire','history_events'),('valeurs','organization_values'),('statut',None),('zones','intervention_zones'),('equipe','team_members')]:
 n=next(x for x in about.all('section') if x.attrs.get('id')==key);add_section('qui-sommes-nous',n,key,col)
 if key=='histoire':
  section=out['sections'][-1];section['body']=body([x for x in n.all('p') if x.parent.tag!='li' and not any(a in x.attrs.get('class','') for a in ['eyebrow','history-aside']) and not any(x in li.all() for li in n.all('li'))])
for i,n in enumerate(about.first(cls='mission-grid').all('article')):add_section('qui-sommes-nous',n,['vision','mission'][i])
for i,n in enumerate(about.first(cls='timeline').all('li')):
 out['history'].append({'key':str(i+1),'page':'qui-sommes-nous','title_parts':n.first('h3').parts(),'description':plain(n.first('p')),'period_label':plain(n.first('span'))})
for i,n in enumerate(about.first(cls='values-list').all('li')):out['values'].append({'key':str(i+1),'page':'qui-sommes-nous','title_parts':n.first('h3').parts(),'description':plain(n.first('p'))})
for name in ['Sud-Kivu','Nord-Kivu']:out['zones'].append({'key':name.lower(),'title':name,'zone_status':'current','description':'Province d’intervention mentionnée dans le document institutionnel ADPDH.'})
for name in ['Tanganyika','Ituri','Haut-Uele','Maniema','Kasaï','Lualaba']:out['zones'].append({'key':name.lower(),'title':name,'zone_status':'planned','description':'Perspective d’extension selon les capacités et financements disponibles.'})
for i,n in enumerate(pages['que-faisons-nous'].all('section','domain-section')):
 axes=[{'key':'axe-'+str(int(n.first('ol').attrs['start'])+j),'title_parts':li.first('h3').parts(),'description':plain(li.first('p'))} for j,li in enumerate(n.all('li'))]
 out['pillars'].append({'key':n.attrs['id'],'page':'que-faisons-nous','title_parts':n.first('h2').parts(),'description':plain(n.first('p','lead')),'axes':axes})
add_section('que-faisons-nous',pages['que-faisons-nous'].first('section','cross-action'),'complementarite')
for i,page in enumerate(details[:3]):
 main=pages[page];detail=main.first('section','activity-detail');facts={plain(x.first('dt')):plain(x.first('dd')) for x in detail.first('dl').children if x.tag=='div'}
 result=main.first('section','result-block')
 out['activities'].append({'key':page.removeprefix('activite-'),'page':page,'title_parts':main.first('h1').parts(),'description':plain(main.first('section','about-heading').all('p')[-1]),'objective':plain(detail.first('p','lead')),'audience':facts['Public cible'],'location':facts['Zone'],'activity_status':'completed' if i==2 else 'ongoing','period_label':'2023–2024' if i==2 else None,'start_year':2023 if i==2 else None,'end_year':2024 if i==2 else None,'results_note':None if i!=1 else plain(result.first('p','lead')),'source_note':plain(result.first('p','source-note')),'steps':[{'key':str(j+1),'title_parts':li.first('h3').parts(),'description':plain(li.first('p'))} for j,li in enumerate(main.first('ol','approach-grid').all('li'))],'cover':'workshop' if i==0 else 'community','axes':[['axe-4','axe-6','axe-7'],['axe-1','axe-7'],['axe-4','axe-5']][i]})
for i,n in enumerate(pages['devenir-partenaire'].first(cls='partner-options').all('article')):out['partnerships'].append({'key':str(i+1),'page':'devenir-partenaire','title_parts':n.first('h2').parts(),'description':plain(n.all('p')[-1])})
for i,n in enumerate(pages['ressources'].all('article','resource-card')):out['publications'].append({'key':['presentation','strategie','modules','rapports'][i],'page':'ressources','title_parts':n.first('h2').parts(),'description':plain(n.first('div','resource-copy').all('p')[-1]),'category':['organisation','strategie','formation','rapports'][i]})
for i,page in enumerate(details[3:5]):
 main=pages[page];out['news'].append({'key':page,'page':page,'title_parts':main.first('h1').parts(),'content':body(main.first('article','article-body').all('p')),'category':['formation','avec'][i],'cover':['workshop','community'][i]})
main=pages['temoignage-exemple'];out['testimonials'].append({'key':'demo-parcours','page':'temoignage-exemple','title_parts':main.first('h1').parts(),'body':body(main.first('article','story-detail').all('p'))})
for i,n in enumerate(pages['faire-un-don'].first(cls='support-faq').all('details')):out['faq'].append({'key':'don-'+str(i+1),'question':plain(n.first('summary')),'answer':plain(n.first('p'))})
# Preserve remaining editorial framing without turning collection cards into section fields.
for page,classes in {'impact':['section-heading','institutional-section','about-cta'],'temoignages':['cross-action'],'devenir-partenaire':['partnership-intro','cross-action'],'faire-un-don':['donation-purpose'],'contact':[],'mentions-legales':[]}.items():
 for cls in classes:add_section(page,pages[page].first(cls=cls),cls)
for i,n in enumerate(pages['mentions-legales'].first(cls='legal-content').children):
 if n.tag=='section':add_section('mentions-legales',n,n.attrs.get('id') or 'legal-'+str(i))
for key in ['logo-small.webp','favicon.png','community-1200.webp','workshop-1200.webp','avatar-default.svg']:
 path=ROOT/'public/adpdh/assets'/key
 width=height=None
 if path.suffix!='.svg':
  with Image.open(path) as im:width,height=im.size
 name=key.split('-1200')[0].split('.')[0]
 out['media'].append({'key':name,'name':key,'path':'adpdh/assets/'+key,'mime_type':({'webp':'image/webp','svg':'image/svg+xml','png':'image/png'}.get(path.suffix.lstrip('.')) or mimetypes.guess_type(key)[0]),'size':path.stat().st_size,'width':width,'height':height,'is_illustration':name in ['community','workshop','avatar-default'],'credit':'Illustration générée par IA — ne représente pas une activité réelle ADPDH' if name in ['community','workshop'] else 'Identité ADPDH / graphisme de maquette','variants':[{'name':str(size),'path':'adpdh/assets/'+name+'-'+str(size)+'.webp','mime_type':'image/webp','size':(ROOT/'public/adpdh/assets'/f'{name}-{size}.webp').stat().st_size,'width':Image.open(ROOT/'public/adpdh/assets'/f'{name}-{size}.webp').size[0],'height':Image.open(ROOT/'public/adpdh/assets'/f'{name}-{size}.webp').size[1]} for size in [640,1680]] if name in ['community','workshop'] else []})
(ROOT/'database/content/adpdh-structured.json').write_text(json.dumps(out,ensure_ascii=False,indent=2)+'\n',encoding='utf-8')
print({k:len(v) for k,v in out.items()})
