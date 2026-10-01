<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;

// Small presentation helpers only. WordPress is the sole CMS and runtime.
class Collection implements \IteratorAggregate, \Countable, \ArrayAccess {
    public function __construct(public array $items = []) {}
    public function getIterator(): \Traversable { return new \ArrayIterator($this->items); }
    public function count(): int { return count($this->items); }
    public function offsetExists(mixed $key): bool { return isset($this->items[$key]); }
    public function offsetGet(mixed $key): mixed { return $this->items[$key] ?? null; }
    public function offsetSet(mixed $key,mixed $value): void { if ($key === null) $this->items[]=$value; else $this->items[$key]=$value; }
    public function offsetUnset(mixed $key): void { unset($this->items[$key]); }
    public function get($key,$default=null): mixed { return $this->items[$key]??$default; }
    public function filter(?callable $fn=null): static { return new static(array_filter($this->items,$fn,ARRAY_FILTER_USE_BOTH)); }
    public function where($key,$value): static { return $this->filter(fn($v)=>($v->$key??null)==$value); }
    public function firstWhere($key,$value): mixed { foreach ($this->items as $v) if (($v->$key??null)==$value) return $v; return null; }
    public function keyBy($key): static { $out=[];foreach($this->items as $v)$out[$v->$key]=$v;return new static($out); }
    public function groupBy($key): static { $out=[];foreach($this->items as $v)$out[$v->$key][]=$v;return new static(array_map(fn($r)=>new static($r),$out)); }
    public function isNotEmpty(): bool { return (bool)$this->items; }
    public function isEmpty(): bool { return !$this->items; }
    public function take(int $n): static { return new static(array_slice($this->items,0,$n)); }
    public function sortByDesc(callable $fn): static { $items=$this->items;uasort($items,fn($a,$b)=>$fn($b)<=>$fn($a));return new static($items); }
    public function sum(callable $fn): int|float { return array_sum(array_map($fn,$this->items)); }
    public function all(): array { return $this->items; }
}
class Paginator extends Collection {
    public function __construct(array $items,public int $totalItems,public int $pageNumber,public int $perPage=6) { parent::__construct($items); }
    public function total(): int { return $this->totalItems; }
    public function currentPage(): int { return $this->pageNumber; }
    public function lastPage(): int { return max(1,(int)ceil($this->totalItems/$this->perPage)); }
    public function hasPages(): bool { return $this->lastPage()>1; }
    public function onFirstPage(): bool { return $this->pageNumber===1; }
    public function hasMorePages(): bool { return $this->pageNumber<$this->lastPage(); }
    public function previousPageUrl(): string { return esc_url(add_query_arg('pg',$this->pageNumber-1)); }
    public function nextPageUrl(): string { return esc_url(add_query_arg('pg',$this->pageNumber+1)); }
}
class Date extends \DateTimeImmutable {
    public function toDateString(): string { return $this->format('Y-m-d'); }
    public function toIso8601String(): string { return $this->format(DATE_ATOM); }
}
class Record {
    public function __construct(public array $data=[]) {}
    public function __get($name): mixed { return $this->data[$name]??null; }
    public function __isset($name): bool { return isset($this->data[$name]); }
    public function href(): string { return adpdh_url($this->data['link_url']??''); }
    public function canDownload(): bool { return \adpdh_document_allowed((int)($this->id??0),true); }
}
class Media extends Record {
    public function publicUrl(): ?string { return $this->data['url']??null; }
    public function isPubliclyAvailable(): bool { return (bool)$this->publicUrl(); }
}
class Str { public static function limit(?string $text,int $limit=100): string { $text=$text??'';return mb_strlen($text)>$limit?mb_substr($text,0,$limit).'...':$text; } }
class Arr { public static function except(array $data,array $keys): array { return array_diff_key($data,array_flip($keys)); } }
class ViewEnvironment {
    private array $loops=[];
    public function addLoop($items): void { $count=is_countable($items)?count($items):0;$this->loops[]=['iteration'=>0,'index'=>-1,'remaining'=>$count,'count'=>$count,'first'=>false,'last'=>false,'depth'=>count($this->loops)+1,'parent'=>$this->getLastLoop()]; }
    public function incrementLoopIndices(): void { $i=array_key_last($this->loops);$l=&$this->loops[$i];$l['iteration']++;$l['index']++;$l['remaining']--;$l['first']=$l['iteration']===1;$l['last']=$l['remaining']===0;$l['even']=$l['iteration']%2===0;$l['odd']=!$l['even']; }
    public function popLoop(): void { array_pop($this->loops); }
    public function getLastLoop(): ?object { return $this->loops?(object)end($this->loops):null; }
    public function make($name,array $data=[],array $scope=[]): RenderedView { return new RenderedView(str_replace('adpdh.','',$name),array_merge($scope,$data)); }
}
class RenderedView {
    public function __construct(private string $name,private array $data) {}
    public function render(): string { return render_view($this->name,$this->data); }
}
function render_view(string $name,array $data=[]): string {
    if (!preg_match('/^[a-z0-9-]+$/D',$name)) return '';
    $file=get_template_directory().'/views/'.$name.'.php';
    if (!is_file($file)) return '';
    extract($data,EXTR_SKIP);$__env=new ViewEnvironment();
    ob_start();include $file;return (string)ob_get_clean();
}
function e(mixed $text): string { return esc_html((string)($text??'')); }
function collect(array|Collection $items=[]): Collection { return $items instanceof Collection?$items:new Collection($items); }
function rich_content(?string $html): string {
    $html=wp_kses_post($html??'');
    // Imported HTML already contains paragraphs. Preserve its source whitespace.
    $preserve=!has_blocks($html)&&preg_match('~<(?:p|h[1-6]|ul|ol)\b~i',$html);
    $priority=has_filter('the_content','wpautop');
    if($preserve&&$priority!==false)remove_filter('the_content','wpautop',$priority);
    try{return apply_filters('the_content',$html);}finally{if($preserve&&$priority!==false)add_filter('the_content','wpautop',$priority);}
}
function asset(string $path): string {
    if ($path==='build/pdfjs') return get_template_directory_uri().'/assets/pdfjs';
    if ($path==='adpdh/mentions-legales.html') return home_url('/mentions-legales/');
    return get_template_directory_uri().'/assets/'.basename($path);
}
function public_path(string $path): string { return get_template_directory().'/assets/'.basename($path); }
function page_image(string $fallback): string { return media(\adpdh_get(get_queried_object_id(),'illustration',0))?->publicUrl()??asset($fallback); }
function adpdh_url(string $url): string {
    if (!$url) return '';
    if (preg_match('~^(?:https?://|mailto:|tel:|#)~i',$url)) return esc_url($url);
    $url=preg_replace('~^/?adpdh/~','/',$url);
    if(preg_match('~^/?actualite-~',$url))return esc_url(home_url('/actualites/'));
    $legacy=\adpdh_legacy_url('/'.ltrim((string)wp_parse_url($url,PHP_URL_PATH),'/'));
    if($legacy)return esc_url($legacy.(wp_parse_url($url,PHP_URL_FRAGMENT)?'#'.wp_parse_url($url,PHP_URL_FRAGMENT):''));
    $url=preg_replace('~\.html(?=[?#]|$)~','',$url);
    $url=preg_replace('~^/?impact(?=[/?#]|$)~','/notre-impact',$url);
    $url=preg_replace('~^/?temoignages(?=[/?#]|$)~','/nos-succes',$url);
    if ($url==='/index'||$url==='index') $url='/';
    return esc_url(home_url('/'.ltrim($url,'/')));
}
function route(string $name,?string $slug=null): string {
    $paths=['home'=>'/','organization'=>'/qui-sommes-nous/','work'=>'/que-faisons-nous/','activities'=>'/activites/','news'=>'/actualites/','resources'=>'/ressources/','impact'=>'/notre-impact/','success'=>'/nos-succes/','partnership'=>'/devenir-partenaire/','donation'=>'/faire-un-don/','contact'=>'/contact/'];
    if ($name==='contact.send') return admin_url('admin-post.php');
    if ($name==='partnership.download') return add_query_arg(['adpdh_document'=>'partnership','download'=>1],home_url('/'));
    [$base,$action]=array_pad(explode('.',$name,2),2,null);
    if (in_array($action,['read','download'],true)) {
        $post=get_page_by_path($slug,OBJECT,'adpdh_ressource');
        return $post?add_query_arg(['adpdh_document'=>$post->ID,'download'=>$action==='download'?1:0],home_url('/')):'';
    }
    return home_url(($paths[$base]??'/').($slug?rawurlencode($slug).'/':''));
}
function request(?string $key=null): mixed {
    if ($key!==null) return isset($_GET[$key])&&is_scalar($_GET[$key])?sanitize_text_field(wp_unslash($_GET[$key])):'';
    return new class { public function routeIs($pattern): bool { $map=['home'=>'home','about'=>'organization','work'=>'work','activities'=>'activities','activity'=>'activities.show','news'=>'news','news-article'=>'news.show','resources'=>'resources','resource'=>'resources.show','success'=>'success','impact'=>'impact','partnership'=>'partnership','donation'=>'donation','contact'=>'contact'];return fnmatch($pattern,$map[current_view()]??''); } };
}
function copy_text(string $view,string $key,string $default=''): string {
    $id=get_queried_object_id();$copy=\adpdh_copy_map($id);
    if (array_key_exists($view.'.'.$key,$copy)) return (string)$copy[$view.'.'.$key];
    $global=get_option('adpdh_interface_copy',[]);
    return (string)($global[$view.'.'.$key]??$default);
}
