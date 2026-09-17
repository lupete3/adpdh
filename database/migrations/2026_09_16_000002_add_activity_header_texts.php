<?php

use App\Models\CmsPage;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $page = CmsPage::where('key', 'activites')->first();
        if (! $page) return;
        $manifest = json_decode(file_get_contents(database_path('content/adpdh-titles.json')), true, 512, JSON_THROW_ON_ERROR);
        foreach ($manifest as $entry) {
            if ($entry['key'] !== 'activites') continue;
            foreach ($entry['titles'] as $order => $title) {
                $page->titles()->firstOrCreate(['key' => $title['key']], ['label' => $title['label'], 'value' => $title['value'], 'sort_order' => $order]);
            }
        }
    }

    public function down(): void
    {
        CmsPage::where('key', 'activites')->first()?->titles()->whereIn('key', ['introduction', 'list-heading'])->delete();
    }
};
