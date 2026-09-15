<?php

use App\Models\CmsSection;
use App\Models\Indicator;
use App\Models\MediaAsset;
use App\Models\Project;
use App\Models\Publication;
use App\Models\User;
use App\Services\Cms\RecordIndicatorValue;
use Database\Seeders\DatabaseSeeder;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('structured content seeds preserve editorial changes and accounts', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $password = $admin->password;
    $this->seed(DatabaseSeeder::class);
    expect(CmsSection::count())->toBe(43);
    expect(Project::forCms()->count())->toBe(3);
    expect(Indicator::count())->toBe(6);
    $section = CmsSection::first();
    $section->update(['title' => 'Titre choisi par administration']);
    $this->seed(DatabaseSeeder::class);
    expect(CmsSection::count())->toBe(43);
    expect($section->fresh()->title)->toBe('Titre choisi par administration');
    expect($admin->fresh()->password)->toBe($password);
    expect(User::count())->toBe(1);
    expect(\App\Models\IndicatorValue::count())->toBe(6);
    expect(Publication::forCms()->get()->every(fn ($p) => ! $p->canDownload()))->toBeTrue();
});

test('indicator changes allow decreases and preserve source history', function () {
    $indicator = Indicator::create(['key' => 'test', 'title' => 'Participants', 'unit' => 'count']);
    $service = new RecordIndicatorValue;
    $service->handle($indicator, ['value' => 120, 'source' => 'Rapport initial']);
    $service->handle($indicator, ['value' => 115, 'source' => 'Rapport corrigé', 'change_note' => 'Correction de doublons']);
    expect($indicator->values()->count())->toBe(2);
    expect((float) $indicator->fresh()->currentValue->value)->toBe(115.0);
    expect((float) $indicator->values()->orderBy('id')->get()->last()->value)->toBe(120.0);
});

test('percentages require valid bounds and an evidence source', function () {
    $indicator = Indicator::create(['key' => 'rate', 'title' => 'Taux', 'unit' => 'percent']);
    expect(fn () => (new RecordIndicatorValue)->handle($indicator, ['value' => 101, 'source' => 'Rapport']))->toThrow(\Illuminate\Validation\ValidationException::class);
    expect(fn () => (new RecordIndicatorValue)->handle($indicator, ['value' => 90]))->toThrow(\Illuminate\Validation\ValidationException::class);
    expect($indicator->values()->count())->toBe(0);
});

test('private and missing media never receive public urls', function () {
    $asset = new MediaAsset(['path' => 'adpdh/assets/avatar-default.svg', 'disk' => 'builtin', 'visibility' => 'private', 'publication_allowed' => true]);
    expect($asset->publicUrl())->toBeNull();
    $asset->visibility = 'public';
    expect($asset->publicUrl())->not->toBeNull();
    $asset->is_demo = true;
    expect($asset->publicUrl())->toBeNull();
    $asset->is_demo = false;
    $asset->path = 'missing-file.pdf';
    expect($asset->publicUrl())->toBeNull();
});

test('grouped sections import existing personalized title fragments', function () {
    $this->seed(\Database\Seeders\CmsTitlesSeeder::class);
    $page = \App\Models\CmsPage::where('key', 'index')->firstOrFail();
    $page->titles()->where('key', 'text-8')->update(['value' => 'Nos domaines personnalisés.']);
    $this->seed(\Database\Seeders\StructuredCmsSeeder::class);
    $section = $page->sections()->where('key', 'piliers')->firstOrFail();
    expect($section->title)->toBe('Nos domaines personnalisés.');
    expect($section->title_accent)->toBe('Un même engagement.');
    expect($section->introduction)->toContain('dignité humaine');
    expect(\App\Models\CmsLegacyMapping::where('target_type', \App\Models\CmsSection::class)->where('target_id', $section->id)->exists())->toBeTrue();
});
