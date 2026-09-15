<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_section_contents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cms_section_id')->constrained()->cascadeOnDelete();
            $t->string('key');
            $t->string('title')->nullable();
            $t->string('subtitle')->nullable();
            $t->text('description')->nullable();
            $t->string('detail_title')->nullable();
            $t->text('detail_text')->nullable();
            $t->string('link_label')->nullable();
            $t->string('link_url', 1000)->nullable();
            $t->foreignId('media_asset_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('indicator_id')->nullable()->constrained()->restrictOnDelete();
            $t->boolean('is_visible')->default(false);
            $t->boolean('is_demo')->default(false);
            $t->unsignedInteger('sort_order')->default(0);
            $t->unsignedInteger('version')->default(1);
            $t->softDeletes();
            $t->timestamps();
            $t->unique(['cms_section_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_section_contents');
    }
};
