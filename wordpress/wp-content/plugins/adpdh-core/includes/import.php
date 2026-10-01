<?php
defined('ABSPATH') || exit;

/** Import is CLI-only, additive and idempotent. It never updates an existing editorial record. */
function adpdh_import(string $manifest): array {
    if (PHP_SAPI !== 'cli') throw new RuntimeException('Import réservé à la ligne de commande.');
    $source=json_decode(file_get_contents($manifest),true,512,JSON_THROW_ON_ERROR);
    if (($source['version']??0)!==1||($source['source']??'')!=='current-laravel') throw new RuntimeException('Export Laravel incompatible.');
    require_once ABSPATH.'wp-admin/includes/image.php';
    require_once ABSPATH.'wp-admin/includes/file.php';
    $base=realpath(dirname($manifest));$report=['created'=>0,'preserved'=>0,'images'=>0,'documents'=>0];$created=[];$maps=[];
    $file=function (?string $name) use ($base): ?string {
        if (!$name) return null;$path=realpath($base.'/'.$name);
        return $path&&str_starts_with(wp_normalize_path($path),trailingslashit(wp_normalize_path($base)))&&is_file($path)?$path:null;
    };
    $find=fn($key)=>get_posts(['post_type'=>array_merge(['page','attachment'],array_map(fn($k)=>'adpdh_'.$k,array_keys(adpdh_types()))),'post_status'=>['publish','draft','pending','future','private','inherit','trash'],'numberposts'=>1,'fields'=>'ids','meta_key'=>'_adpdh_source','meta_value'=>$key])[0]??0;
    $insert=function ($key,$type,$title,$row=[]) use (&$report,&$created,$find): int {
        if ($id=(int)$find($key)) {$report['preserved']++;return $id;}
        $args=$row+['post_type'=>$type,'post_title'=>$title,'post_status'=>'publish','post_author'=>get_current_user_id()?:1];
        $id=wp_insert_post(wp_slash($args),true);if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
        update_post_meta($id,'_adpdh_source',$key);$created[$id]=true;$report['created']++;return $id;
    };
    $asset=[];
    foreach ($source['media'] as $m) {
        $asset[$m['id']]=$m;
        $path=$file($m['export_file']??null);
        if (!$path||!str_starts_with($m['mime_type'],'image/')||$m['mime_type']==='image/svg+xml'||$m['visibility']!=='public'||!$m['publication_allowed']||$m['is_demo']) continue;
        $key='media:'.$m['id'];$id=(int)$find($key);
        if (!$id) {
            $uploads=wp_upload_dir();if ($uploads['error']) throw new RuntimeException($uploads['error']);
            $name=wp_unique_filename($uploads['path'],sanitize_file_name(basename($path)));$dest=$uploads['path'].'/'.$name;
            if (!copy($path,$dest)) throw new RuntimeException('Copie du média impossible.');
            $id=wp_insert_attachment(wp_slash(['post_title'=>$m['name'],'post_excerpt'=>$m['caption']??'','post_mime_type'=>$m['mime_type'],'post_status'=>'inherit']),$dest,0,true);
            if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
            update_post_meta($id,'_adpdh_source',$key);update_post_meta($id,'_wp_attachment_image_alt',$m['alt']??'');
            foreach (['credit','source','is_illustration'] as $field) update_post_meta($id,'adpdh_'.$field,$m[$field]??'');
            wp_update_attachment_metadata($id,wp_generate_attachment_metadata($id,$dest));$report['images']++;
        }
        $maps['media'][$m['id']]=$id;
    }
    $visible=fn($row)=>empty($row['is_demo'])&&(!array_key_exists('is_visible',$row)||!empty($row['is_visible']))&&($row['publication_state']??$row['status']??'published')==='published';
    foreach (['activities'=>'activite','news'=>'actualite','resources'=>'ressource','team'=>'equipe','indicators'=>'indicateur'] as $collection=>$type) {
        foreach ($source[$collection] as $r) {
            $status=$type==='indicateur'?(!empty($r['is_visible'])?'publish':'draft'):($visible($r)?'publish':'draft');
            $content=$r['content']??($type==='equipe'?($r['description']??''):'');
            if ($type==='activite'&&!$content) {
                foreach (['objective'=>'Notre objectif','audience'=>'Les personnes accompagnées','results_note'=>'Résultats'] as $f=>$label) if (!empty($r[$f])) $content.='<h2>'.esc_html($label).'</h2><p>'.esc_html($r[$f]).'</p>';
                foreach ($r['body']['blocks']??[] as $block) $content.='<p>'.esc_html($block['text']??'').'</p>';
                foreach ($r['steps']??[] as $step) $content.='<h2>'.esc_html($step['title']).'</h2><p>'.esc_html($step['description']).'</p>';
            }
            $id=$insert($collection.':'.$r['id'],'adpdh_'.$type,$r['title']??$r['name'],[
                'post_status'=>$status,'post_name'=>$r['slug']??$r['cms_key']??$r['key']??'',
                'post_content'=>wp_kses_post($content),'post_excerpt'=>$r['excerpt']??$r['description']??'',
                'post_date'=>gmdate('Y-m-d H:i:s',strtotime($r['published_at']??$r['created_at']??'now')),'menu_order'=>(int)($r['sort_order']??0),
            ]);
            $maps[$collection][$r['id']]=$id;if (empty($created[$id])) continue;
            foreach (adpdh_schema('adpdh_'.$type) as $key=>$field) if (isset($r[$key])) adpdh_set($id,$key,adpdh_clean_field($r[$key],$field));
            $image=$maps['media'][$r['cover_media_id']??$r['photo_media_id']??0]??0;if ($image) set_post_thumbnail($id,$image);
            if ($type==='activite') {
                $gallery=[];foreach ($r['gallery']['items']??[] as $photo) if ($mid=$maps['media'][$photo['media_asset_id']]??0) $gallery[]=['media_id'=>$mid,'alt'=>$photo['alt']??'','caption'=>$photo['caption']??''];
                adpdh_set($id,'gallery',$gallery);
            }
            if (in_array($type,['actualite','ressource'],true)&&!empty($r['category'])) wp_set_object_terms($id,$r['category'],$type==='actualite'?'adpdh_news_cat':'adpdh_doc_cat');
            if ($type==='ressource') {
                $m=$asset[$r['file_media_id']??0]??[];$path=$file($m['export_file']??null);$dir=adpdh_private_dir();
                $allowed=!empty($m['publication_allowed'])&&empty($m['is_demo'])&&($m['mime_type']??'')==='application/pdf';
                adpdh_set($id,'reading_allowed',$allowed&&$path?1:0);
                if ($path&&$dir&&($m['mime_type']??'')==='application/pdf') {
                    wp_mkdir_p($dir);$name=$id.'-'.bin2hex(random_bytes(16)).'.pdf';copy($path,$dir.'/'.$name);update_post_meta($id,'_adpdh_document',$name);$report['documents']++;
                }
            }
        }
    }
    foreach ($source['indicators'] as $r) {
        $id=$maps['indicators'][$r['id']];
        if (!empty($created[$id])) adpdh_set($id,'activity_id',$maps['activities'][$r['project_id']??0]??0);
        foreach ($r['values']??[] as $value) {
            $sample=$insert('indicator_values:'.$value['id'],'adpdh_releve',$r['title'].' — '.($value['period_label']?:'Relevé '.$value['id']),['post_date'=>$value['created_at'],'post_status'=>'publish']);
            if (empty($created[$sample])) continue;
            foreach (adpdh_schema('adpdh_releve') as $key=>$field) if (isset($value[$key])) adpdh_set($sample,$key,adpdh_clean_field($value[$key],$field));
            adpdh_set($sample,'indicator_id',$id);
        }
    }
    $term=function ($r,$parent=0) {
        $exists=term_exists($r['key'],'adpdh_domaine');
        if ($exists) return (int)(is_array($exists)?$exists['term_id']:$exists);
        $created=wp_insert_term($r['title'],'adpdh_domaine',['slug'=>$r['key'],'parent'=>$parent,'description'=>$r['description']??'']);
        if (is_wp_error($created)) throw new RuntimeException($created->get_error_message());
        $id=(int)$created['term_id'];update_term_meta($id,'adpdh_order',(int)$r['sort_order']);update_term_meta($id,'adpdh_hidden',empty($r['is_visible']));return $id;
    };
    foreach ($source['pillars'] as $r) {$parent=$term($r);foreach($r['axes'] as $axis)$maps['axes'][$axis['id']]=$term($axis,$parent);}
    foreach ($source['activities'] as $r) if (!empty($created[$maps['activities'][$r['id']]])) wp_set_object_terms($maps['activities'][$r['id']],array_values(array_filter(array_map(fn($a)=>$maps['axes'][$a['id']]??0,$r['axes']??[]))),'adpdh_domaine');

    $pageViews=['index'=>['home','accueil','Accueil'],'qui-sommes-nous'=>['about','qui-sommes-nous','Qui sommes-nous ?'],'que-faisons-nous'=>['work','que-faisons-nous','Que faisons-nous ?'],'activites'=>['activities','activites','Activités'],'actualites'=>['news','actualites','Actualités'],'ressources'=>['resources','ressources','Ressources'],'impact'=>['impact','notre-impact','Notre impact'],'temoignages'=>['success','nos-succes','Nos succès'],'devenir-partenaire'=>['partnership','devenir-partenaire','Devenir partenaire'],'faire-un-don'=>['donation','faire-un-don','Faire un don'],'contact'=>['contact','contact','Contact']];
    $defaults=is_file(get_template_directory().'/includes/copy-defaults.php')?require get_template_directory().'/includes/copy-defaults.php':[];
    $sourcePages=array_column($source['pages'],null,'key');
    foreach ($pageViews as $key=>[$view,$slug,$label]) {
        $r=$sourcePages[$key]??[];$id=$insert('pages:'.$key,'page',$label,['post_name'=>$slug]);$maps['pages'][$view]=$id;
        if (empty($created[$id])) continue;
        update_post_meta($id,'_adpdh_view',$view);update_post_meta($id,'_adpdh_seo_title',$r['seo_title']??$label.' · ADPDH');
        update_post_meta($id,'_wp_page_template','templates/'.$view.'.php');adpdh_set($id,'seo_title',$r['seo_title']??$label.' · ADPDH');
        adpdh_set($id,'seo_description',$r['seo_description']??'');adpdh_set($id,'titles',array_map(fn($t)=>array_intersect_key($t,array_flip(['key','label','value'])),$r['titles']??[]));
        $sections=[];
        foreach ($r['sections']??[] as $s) {
            if ($s['key']==='footer') {if (!get_option('adpdh_footer'))update_option('adpdh_footer',$s,false);continue;}
            $row=array_intersect_key($s,adpdh_schema('page')['sections']['fields']);$row['media_id']=$maps['media'][$s['media_asset_id']??0]??0;
            $row['body_text']=implode("\n\n",array_column($s['body']['blocks']??[],'text'));$contents=$s['contents']??[];
            if (in_array($s['key'],['activites','actualites'],true)) usort($contents,fn($a,$b)=>[$b['created_at'],$b['id']]<=>[$a['created_at'],$a['id']]);
            $row['contents']=[];
            foreach ($contents as $item) {
                if (!empty($item['is_demo'])) continue;
                $card=array_intersect_key($item,adpdh_schema('page')['sections']['fields']['contents']['fields']);
                $card['media_id']=$maps['media'][$item['media_asset_id']??0]??0;$card['indicator_id']=$maps['indicators'][$item['indicator_id']??0]??0;
                if ($s['key']==='temoignages') {
                    $story=$insert('section_testimonial:'.$item['id'],'adpdh_temoignage',$item['title'],['post_excerpt'=>$item['description'],'post_status'=>$item['is_visible']?'publish':'draft','menu_order'=>(int)$item['sort_order']]);
                    if (!empty($created[$story])) {adpdh_set($story,'subtitle',$item['subtitle']);adpdh_set($story,'publication_allowed',(bool)$item['is_visible']);if ($card['media_id'])set_post_thumbnail($story,$card['media_id']);}
                    $card['record_id']=$story;$card['title']='';$card['description']='';$card['subtitle']='';
                }
                $row['contents'][]=$card;
            }
            $sections[]=$row;
        }
        adpdh_set($id,'sections',$sections);
        $copy=[];$scopes=[$view];
        if ($view==='home') $scopes=array_merge($scopes,['home-sections','home-impact','content-link']);
        if (in_array($view,['about','work'])) $scopes=array_merge($scopes,['about-buttons','about-text','about-image']);
        foreach ($scopes as $scope) foreach ($defaults[$scope]??[] as $k=>$value) $copy[$scope.'.'.$k]=$value;
        if ($view==='about') foreach (['history','values','zones'] as $collection) adpdh_set($id,$collection,array_values(array_filter($source[$collection],fn($r)=>!empty($r['is_visible']))));
        if ($view==='partnership') {adpdh_set($id,'partnerships',array_values(array_filter($source['partnerships'],fn($r)=>!empty($r['is_visible']))));$copy=array_merge($copy,$source['partnership_copy']);}
        if ($view==='donation') $copy=array_merge($copy,array_diff_key($source['donation'],array_flip(['bank','account_name','account_number'])));
        if ($view==='contact') $copy=array_merge($copy,[
            'eyebrow'=>'Contact','heading'=>'Un lien direct','heading_accent'=>'avec ADPDH.','introduction'=>'Une question, une information ou un premier échange : écrivez à notre équipe.',
            'address_title'=>'Nous retrouver','email_title'=>'Nous écrire','phone_title'=>'Nous appeler','partnership_title'=>'Un projet de collaboration ?','partnership_text'=>'Découvrez les différentes formes de soutien.','partnership_link'=>'Devenir partenaire →','form_title'=>'Votre message','form_notice'=>'Votre message sera envoyé à {email}. Les champs ci-dessous sont obligatoires.','name_label'=>'Nom','email_label'=>'E-mail','subject_label'=>'Sujet','message_label'=>'Message','privacy_text'=>'Les informations saisies sont transmises à l’équipe ADPDH pour répondre à votre demande.','privacy_link'=>'Consulter les informations de confidentialité','submit_label'=>'Envoyer le message →',
        ]);
        foreach ($source['settings'] as $k=>$value) if (($view==='impact'&&str_starts_with($k,'adpdh.impact.'))||($view==='success'&&str_starts_with($k,'adpdh.success.'))) $copy[$k]=$value;
        adpdh_set($id,'copy',array_map(fn($key,$value)=>['key'=>$key,'label'=>$key,'value'=>$value],array_keys($copy),array_values($copy)));
    }
    if (!get_option('adpdh_settings')) {
        $settings=array_filter($source['settings'],fn($key)=>str_starts_with($key,'adpdh.contact.')||str_starts_with($key,'adpdh.partnership.email')||str_starts_with($key,'adpdh.partnership.phone'),ARRAY_FILTER_USE_KEY);
        $settings+=['adpdh.brand_name'=>"Action pour le Développement\net la Promotion des Droits Humains",'adpdh.topbar.address'=>'Av P.E Lumumba : Bukavu, Av Galilee : Kinshasa, Q. Namyanda : Uvira  · République démocratique du Congo','adpdh.address.kinshasa'=>"22, avenue Galilee\nCommune Ngaliema, Kinshasa, RD Congo",'adpdh.address.bukavu'=>'305, avenue Patrice Emery Lumumba, Commune d’Ibanda, Bukavu, Sud-Kivu, RD Congo','adpdh.address.uvira'=>'48, quartier Namyanda, Uvira, Sud-Kivu, RD Congo'];
        update_option('adpdh_settings',$settings,false);
    }
    if (!get_option('adpdh_bank')) update_option('adpdh_bank',array_intersect_key($source['donation'],array_flip(['bank','account_name','account_number'])),false);
    if (!get_option('adpdh_interface_copy')) {
        $interface=[];foreach($defaults as $view=>$strings)foreach($strings as $key=>$value)$interface[$view.'.'.$key]=$value;update_option('adpdh_interface_copy',$interface,false);
    }
    if (($path=$file($source['partnership_file']??null))&&adpdh_private_dir()&&!get_option('adpdh_partnership_document')) {
        wp_mkdir_p(adpdh_private_dir());$name='partnership-'.bin2hex(random_bytes(16)).'.pdf';copy($path,adpdh_private_dir().'/'.$name);update_option('adpdh_partnership_document',$name,false);
    }
    if (!get_option('adpdh_import_initialized')) {
        update_option('show_on_front','page');update_option('page_on_front',$maps['pages']['home']);
        adpdh_import_menus($maps['pages'],get_option('adpdh_footer',[]));
        update_option('adpdh_import_initialized',1,false);
    }
    $legacy=get_option('adpdh_legacy_activities',[]);
    foreach($source['activities'] as $r)if(!empty($r['cms_key'])&&!empty($maps['activities'][$r['id']]))$legacy[$r['cms_key']]=$maps['activities'][$r['id']];
    update_option('adpdh_legacy_activities',$legacy,false);
    adpdh_information_page();
    update_option('adpdh_last_import',['date'=>gmdate(DATE_ATOM),'report'=>$report],false);
    flush_rewrite_rules();return $report;
}
function adpdh_import_menus(array $pages,array $footer): void {
    $menu=wp_create_nav_menu('Navigation principale');if (is_wp_error($menu)) return;
    $locations=['primary'=>$menu];
    $add=function ($label,$url,$parent=0,$classes='') use ($menu) {return wp_update_nav_menu_item($menu,0,['menu-item-title'=>$label,'menu-item-url'=>$url,'menu-item-status'=>'publish','menu-item-parent-id'=>$parent,'menu-item-type'=>'custom','menu-item-classes'=>$classes]);};
    $parent=$add('Qui sommes-nous ?',get_permalink($pages['about']),0,'organization-menu');
    foreach ([''=>'Présentation de l’organisation','histoire'=>'Notre histoire','vision-mission'=>'Vision et mission','valeurs'=>'Nos valeurs','statut'=>'Statut juridique','zones'=>'Zones d’intervention','equipe'=>'Notre équipe'] as $anchor=>$label) $add($label,get_permalink($pages['about']).($anchor?'#'.$anchor:''),$parent);
    $add('Que faisons-nous ?',get_permalink($pages['work']));$add('Activités',get_permalink($pages['activities']));
    $parent=$add('S’informer','#');foreach(['news'=>'Actualités','resources'=>'Nos ressources','success'=>'Nos succès']as$v=>$label)$add($label,get_permalink($pages[$v]),$parent);
    foreach(['impact'=>'Notre impact','partnership'=>'Devenir partenaire','contact'=>'Contact','donation'=>'Faire un don']as$v=>$label)$add($label,get_permalink($pages[$v]),0,$v==='donation'?'adpdh-cta':'');
    $groups=[];foreach($footer['contents']??[]as$item)if($item['is_visible']&&!$item['is_demo'])$groups[$item['subtitle']][]=$item;
    $i=0;foreach($groups as $label=>$items){$i++;if($i>3)break;$id=wp_create_nav_menu($label);if(is_wp_error($id))continue;$locations['footer_'.$i]=$id;
        foreach($items as $item)wp_update_nav_menu_item($id,0,['menu-item-title'=>$item['title'],'menu-item-url'=>ADPDH\Theme\adpdh_url($item['link_url']),'menu-item-status'=>'publish','menu-item-type'=>'custom']);
    }
    set_theme_mod('nav_menu_locations',$locations);
}
