<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('abouts', function (Blueprint $table) {
            $table->string('image')->nullable()->after('content');
            $table->string('kicker')->nullable()->after('image');
            $table->string('badge_title')->nullable()->after('kicker');
            $table->string('badge_text')->nullable()->after('badge_title');
            $table->json('metrics')->nullable()->after('badge_text');
            $table->string('button_text')->nullable()->after('metrics');
            $table->string('button_url')->nullable()->after('button_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abouts', function (Blueprint $table) {
            $table->dropColumn(['image', 'kicker', 'badge_title', 'badge_text', 'metrics', 'button_text', 'button_url']);
        });
    }
};
