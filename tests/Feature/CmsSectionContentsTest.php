<?php
use App\Models\{CmsPage,CmsSectionContent,User};
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
beforeEach(function(){$this->seed(\Database\Seeders\DatabaseSeeder::class);});
test('eight pillars render independently of their heading and removed items stay removed after seeding',function(){
 $this->actingAs(User::factory()->create(['is_admin'=>true]));
 $section=CmsPage::where('key','index')->firstOrFail()->sections()->where('key','piliers')->firstOrFail();
 expect($section->contents()->count())->toBe(4);$heading=$section->title;
 for($i=5;$i<=8;$i++)$this->post(route('admin.cms.sections.store',$section),['title'=>'Pilier '.$i,'description'=>'Contenu évolutif','sort_order'=>$i+100,'is_visible'=>1,'is_demo'=>0])->assertSessionHasNoErrors();
 expect($section->contents()->count())->toBe(8);expect($section->fresh()->title)->toBe($heading);
 $this->get('/')->assertOk()->assertSee('Pilier 8');
 $item=$section->contents()->first();$this->delete(route('admin.cms.sections.destroy',[$section,$item]),['version'=>$item->version])->assertSessionHasNoErrors();
 $this->seed(\Database\Seeders\HomeContentsSeeder::class);expect($section->contents()->count())->toBe(7);expect(CmsSectionContent::withTrashed()->find($item->id)->trashed())->toBeTrue();
});
test('new management separates headings and items with ownership and concurrency checks',function(){
 $this->get(route('admin.cms.home'))->assertRedirect('/login');$this->actingAs(User::factory()->create())->get(route('admin.cms.home'))->assertForbidden();
 $this->actingAs(User::factory()->create(['is_admin'=>true]))->withoutVite()->get(route('admin.cms.home'))->assertOk()->assertSee('Gérer les éléments');
 $section=CmsPage::where('key','index')->firstOrFail()->sections()->where('key','piliers')->firstOrFail();$item=$section->contents()->first();
 $this->get(route('admin.cms.sections.edit',$section).'?tab=titles')->assertOk();$this->get(route('admin.cms.sections.edit',$section))->assertOk();$this->get(route('admin.cms.sections.content.edit',[$section,$item]))->assertOk();
 $data=['version'=>$item->version,'title'=>'<script>unsafe</script>','sort_order'=>1,'is_visible'=>1,'is_demo'=>0];
 $this->put(route('admin.cms.sections.update',[$section,$item]),$data)->assertSessionHasNoErrors();
 $this->get('/')->assertSee('&lt;script&gt;unsafe&lt;/script&gt;',false)->assertDontSee('<script>unsafe</script>',false);
 $this->put(route('admin.cms.sections.update',[$section,$item]),$data)->assertSessionHasErrors('version');
 $other=CmsPage::where('key','index')->firstOrFail()->sections()->where('key','activites')->firstOrFail();
 $this->put(route('admin.cms.sections.update',[$other,$item]),$data)->assertNotFound();
 $data['version']=$item->fresh()->version;$data['link_url']='javascript:alert(1)';$this->put(route('admin.cms.sections.update',[$section,$item]),$data)->assertSessionHasErrors('link_url');
});
test('additional indicators have no hardcoded key filter and demo records are hidden',function(){
 $this->actingAs(User::factory()->create(['is_admin'=>true]));$section=CmsPage::where('key','index')->firstOrFail()->sections()->where('key','impact')->firstOrFail();
 $this->post(route('admin.cms.sections.indicator.create',$section),['title'=>'Nouveau résultat','unit'=>'count','value'=>250,'source'=>'Rapport validé'])->assertSessionHasNoErrors();
 $this->get('/')->assertSee('Nouveau résultat')->assertSee('250')->assertDontSee('Parcours illustratif')->assertDontSee('Une journée pour apprendre à gérer son activité');
});
