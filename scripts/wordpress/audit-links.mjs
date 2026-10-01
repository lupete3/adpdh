import fs from 'node:fs/promises';
import path from 'node:path';
import {fileURLToPath} from 'node:url';
const root=path.resolve(path.dirname(fileURLToPath(import.meta.url)),'../..');
const base='http://127.0.0.1:8093';
const pages=['/','/qui-sommes-nous/','/que-faisons-nous/','/activites/','/actualites/','/ressources/','/notre-impact/','/nos-succes/','/devenir-partenaire/','/faire-un-don/','/contact/'];
const links=new Set();
for(const page of pages){const html=await(await fetch(base+page)).text();for(const m of html.matchAll(/href="([^"]+)"/g)){const u=new URL(m[1].replaceAll('&amp;','&'),base+page);if(u.origin===base&&!u.pathname.includes('/wp-')&&!/\.(css|js|png|ico|jpg|webp)$/.test(u.pathname)){u.hash='';links.add(u.href);}}}
const results=[];
for(const url of links){const r=await fetch(url,{method:'GET'});results.push({url,status:r.status,final:r.url,type:r.headers.get('content-type')});await r.body?.cancel();}
await fs.writeFile(path.join(root,'wordpress-artifacts/links-report.json'),JSON.stringify(results,null,2));
console.log(JSON.stringify({checked:results.length,failures:results.filter(r=>r.status>=400)},null,2));
process.exitCode=results.some(r=>r.status>=400)?1:0;
