<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('cms_pages', function (Blueprint $t) {
      $t->id();
      $t->string('key')->unique();
      $t->string('label');
      $t->unsignedInteger('version')->default(1);
      $t->timestamps();
    });
    Schema::create('cms_titles', function (Blueprint $t) {
      $t->id();
      $t->foreignId('cms_page_id')->constrained('cms_pages')->cascadeOnDelete();
      $t->string('key');
      $t->string('label');
      $t->text('value');
      $t->unsignedInteger('sort_order');
      $t->timestamps();
      $t->unique(['cms_page_id', 'key']);
    });
  }
  public function down(): void
  {
    Schema::dropIfExists('cms_titles');
    Schema::dropIfExists('cms_pages');
  }
};
