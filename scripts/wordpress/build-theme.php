<?php
/** Build ordinary PHP templates from the CURRENT Laravel views. No Laravel runtime in WordPress. */
require dirname(__DIR__,2).'/vendor/autoload.php';
$root = dirname(__DIR__,2);
$theme = $root.'/wordpress/wp-content/themes/adpdh';
foreach (['views','assets','includes'] as $dir) if (!is_dir($theme.'/'.$dir)) mkdir($theme.'/'.$dir,0775,true);
$compiler = new Illuminate\View\Compilers\BladeCompiler(new Illuminate\Filesystem\Filesystem(), sys_get_temp_dir());
$copy = [];
foreach (glob($root.'/resources/views/adpdh/*.blade.php') as $source) {
    $name = basename($source,'.blade.php');
    if (in_array($name,['activity-layout','contact','header','home-footer'],true)) continue;
    $view = file_get_contents($source);
    if (str_contains($view,'<!doctype')) {
        $view = preg_replace('~^.*?<main id="contenu">~s','',$view);
        $view = preg_replace('~</main>.*$~s','',$view);
    }
    $view = preg_replace('~^@extends\([^\r\n]+\)\s*~','',$view);
    $view = str_replace(["@section('content')",'@endsection'], '', $view);
    $view = preg_replace('~<link rel="stylesheet"[^\r\n]+>~','',$view);
    $view = preg_replace('~@vite\([^\r\n]+\)~','',$view);
    $view = str_replace('\\App\\Support\\ActivityHtml::clean', 'rich_content', $view);
    if ($name === 'home-sections') {
        $view = preg_replace('~        if \(in_array\(\$section->key,.*?\n        }~s', '', $view, 1);
    }
    if ($name === 'donation') $view = str_replace("asset('adpdh/assets/workshop-1200.webp')", "page_image('workshop-1200.webp')", $view);
    // Capture standalone literal editorial text, preserving source markup and whitespace.
    $view = preg_replace_callback('~(<(?:p|h[1-6]|span|a|small|strong|em|summary|button|option|label|dt|dd|address|div)\b[^<>]*>)([^<>]+)(?=<)~u', function ($m) use (&$copy,$name) {
        $text = trim($m[2]);
        if (!$text || preg_match('~[@{}$]|^[-—↗→←↓↑%×0-9 /·]+$~u',$text)) return $m[0];
        $key = 'text_'.substr(sha1($text),0,10);
        $copy[$name][$key] = html_entity_decode($text,ENT_QUOTES|ENT_HTML5,'UTF-8');
        return $m[1].'{{ copy_text('.var_export($name,true).', '.var_export($key,true).', '.var_export($copy[$name][$key],true).') }}';
    },$view);
    $php = $compiler->compileString($view);
    $php = str_replace('\\Illuminate\\Support\\Arr','\\ADPDH\\Theme\\Arr',$php);
    $php = preg_replace('~<\?php /\*\*PATH.*?ENDPATH\*\*/ \?>~s','',$php);
    file_put_contents($theme.'/views/'.$name.'.php', "<?php\nnamespace ADPDH\\Theme;\ndefined('ABSPATH') || exit;\n// Ported from current Laravel view: ".$name.".blade.php.\n?>\n".$php);
}
file_put_contents($theme.'/includes/copy-defaults.php', "<?php\ndefined('ABSPATH') || exit;\nreturn ".var_export($copy,true).";\n");
foreach (glob($root.'/public/adpdh/assets/*') as $asset) {
    if (is_file($asset) && preg_match('~\.(css|js|png|jpe?g|webp|svg|ico)$~i',$asset)) copy($asset,$theme.'/assets/'.basename($asset));
}
$script=file_get_contents($theme.'/assets/adpdh.js');
$script=str_replace("document.querySelector('#year').textContent = new Date().getFullYear();", "const yearElement = document.querySelector('#year');\nif (yearElement) yearElement.textContent = new Date().getFullYear();", $script);
file_put_contents($theme.'/assets/adpdh.js',$script);
$reader = file_get_contents($root.'/resources/assets/js/resource-reader.js');
$reader = str_replace(["from 'pdfjs-dist'", "import workerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';"], ["from './pdfjs/pdf.min.mjs'", "const workerUrl = new URL('./pdfjs/pdf.worker.min.mjs', import.meta.url).href;"], $reader);
file_put_contents($theme.'/assets/resource-reader.js',$reader);
$copyTree = function ($from,$to) use (&$copyTree) {
    if (!is_dir($to)) mkdir($to,0775,true);
    foreach (new DirectoryIterator($from) as $entry) {
        if ($entry->isDot()) continue;
        if ($entry->isDir()) $copyTree($entry->getPathname(),$to.'/'.$entry->getFilename());
        else copy($entry->getPathname(),$to.'/'.$entry->getFilename());
    }
};
$pdf = $root.'/node_modules/pdfjs-dist';
if (is_dir($pdf)) {
    if (!is_dir($theme.'/assets/pdfjs')) mkdir($theme.'/assets/pdfjs',0775,true);
    foreach (['pdf.min.mjs','pdf.worker.min.mjs'] as $file) copy($pdf.'/build/'.$file,$theme.'/assets/pdfjs/'.$file);
    foreach (['cmaps','standard_fonts','wasm'] as $dir) if (is_dir($pdf.'/'.$dir)) $copyTree($pdf.'/'.$dir,$theme.'/assets/pdfjs/'.$dir);
    copy($pdf.'/LICENSE',$theme.'/assets/pdfjs/LICENSE');
}
echo 'Templates PHP et ressources copiés depuis Laravel.'.PHP_EOL;
$pageTemplates=['home'=>'Accueil','about'=>'Organisation','work'=>'Domaines d’intervention','activities'=>'Activités','news'=>'Actualités','resources'=>'Ressources','impact'=>'Impact','success'=>'Nos succès','partnership'=>'Devenir partenaire','donation'=>'Faire un don','contact'=>'Contact'];
if(!is_dir($theme.'/templates'))mkdir($theme.'/templates',0775,true);
foreach($pageTemplates as$view=>$label)file_put_contents($theme.'/templates/'.$view.'.php',"<?php\n/** Template Name: ADPDH — ".$label." */\ndefined('ABSPATH') || exit;\nrequire dirname(__DIR__).'/index.php';\n");
foreach(['front-page','page','single-adpdh_activite','single-adpdh_actualite','single-adpdh_ressource','404','search']as$name)file_put_contents($theme.'/'.$name.'.php',"<?php\ndefined('ABSPATH') || exit;\nrequire __DIR__.'/index.php';\n");
