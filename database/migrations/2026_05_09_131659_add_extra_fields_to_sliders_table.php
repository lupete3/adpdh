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
        Schema::table('sliders', function (Blueprint $table) {
            $table->text('description')->nullable()->after('subtitle');
            $table->json('mini_stats')->nullable()->after('button2_url');
            $table->string('secondary_image')->nullable()->after('image');
            $table->string('floating_badge')->nullable()->after('secondary_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn(['description', 'mini_stats', 'secondary_image', 'floating_badge']);
        });
    }
};
