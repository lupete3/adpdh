<?php
/** Export read-only from the current Laravel database. No users, passwords or inboxes. */
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$root = dirname(__DIR__, 2);
$out = $root.'/wordpress-private/migration';
if (!is_dir($out)) mkdir($out, 0700, true);
$data = ['version' => 1, 'exported_at' => date(DATE_ATOM), 'source' => 'current-laravel'];
$models = [
    'pages' => [App\Models\CmsPage::class, ['sections.contents','titles']],
    'activities' => [App\Models\Project::class, ['steps','gallery.items','axes']],
    'news' => [App\Models\Post::class, []],
    'resources' => [App\Models\Publication::class, []],
    'team' => [App\Models\TeamMember::class, []],
    'indicators' => [App\Models\Indicator::class, ['values']],
    'pillars' => [App\Models\Pillar::class, ['axes']],
    'history' => [App\Models\HistoryEvent::class, []],
    'values' => [App\Models\OrganizationValue::class, []],
    'zones' => [App\Models\InterventionZone::class, []],
    'partnerships' => [App\Models\PartnershipType::class, []],
    'media' => [App\Models\MediaAsset::class, []],
];
foreach ($models as $key => [$model,$relations]) {
    $query = $model::query()->with($relations);
    if (in_array($key, ['activities','news','resources','team'],true)) $query->whereNotNull('cms_key');
    $data[$key] = $query->get()->toArray();
}
$data['settings'] = Illuminate\Support\Facades\DB::table('settings')->where('key','like','adpdh.%')->pluck('value','key')->all();
$data['donation'] = App\Http\Controllers\CmsDonationController::DEFAULTS;
foreach ($data['donation'] as $key => &$value) $value = $data['settings']['adpdh.donation.'.$key] ?? $value;
unset($value);
$data['partnership_copy'] = App\Http\Controllers\CmsPartnershipController::TEXTS;
foreach ($data['partnership_copy'] as $key => &$value) $value = $data['settings']['adpdh.partnership.'.$key] ?? $value;
unset($value);
$count = 0;
foreach ($data['media'] as &$media) {
    $source = $media['disk'] === 'builtin' ? public_path($media['path']) : Illuminate\Support\Facades\Storage::disk($media['disk'])->path($media['path']);
    $media['export_file'] = null;
    if (is_file($source)) {
        $relative = 'files/'.$media['id'].'-'.basename($source);
        if (!is_dir($out.'/files')) mkdir($out.'/files',0700,true);
        copy($source, $out.'/'.$relative);
        $media['export_file'] = $relative;
        $count++;
    }
}
unset($media);
// Current public assets can be recorded on the public disk without a stored upload.
$pdf = $data['settings']['adpdh.partnership.pdf'] ?? '';
if ($pdf && str_starts_with($pdf,'partnership/') && !str_contains($pdf,'..')) {
    $source = Illuminate\Support\Facades\Storage::disk('local')->path($pdf);
    if (is_file($source)) { copy($source,$out.'/partnership.pdf'); $data['partnership_file'] = 'partnership.pdf'; }
}
file_put_contents($out.'/content.json', json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR));
echo json_encode(['pages'=>count($data['pages']),'activities'=>count($data['activities']),'news'=>count($data['news']),'resources'=>count($data['resources']),'team'=>count($data['team']),'media_files'=>$count],JSON_UNESCAPED_UNICODE).PHP_EOL;
