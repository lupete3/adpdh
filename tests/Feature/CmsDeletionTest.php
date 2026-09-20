<?php

use App\Http\Controllers\{CmsActivityController, CmsNewsController};
use App\Models\{Gallery, Indicator, MediaAsset, Post, Project, User};
use Illuminate\Support\Facades\Storage;

test('cms deletion requires administration and a current revision and preserves media', function ($kind, $model, $controller) {
    $this->withoutVite();
    Storage::fake('public');
    Storage::disk('public')->put('shared.jpg', 'image');
    $media = MediaAsset::create(['key' => 'shared', 'name' => 'shared.jpg', 'kind' => 'image', 'disk' => 'public', 'path' => 'shared.jpg', 'mime_type' => 'image/jpeg']);
    $admin = User::factory()->create(['is_admin' => true]);
    $attributes = ['title' => 'Contenu à supprimer', 'cms_key' => 'delete-me', 'slug' => 'delete-me', 'cover_media_id' => $media->id];
    if ($model === Post::class) $attributes += ['user_id' => $admin->id, 'content' => 'Texte', 'category' => 'Communauté'];
    $record = $model::create($attributes);
    $legacy = $model::create(array_merge($attributes, ['cms_key' => null, 'slug' => 'legacy']));
    if ($model === Project::class) {
        $gallery = Gallery::create(['key' => 'shared-gallery', 'title' => 'Galerie']);
        $gallery->items()->create(['media_asset_id' => $media->id]);
        $record->update(['gallery_id' => $gallery->id]);
        $legacy->update(['gallery_id' => $gallery->id]);
        $record->steps()->create(['key' => 'step', 'title' => 'Étape', 'description' => 'Description', 'sort_order' => 0]);
        $indicator = Indicator::create(['key' => 'retained-impact', 'title' => 'Résultat', 'unit' => 'count', 'project_id' => $record->id]);
    }
    $url = route('admin.cms.'.$kind.'.destroy', $record);
    $data = ['revision' => $controller::revision($record->fresh())];
    $this->delete($url, $data)->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->delete($url, $data)->assertForbidden();
    $this->actingAs($admin);
    $this->delete(route('admin.cms.'.$kind.'.destroy', $legacy), $data)->assertNotFound();
    $this->delete($url)->assertSessionHasErrors('revision');
    $record->update(['title' => 'Titre mis à jour']);
    $this->delete($url, $data)->assertSessionHasErrors('revision');
    expect($record->fresh())->not->toBeNull();
    $this->get(route('admin.cms.'.$kind))->assertOk()->assertSee($url, false)->assertSee('Supprimer');
    $this->get(route('admin.cms.'.$kind.'.edit', $record))->assertOk()->assertSee($url, false);
    $this->delete($url, ['revision' => $controller::revision($record->fresh())])->assertRedirect(route('admin.cms.'.$kind));
    expect($record->fresh())->toBeNull();
    expect($legacy->fresh())->not->toBeNull();
    expect($media->fresh())->not->toBeNull();
    Storage::disk('public')->assertExists('shared.jpg');
    $this->get(route($kind === 'news' ? 'news.show' : 'activities.show', 'delete-me'))->assertNotFound();
    if ($model === Project::class) {
        expect($gallery->fresh()->items)->toHaveCount(1);
        expect($indicator->fresh()->project_id)->toBeNull();
        $this->assertDatabaseMissing('activity_steps', ['project_id' => $record->id]);
    }
})->with([
    ['news', Post::class, CmsNewsController::class],
    ['activities', Project::class, CmsActivityController::class],
]);
