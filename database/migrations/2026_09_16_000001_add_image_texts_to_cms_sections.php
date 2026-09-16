<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_sections', function (Blueprint $table) {
            $table->text('image_caption')->nullable();
            $table->string('image_note_title')->nullable();
            $table->string('image_note_text', 500)->nullable();
        });

        $pageId = DB::table('cms_pages')->where('key', 'qui-sommes-nous')->value('id');
        if ($pageId) {
            DB::table('cms_sections')->where('cms_page_id', $pageId)->whereIn('key', ['hero', 'valeurs'])
                ->update(['image_caption' => 'Image d’illustration générée par IA · à remplacer']);
            DB::table('cms_sections')->where('cms_page_id', $pageId)->where('key', 'hero')
                ->update(['image_note_title' => 'Depuis 2010', 'image_note_text' => 'Développement · Protection · Droits humains']);
        }
    }

    public function down(): void
    {
        Schema::table('cms_sections', fn (Blueprint $table) => $table->dropColumn(['image_caption', 'image_note_title', 'image_note_text']));
    }
};
