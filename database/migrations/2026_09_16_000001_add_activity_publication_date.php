<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', fn (Blueprint $table) => $table->timestamp('published_at')->nullable()->index());
        DB::table('projects')->whereNotNull('cms_key')->where('publication_state', 'published')->update(['published_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('projects', fn (Blueprint $table) => $table->dropColumn('published_at'));
    }
};
