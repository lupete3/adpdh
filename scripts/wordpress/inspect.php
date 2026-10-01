<?php
// Read-only inspection: never print credentials, account records or message contents.
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$tables = ['cms_pages','cms_sections','cms_section_contents','cms_titles','projects','posts','publications','team_members','indicators','indicator_values','pillars','intervention_axes','history_events','organization_values','intervention_zones','partnership_types','media_assets'];
foreach ($tables as $table) {
    echo $table.': '.Illuminate\Support\Facades\DB::table($table)->count().' | '.implode(',', Illuminate\Support\Facades\Schema::getColumnListing($table)).PHP_EOL;
}
echo 'Public pages: '.json_encode(App\Models\CmsPage::select('key','label')->get(), JSON_UNESCAPED_UNICODE).PHP_EOL;
echo 'Setting keys: '.json_encode(Illuminate\Support\Facades\DB::table('settings')->where('key','like','adpdh.%')->pluck('key'), JSON_UNESCAPED_UNICODE).PHP_EOL;
