<?php

use App\Http\Controllers\CmsNewsController;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () { $this->withoutVite(); });

function newsData(array $overrides = []): array
{
    return array_merge(['title' => 'Nouvelle du terrain', 'excerpt' => 'Un résumé de notre action.', 'content' => '<h2>Ensemble</h2><p>Notre action.</p>', 'category' => 'Communauté', 'status' => 'published', 'published_at' => now()->subDay()->format('Y-m-d H:i:s')], $overrides);
}

test('news listing paginates and excludes private and legacy records', function () {
    $user = User::factory()->create();
    foreach (range(1, 8) as $n) {
        Post::create(newsData(['title' => 'Actualité numéro '.$n, 'user_id' => $user->id, 'cms_key' => 'news-'.$n, 'slug' => 'news-'.$n, 'published_at' => now()->subDays(10 - $n)]));
    }
    foreach (['draft' => ['status' => 'draft'], 'future' => ['published_at' => now()->addDay()], 'demo' => ['is_demo' => true], 'undated' => ['published_at' => null], 'legacy' => ['cms_key' => null]] as $key => $changes) {
        Post::create(newsData(array_merge(['user_id' => $user->id, 'title' => 'Masqué '.$key, 'cms_key' => $key, 'slug' => $key], $changes)));
        $this->get('/actualites/'.$key)->assertNotFound();
    }
    $response = $this->get('/actualites')->assertOk()->assertSeeInOrder(['Actualité numéro 8', 'Actualité numéro 7', 'Actualité numéro 6'])->assertDontSee('Actualité numéro 2')->assertDontSee('Masqué')->assertSee('Page 1 sur 2');
    $this->get('/actualites?page=2')->assertOk()->assertSee('Actualité numéro 2')->assertSee('Actualité numéro 1');
    $this->get('/actualites/news-8')->assertOk()->assertSee('<h2>Ensemble</h2>', false);
    $this->get('/actualites.html')->assertRedirect('/actualites');
    $this->get('/adpdh/actualites.html')->assertRedirect('/actualites');
    expect(adpdh_url('actualites.html'))->toBe(route('news'));
});

test('news administration saves sanitized content and cover and rejects stale edits', function () {
    Storage::fake('public');
    $this->get(route('admin.cms.news'))->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->post(route('admin.cms.news.store'), newsData())->assertForbidden();
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    $this->get(route('admin.cms.news.create'))->assertOk();
    $this->post(route('admin.cms.news.store'), newsData(['content' => '<p>Texte sûr</p><script>alert(1)</script>', 'cover' => UploadedFile::fake()->image('cover.jpg')]))->assertSessionHasNoErrors()->assertRedirect();
    $post = Post::firstOrFail();
    expect($post->content)->not->toContain('<script');
    Storage::disk('public')->assertExists($post->cover->path);
    $this->get(route('admin.cms.news'))->assertOk()->assertSee($post->title);
    $this->get(route('admin.cms.news.edit', $post))->assertOk()->assertSee('Contenu de l’actualité');
    $this->get(route('news.show', $post->slug))->assertOk()->assertSee($post->cover->publicUrl(), false);
    $revision = CmsNewsController::revision($post);
    $data = newsData(['revision' => $revision, 'status' => 'draft', 'remove_cover' => 1]);
    $this->put(route('admin.cms.news.update', $post), $data)->assertSessionHasNoErrors();
    expect($post->fresh()->cover_media_id)->toBeNull();
    $this->get(route('news.show', $post->slug))->assertNotFound();
    $this->put(route('admin.cms.news.update', $post), $data)->assertSessionHasErrors('revision');
    $this->post(route('admin.cms.news.store'), newsData(['content' => '<p><br></p>']))->assertSessionHasErrors('content');
    $this->post(route('admin.cms.news.store'), newsData(['cover' => UploadedFile::fake()->create('bad.svg', 1, 'image/svg+xml')]))->assertSessionHasErrors('cover');
    $legacy = Post::create(newsData(['user_id' => $post->user_id]));
    $this->get(route('admin.cms.news.edit', $legacy))->assertNotFound();
    $this->put(route('admin.cms.news.update', $legacy), $data)->assertNotFound();
});

test('news empty state contains no fictitious articles', function () {
    $this->get('/actualites')->assertOk()->assertSee('Nos premières actualités seront publiées ici.')->assertDontSee('exemples fictifs');
});
