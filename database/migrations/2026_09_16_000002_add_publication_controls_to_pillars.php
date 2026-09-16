<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['pillars', 'intervention_axes'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->boolean('is_visible')->default(true);
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach (['pillars', 'intervention_axes'] as $name) {
            Schema::table($name, fn (Blueprint $table) => $table->dropColumn(['is_visible', 'deleted_at']));
        }
    }
};
