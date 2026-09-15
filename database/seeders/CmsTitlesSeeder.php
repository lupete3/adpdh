<?php
namespace Database\Seeders;
use App\Models\CmsPage;
use Illuminate\Database\Seeder;
class CmsTitlesSeeder extends Seeder {
 public function run(): void {
  $manifest = json_decode(file_get_contents(database_path('content/adpdh-titles.json')), true, 512, JSON_THROW_ON_ERROR);
  foreach ($manifest as $item) {
   $page = CmsPage::firstOrCreate(['key'=>$item['key']], ['label'=>$item['label']]);
   foreach ($item['titles'] as $order=>$title) {
    $page->titles()->firstOrCreate(['key'=>$title['key']], ['label'=>$title['label'],'value'=>$title['value'],'sort_order'=>$order]);
   }
  }
 }
}
