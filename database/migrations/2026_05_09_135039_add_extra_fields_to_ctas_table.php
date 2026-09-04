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
        Schema::table('ctas', function (Blueprint $table) {
            $table->string('badge_1_icon')->nullable()->after('image');
            $table->string('badge_1_title')->nullable()->after('badge_1_icon');
            $table->string('badge_1_subtitle')->nullable()->after('badge_1_title');
            $table->string('badge_2_icon')->nullable()->after('badge_1_subtitle');
            $table->string('badge_2_title')->nullable()->after('badge_2_icon');
            $table->string('badge_2_subtitle')->nullable()->after('badge_2_title');
            $table->string('button2_text')->nullable()->after('button_url');
            $table->string('button2_url')->nullable()->after('button2_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ctas', function (Blueprint $table) {
            $table->dropColumn(['badge_1_icon', 'badge_1_title', 'badge_1_subtitle', 'badge_2_icon', 'badge_2_title', 'badge_2_subtitle', 'button2_text', 'button2_url']);
        });
    }
};
