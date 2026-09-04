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
        Schema::table('why_us', function (Blueprint $table) {
            $table->string('button1_text')->nullable()->after('intro_highlights');
            $table->string('button1_url')->nullable()->after('button1_text');
            $table->string('button2_text')->nullable()->after('button1_url');
            $table->string('button2_url')->nullable()->after('button2_text');
            $table->string('banner_button_text')->nullable()->after('assurance_description');
            $table->string('banner_button_url')->nullable()->after('banner_button_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('why_us', function (Blueprint $table) {
            $table->dropColumn(['button1_text', 'button1_url', 'button2_text', 'button2_url', 'banner_button_text', 'banner_button_url']);
        });
    }
};
