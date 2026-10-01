<?php
namespace ADPDH\Theme;
defined('ABSPATH') || exit;
function current_view(): string {
    if (is_front_page()) return 'home';
    if (is_singular('adpdh_activite')) return 'activity';
    if (is_singular('adpdh_actualite')) return 'news-article';
    if (is_singular('adpdh_ressource')) return 'resource';
    $template=get_post_meta(get_queried_object_id(),'_wp_page_template',true);
    if ($template==='default') return 'standard';
    if (str_starts_with($template,'templates/')) return basename($template,'.php');
    return get_post_meta(get_queried_object_id(),'_adpdh_view',true) ?: 'standard';
}
function media(int|string|null $id): ?Media {
    $id=(int)$id;if (!$id||get_post_type($id)!=='attachment') return null;
    $url=wp_get_original_image_url($id) ?: wp_get_attachment_url($id);if (!$url) return null;
    $size=wp_get_attachment_metadata($id);
    return new Media(['id'=>$id,'url'=>$url,'alt'=>get_post_meta($id,'_wp_attachment_image_alt',true),'caption'=>wp_get_attachment_caption($id),'width'=>$size['width']??0,'height'=>$size['height']??0]);
}
function record(int $id): ?Record {
    $p=get_post($id);if (!$p) return null;
    $d=[];foreach (\adpdh_schema($p->post_type) as $key=>$field) $d[$key]=\adpdh_get($id,$key,$field['default']??'');
    $d+=['id'=>$id,'title'=>$p->post_title,'name'=>$p->post_title,'slug'=>$p->post_name,'content'=>$p->post_content,'description'=>$p->post_excerpt,'excerpt'=>$p->post_excerpt,'is_visible'=>$p->post_status==='publish','is_demo'=>false,'sort_order'=>$p->menu_order];
    $d['created_at']=new Date($p->post_date);$d['published_at']=new Date($p->post_date);
    $d['cover']=$d['portrait']=$d['media']=media(get_post_thumbnail_id($id));
    $d['steps']=new Collection();$d['body']=[];
    if ($p->post_type==='adpdh_equipe') $d['description']=wp_strip_all_tags($p->post_content);
    if ($p->post_type==='adpdh_temoignage') $d['description']=$p->post_excerpt?:wp_strip_all_tags($p->post_content);
    $tax=['adpdh_actualite'=>'adpdh_news_cat','adpdh_ressource'=>'adpdh_doc_cat'][$p->post_type]??null;
    if ($tax) {$terms=wp_get_post_terms($id,$tax,['fields'=>'names']);$d['category']=is_wp_error($terms)?'':implode(', ',$terms);}
    if ($p->post_type==='adpdh_activite') {
        $items=[];foreach ((array)$d['gallery'] as $row) $items[]=new Record($row+['media'=>media($row['media_id']??0)]);
        $d['gallery']=new Record(['items'=>new Collection($items)]);
    }
    if ($p->post_type==='adpdh_indicateur') {
        $samples=get_posts(['post_type'=>'adpdh_releve','post_status'=>'publish','numberposts'=>1,'orderby'=>'ID','order'=>'DESC','meta_key'=>'adpdh_indicator_id','meta_value'=>$id]);
        $d['currentValue']=$samples?record($samples[0]->ID):null;
    }
    if ($p->post_type==='adpdh_releve' && ($d['period_label']??'')==='') $d['period_label']=null;
    return new Record($d);
}
function records(string $type,array $extra=[]): Collection {
    return new Collection(array_map(fn($p)=>record($p->ID),get_posts($extra+['post_type'=>'adpdh_'.$type,'post_status'=>'publish','numberposts'=>-1,'orderby'=>['menu_order'=>'ASC','ID'=>'ASC'],'suppress_filters'=>false])));
}
function sections(int $id): Collection {
    $out=[];
    foreach ((array)\adpdh_get($id,'sections',[]) as $row) {
        $items=[];
        foreach ((array)($row['contents']??[]) as $n=>$item) {
            $linked=!empty($item['record_id'])&&get_post_status($item['record_id'])==='publish'?record((int)$item['record_id']):null;
            if (!empty($item['record_id'])&&!$linked) continue;
            $defaults=$linked?$linked->data:[];
            foreach (['title','description','subtitle','media_id'] as $key) if (isset($item[$key])&&$item[$key]===''&&isset($defaults[$key])) unset($item[$key]);
            $item=$item+$defaults;
            $item['id']=$n;$item['is_demo']=false;$item['created_at']=new Date('2000-01-01');
            $item['media']=media($item['media_id']??0)??$linked?->media;
            $indicator=(int)($item['indicator_id']??0);$item['indicator']=$indicator&&get_post_status($indicator)==='publish'?record($indicator):null;
            if ($linked && empty($item['link_url']) && get_post_type_object(get_post_type($linked->id))->publicly_queryable) $item['link_url']=get_permalink($linked->id);
            $items[]=new Record($item);
        }
        $row['contents']=new Collection($items);$row['media']=media($row['media_id']??0);
        $row['body']=['blocks'=>array_map(fn($text)=>['text'=>$text],preg_split('/\R\s*\R/',trim($row['body_text']??''),-1,PREG_SPLIT_NO_EMPTY))];
        $out[]=new Record($row);
    }
    return new Collection($out);
}
function page_id(string $view): int {
    $ids=get_posts(['post_type'=>'page','numberposts'=>1,'fields'=>'ids','meta_key'=>'_adpdh_view','meta_value'=>$view]);return (int)($ids[0]??0);
}
function paginator(string $type): Paginator {
    $page=max(1,(int)request('pg'));
    $args=['post_type'=>'adpdh_'.$type,'post_status'=>'publish','posts_per_page'=>6,'paged'=>$page,'orderby'=>['date'=>'DESC','ID'=>'DESC']];
    if ($type==='ressource') {
        $args['s']=mb_substr((string)request('q'),0,200);
        $args['meta_query']=[['key'=>'adpdh_availability','value'=>'available'],['key'=>'adpdh_reading_allowed','value'=>1]];
        if (request('category')) $args['tax_query']=[['taxonomy'=>'adpdh_doc_cat','field'=>'name','terms'=>request('category')]];
    }
    $q=new \WP_Query($args);
    return new Paginator(array_map(fn($p)=>record($p->ID),$q->posts),(int)$q->found_posts,$page);
}
function context(): array {
    $id=get_queried_object_id();$view=current_view();$p=get_post($id);
    $settings=get_option('adpdh_settings',[]);$copy=\adpdh_copy_map($id);
    foreach ($copy as $key=>$value) if (str_starts_with($key,'adpdh.')) $settings[$key]=$value;
    $titles=[];foreach ((array)\adpdh_get($id,'titles',[]) as $row) $titles[$row['key']]=$row['value'];
    $data=['page'=>new Record(['seo_title'=>get_post_meta($id,'_adpdh_seo_title',true)?:($p->post_title??''),'seo_description'=>\adpdh_get($id,'seo_description','')]),'settings'=>$settings,'copy'=>$copy,'titles'=>$titles];
    $data['sections']=sections($id);
    if (in_array($view,['about','work'])) $data['sections']=$data['sections']->keyBy('key');
    if ($view==='about') {
        $data['collections']=['equipe'=>records('equipe')];
        foreach (['histoire'=>'history','valeurs'=>'values','zones'=>'zones'] as $name=>$field) $data['collections'][$name]=new Collection(array_map(fn($r)=>new Record($r),(array)\adpdh_get($id,$field,[])));
    }
    if ($view==='work') {
        $pillars=[];$terms=get_terms(['taxonomy'=>'adpdh_domaine','hide_empty'=>false,'parent'=>0]);
        if (!is_wp_error($terms)) foreach ($terms as $term) {
            if (get_term_meta($term->term_id,'adpdh_hidden',true)) continue;
            $axes=[];$children=get_terms(['taxonomy'=>'adpdh_domaine','hide_empty'=>false,'parent'=>$term->term_id]);
            if (!is_wp_error($children)) foreach ($children as $axis) {
                if (!get_term_meta($axis->term_id,'adpdh_hidden',true)) $axes[]=new Record(['key'=>$axis->slug,'title'=>$axis->name,'description'=>$axis->description,'sort_order'=>(int)get_term_meta($axis->term_id,'adpdh_order',true)]);
            }
            usort($axes,fn($a,$b)=>$a->sort_order<=>$b->sort_order);
            $pillars[]=new Record(['key'=>$term->slug,'title'=>$term->name,'description'=>$term->description,'axes'=>new Collection($axes),'sort_order'=>(int)get_term_meta($term->term_id,'adpdh_order',true)]);
        }
        usort($pillars,fn($a,$b)=>$a->sort_order<=>$b->sort_order);$data['pillars']=new Collection($pillars);
        $words=['aucun','un','deux','trois','quatre','cinq','six','sept','huit','neuf','dix','onze','douze','treize','quatorze','quinze','seize'];
        $count=fn($n,$s,$plural)=>($words[$n]??$n).' '.($n<=1?$s:$plural);
        $pl=$count(count($pillars),'pilier','piliers');$ax=$count($data['pillars']->sum(fn($p)=>$p->axes->count()),'axe','axes');
        $tokens=['{complementaires}'=>count($pillars)<=1?'complémentaire':'complémentaires','{piliers}'=>$pl,'{Piliers}'=>mb_strtoupper(mb_substr($pl,0,1)).mb_substr($pl,1),'{axes}'=>$ax,'{Axes}'=>mb_strtoupper(mb_substr($ax,0,1)).mb_substr($ax,1)];
        $data['text']=fn($text)=>strtr($text??'',$tokens);
    }
    if ($view==='activities') $data['activities']=paginator('activite');
    if ($view==='news') $data['news']=paginator('actualite');
    if ($view==='resources') {
        $data['resources']=paginator('ressource');
        $available=get_posts(['post_type'=>'adpdh_ressource','post_status'=>'publish','numberposts'=>-1,'fields'=>'ids','meta_query'=>[['key'=>'adpdh_availability','value'=>'available'],['key'=>'adpdh_reading_allowed','value'=>1]]]);
        $terms=$available?wp_get_object_terms($available,'adpdh_doc_cat',['fields'=>'names']):[];
        $data['categories']=new Collection(is_wp_error($terms)?[]:array_values(array_unique($terms)));
    }
    foreach (['activity'=>'activity','news-article'=>'post','resource'=>'resource'] as $v=>$key) if ($view===$v) $data[$key]=record($id);
    if ($view==='impact') $data['indicators']=records('indicateur')->filter(fn($r)=>$r->currentValue);
    if ($view==='success') {
        $data['testimonials']=records('temoignage',['meta_query'=>[['key'=>'adpdh_publication_allowed','value'=>1]]]);
        $data['networks']=['facebook'=>'Facebook','x'=>'X','linkedin'=>'LinkedIn','youtube'=>'YouTube','instagram'=>'Instagram','tiktok'=>'TikTok'];
    }
    if ($view==='partnership') {
        $data['reasons']=new Collection(array_map(fn($r)=>new Record($r),(array)\adpdh_get($id,'partnerships',[])));
        $data['pdfAvailable']=\adpdh_document_path('partnership')!==null;
    }
    if ($view==='donation') $data['copy']=array_merge($copy,get_option('adpdh_bank',[]));
    return $data;
}
