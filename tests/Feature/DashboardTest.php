<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('administrators can visit the dashboard', function () {
    $this->withoutVite();
    $user = User::factory()->create(['is_admin'=>true]);
    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertStatus(200);
});

test('dashboard counts current published content and separates drafts schedules and legacy data', function () {
    $this->withoutVite();
    $admin = User::factory()->create(['is_admin' => true]);
    foreach ([\App\Models\Post::class => 'status', \App\Models\Project::class => 'publication_state'] as $model => $state) {
        foreach (['live', 'draft', 'future', 'demo', 'legacy', 'undated'] as $kind) {
            $data = ['title' => 'Record '.$kind, 'cms_key' => $kind === 'legacy' ? null : $kind, 'slug' => $kind, $state => $kind === 'draft' ? 'draft' : 'published', 'is_demo' => $kind === 'demo', 'published_at' => $kind === 'undated' ? null : ($kind === 'future' ? now()->addDay() : now()->subDay())];
            if ($model === \App\Models\Post::class) $data += ['user_id' => $admin->id, 'content' => 'Contenu', 'category' => 'Communauté'];
            $model::create($data);
        }
    }
    $indicator = \App\Models\Indicator::create(['key' => 'visible', 'title' => 'Personnes accompagnées', 'unit' => 'count', 'is_visible' => true]);
    $indicator->values()->create(['value' => 125, 'source' => 'Rapport', 'period_label' => '2026']);
    \App\Models\Indicator::create(['key' => 'empty', 'title' => 'Sans valeur', 'unit' => 'count', 'is_visible' => true]);
    $data = app(\App\Services\Cms\DashboardSummary::class)->data();
    $cards = collect($data['cards'])->keyBy('route');
    expect($cards['admin.cms.news']['count'])->toBe(1);
    expect($cards['admin.cms.activities']['count'])->toBe(2);
    expect($cards['admin.cms.news']['detail'])->toBe('1 brouillon(s) · 1 programmée(s)');
    expect($cards['admin.cms.activities']['detail'])->toBe('1 brouillon(s) · 1 programmée(s)');
    expect($cards['admin.cms.impact']['count'])->toBe(1);
    $this->actingAs($admin)->get('/dashboard')->assertOk()->assertSee('Personnes accompagnées')->assertSee('125')->assertSee(route('admin.cms.news'), false)->assertSee('Modifier la page de don')->assertDontSee('Record legacy')->assertDontSee('Record demo');
    $this->actingAs(User::factory()->create())->get('/dashboard')->assertForbidden();
});
