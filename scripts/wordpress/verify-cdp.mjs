// Dependency-free, isolated headless Edge verification; never uses the user's browser profile.
import { spawn } from 'node:child_process';
import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
const root=path.resolve(path.dirname(fileURLToPath(import.meta.url)),'../..');
const out=path.join(root,'wordpress-artifacts');await fs.mkdir(out,{recursive:true});
const edge='C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe';
const child=spawn(edge,['--headless=new','--disable-gpu','--disable-background-networking','--no-first-run','--no-default-browser-check','--remote-debugging-port=9227','--remote-debugging-address=127.0.0.1',`--user-data-dir=${path.join(out,'edge-test-profile')}`,'about:blank'],{stdio:['ignore','ignore','pipe'],windowsHide:true});
child.stderr.on('data',data=>fs.appendFile(path.join(out,'browser-start.log'),data).catch(()=>{}));
const pause=ms=>new Promise(r=>setTimeout(r,ms));
let socket;let next=1;const pending=new Map();const exceptions=[];
async function send(method,params={}){
  const id=next++;return new Promise((resolve,reject)=>{const timer=setTimeout(()=>{pending.delete(id);reject(new Error(method+' timed out'));},120000);pending.set(id,{resolve:v=>{clearTimeout(timer);resolve(v);},reject:e=>{clearTimeout(timer);reject(e);}});socket.send(JSON.stringify({id,method,params}));});
}
async function evaluate(expression){const r=await send('Runtime.evaluate',{expression,returnByValue:true,awaitPromise:true});if(r.exceptionDetails)throw new Error(r.exceptionDetails.text);return r.result.value;}
const admin=process.argv.includes('--admin');
const routes=admin?[]:(process.argv.find(a=>a.startsWith('--routes='))?.slice(9)??'/,/qui-sommes-nous,/que-faisons-nous,/activites,/actualites,/ressources,/notre-impact,/nos-succes,/devenir-partenaire,/faire-un-don,/contact').split(',');
const widths=(process.argv.find(a=>a.startsWith('--widths='))?.slice(9)??'390,768,1440').split(',').map(Number);
let results=[];try{results=JSON.parse(await fs.readFile(path.join(out,'browser-report.json'),'utf8')).results;}catch{}
try{
  let tabs;
  for(let i=0;i<240;i++){try{tabs=await(await fetch('http://127.0.0.1:9227/json')).json();if(tabs.some(t=>t.type==='page'))break;}catch{}await pause(250);}
  if(!tabs)throw new Error('Edge did not start');
  socket=new WebSocket(tabs.find(t=>t.type==='page').webSocketDebuggerUrl);
  await new Promise((resolve,reject)=>{socket.onopen=resolve;socket.onerror=reject;});
  socket.onmessage=e=>{const m=JSON.parse(e.data);if(m.id&&pending.has(m.id)){const p=pending.get(m.id);pending.delete(m.id);m.error?p.reject(new Error(m.error.message)):p.resolve(m.result);}if(m.method==='Runtime.exceptionThrown')exceptions.push(m.params.exceptionDetails.exception?.description||m.params.exceptionDetails.text);};
  await send('Page.enable');await send('Runtime.enable');
  await send('Network.clearBrowserCookies');
  if(admin){
    await send('Emulation.setDeviceMetricsOverride',{width:1440,height:1000,deviceScaleFactor:1,mobile:false});
    const access=await fs.readFile(path.join(root,'wordpress-private/local-access.txt'),'utf8');
    const login=access.match(/Utilisateur : (.+)/)[1].trim(),password=access.match(/Mot de passe : (.+)/)[1].trim();
    await send('Page.navigate',{url:'http://127.0.0.1:8093/wp-login.php'});
    for(let i=0;i<120;i++){if(await evaluate("!!document.querySelector('#user_login')"))break;await pause(250);}
    await evaluate(`document.querySelector('#user_login').value=${JSON.stringify(login)};document.querySelector('#user_pass').value=${JSON.stringify(password)};document.querySelector('#wp-submit').click();true`);
    for(let i=0;i<120;i++){if(await evaluate("location.pathname.includes('/wp-admin/')&&document.readyState==='complete'"))break;await pause(250);}
    await send('Page.navigate',{url:'http://127.0.0.1:8093/wp-admin/edit.php?post_type=page'});
    for(let i=0;i<120;i++){if(await evaluate("!!document.querySelector('a.row-title')"))break;await pause(250);}
    const edit=await evaluate("[...document.querySelectorAll('a.row-title')].find(a=>a.textContent.trim()==='Accueil')?.href");
    if(!edit)throw new Error('Page Accueil introuvable dans l’administration');
    await send('Page.navigate',{url:edit});
    for(let i=0;i<120;i++){if(await evaluate("!!document.querySelector('#adpdh-fields')"))break;await pause(250);}
    for(let i=0;i<240;i++){if(await evaluate("document.readyState==='complete'&&!!window.wp?.data?.select('core/editor')?.getCurrentPostId()"))break;await pause(250);}
    await pause(1500);
    const result=await evaluate("({title:document.title,fields:document.querySelectorAll('[name^=adpdh_fields]').length,metabox:!!document.querySelector('#adpdh-fields'),editorReady:!!window.wp?.data?.select('core/editor')?.getCurrentPostId(),editorVisible:!!document.querySelector('.interface-interface-skeleton,.edit-post-layout')})");
    const screenshot=await send('Page.captureScreenshot',{format:'png'});await fs.writeFile(path.join(out,'wordpress-admin.png'),Buffer.from(screenshot.data,'base64'));
    result.errors=exceptions;await fs.writeFile(path.join(out,'admin-report.json'),JSON.stringify(result,null,2));console.log(JSON.stringify(result));
    if(!result.metabox||!result.fields||!result.editorReady||!result.editorVisible)throw new Error('Éditeur WordPress incomplet');
  }
  await send('Emulation.setEmulatedMedia',{features:[{name:'prefers-reduced-motion',value:'reduce'}]});
  for(const width of widths){
    await send('Emulation.setDeviceMetricsOverride',{width,height:1000,deviceScaleFactor:1,mobile:false});
    for(const route of routes){
      const entry={route,width,versions:{}};
      for(const[version,port]of[['laravel',8092],['wordpress',8093]]){
        exceptions.length=0;await send('Page.navigate',{url:`http://127.0.0.1:${port}${route}`});
        for(let i=0;i<100;i++){await pause(100);if(await evaluate("document.readyState==='complete'"))break;}
        await evaluate("document.fonts.ready.then(()=>true)");await pause(150);
        await evaluate("Promise.all([...document.images].map(img=>{img.loading='eager';return img.decode().catch(()=>{});})).then(()=>true)");
        if(route.startsWith('/ressources/'))for(let i=0;i<100;i++){if(await evaluate("!document.querySelector('[data-reader-status]') || document.querySelector('[data-reader-status]').hidden"))break;await pause(150);}
        entry.versions[version]=await evaluate(`(()=>({
          h1:document.querySelector('h1')?.textContent.trim(),
          overflow:document.documentElement.scrollWidth>innerWidth,
          brokenImages:[...document.images].filter(i=>i.complete&&i.naturalWidth===0).map(i=>i.getAttribute('src')),
          text:document.querySelector('main')?.innerText.replace(/\\s+/g,' ').trim(),
          pdfCanvas:document.querySelector('.pdf-reader canvas')?{width:document.querySelector('.pdf-reader canvas').width,height:document.querySelector('.pdf-reader canvas').height,status:document.querySelector('[data-reader-status]')?.textContent}:null,
          sections:[...document.querySelectorAll('main>section,main>.container,main>article')].map(e=>({class:e.className,y:Math.round(e.getBoundingClientRect().y),height:Math.round(e.getBoundingClientRect().height)}))
        }))()`);
        entry.versions[version].errors=[...exceptions];
        if(width===390){
          entry.versions[version].menuOpens=await evaluate("document.querySelector('.menu-toggle').click();document.querySelector('.menu-toggle').getAttribute('aria-expanded')==='true'");
          entry.versions[version].escapeCloses=await evaluate("document.dispatchEvent(new KeyboardEvent('keydown',{key:'Escape',bubbles:true}));document.querySelector('.menu-toggle').getAttribute('aria-expanded')==='false'");
        }
        const metrics=await send('Page.getLayoutMetrics');
        const image=await send('Page.captureScreenshot',{format:'png',captureBeyondViewport:true,clip:{x:0,y:0,width,height:Math.ceil(metrics.cssContentSize.height),scale:1}});
        await fs.writeFile(path.join(out,`${version}-${route==='/'?'home':route.slice(1).replaceAll('/','-')}-${width}.png`),Buffer.from(image.data,'base64'));
        if(route==='/'){const top=await send('Page.captureScreenshot',{format:'png',captureBeyondViewport:false});await fs.writeFile(path.join(out,`${version}-home-${width}-top.png`),Buffer.from(top.data,'base64'));}
      }
      entry.sameMainText=entry.versions.laravel.text===entry.versions.wordpress.text;
      entry.sameSectionGeometry=JSON.stringify(entry.versions.laravel.sections)===JSON.stringify(entry.versions.wordpress.sections);
      results=results.filter(r=>r.route!==route||r.width!==width);results.push(entry);await fs.writeFile(path.join(out,'browser-report.json'),JSON.stringify({results},null,2));
      console.log(JSON.stringify({route,width,sameMainText:entry.sameMainText,sameSectionGeometry:entry.sameSectionGeometry,errors:entry.versions.wordpress.errors}));
    }
  }
}finally{
  if(socket?.readyState===WebSocket.OPEN){await send('Browser.close').catch(()=>{});socket.close();}
  child.kill();
}
process.exitCode=results.some(r=>r.versions.wordpress.errors.length||r.versions.wordpress.overflow||r.versions.wordpress.brokenImages.length)?1:0;
