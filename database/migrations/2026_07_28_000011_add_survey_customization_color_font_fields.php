<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            if (! Schema::hasColumn('surveys', 'container_background_color')) {
                $table->string('container_background_color', 20)->nullable()->after('background_color');
            }
            if (! Schema::hasColumn('surveys', 'container_border_color')) {
                $table->string('container_border_color', 20)->nullable()->after('container_background_color');
            }
            if (! Schema::hasColumn('surveys', 'font_family')) {
                $table->string('font_family')->nullable()->after('button_text');
            }
            if (! Schema::hasColumn('surveys', 'font_size')) {
                $table->string('font_size', 20)->nullable()->after('font_family');
            }
        });
    }

    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            if (Schema::hasColumn('surveys', 'font_size')) {
                $table->dropColumn('font_size');
            }
            if (Schema::hasColumn('surveys', 'font_family')) {
                $table->dropColumn('font_family');
            }
            if (Schema::hasColumn('surveys', 'container_border_color')) {
                $table->dropColumn('container_border_color');
            }
            if (Schema::hasColumn('surveys', 'container_background_color')) {
                $table->dropColumn('container_background_color');
            }
        });
    }
};
