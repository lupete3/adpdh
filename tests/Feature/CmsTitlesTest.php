<?php
use App\Models\User;
use App\Models\CmsPage;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('cms is restricted to administrators including Livewire updates', function () {
 $this->get('/admin/cms/titles')->assertRedirect('/login');
 $user=User::factory()->create();
 $this->actingAs($user)->get('/admin/cms/titles')->assertForbidden();
 $this->get('/dashboard')->assertForbidden();
 expect(\Livewire\Livewire::getPersistentMiddleware())->toContain(\App\Http\Middleware\EnsureAdministrator::class);
});

test('seeding titles is idempotent and preserves accounts and editorial changes', function () {
 $user=User::factory()->create(['email'=>'admin@adpdh.org','password'=>'personal-secret']);
 $hash=$user->password;
 $this->seed(DatabaseSeeder::class);
 $page=CmsPage::first(); $title=$page->titles()->first(); $title->update(['value'=>'Titre personnalisÃƒÂ©']);
 $count=\App\Models\CmsTitle::count();
 $this->seed(DatabaseSeeder::class);
 expect(CmsPage::count())->toBe(18);
 expect(\App\Models\CmsTitle::count())->toBe($count);
 expect($title->fresh()->value)->toBe('Titre personnalisÃƒÂ©');
 expect($user->fresh()->password)->toBe($hash);
 expect($user->fresh()->is_admin)->toBeFalse();
});

test('admin can edit titles and preview escaped text with concurrency control', function () {
 $this->withoutVite();
 $this->seed(DatabaseSeeder::class);
 $admin=User::factory()->create(['is_admin'=>true]);
 $page=CmsPage::where('key','activites')->firstOrFail();
 $values=$page->titles()->pluck('value','id')->all();
 $id=array_key_first($values); $values[$id]='<script>alert(1)</script> Nouveau titre';
 $this->actingAs($admin)->put(route('admin.cms.titles.update',$page),['version'=>1,'titles'=>$values])->assertRedirect();
 $this->get(route('admin.cms.preview',$page))->assertOk()->assertSee('&lt;script&gt;',false)->assertDontSee('<script>alert(1)</script>',false)->assertHeader('X-Robots-Tag','noindex, nofollow');
 $this->put(route('admin.cms.titles.update',$page),['version'=>1,'titles'=>$values])->assertSessionHasErrors('version');
 $this->put(route('admin.cms.titles.update',$page),['version'=>2,'titles'=>[]])->assertSessionHasErrors('titles');
 $this->get(route('admin.cms.titles'))->assertOk();
 $this->get(route('admin.cms.titles.edit',$page))->assertOk();
});

test('draft articles cannot be accessed publicly', function () {
 $user=User::factory()->create();
 $post=\App\Models\Post::create(['title'=>'Brouillon','content'=>'Texte','user_id'=>$user->id,'status'=>'draft','category'=>'Test']);
 $this->get('/blog/'.$post->id)->assertNotFound();
});


test('all eighteen title previews render for the administrator', function () {
 $this->seed(DatabaseSeeder::class);
 $admin=User::factory()->create(['is_admin'=>true]);
 $this->actingAs($admin);
 foreach(CmsPage::all() as $page) $this->get(route('admin.cms.preview',$page))->assertOk();
});
