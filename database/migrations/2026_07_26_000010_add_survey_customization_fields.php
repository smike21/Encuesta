<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->string('welcome_title')->nullable()->after('description');
            $table->text('welcome_text')->nullable()->after('welcome_title');
            $table->string('thank_you_title')->nullable()->after('welcome_text');
            $table->text('thank_you_text')->nullable()->after('thank_you_title');
            $table->string('primary_color', 20)->nullable()->after('thank_you_text');
            $table->string('background_color', 20)->nullable()->after('primary_color');
            $table->string('text_color', 20)->nullable()->after('background_color');
            $table->string('button_text', 100)->nullable()->after('text_color');
            $table->boolean('show_title')->default(true)->after('button_text');
            $table->boolean('show_description')->default(true)->after('show_title');
            $table->boolean('show_progress')->default(true)->after('show_description');
            $table->boolean('show_submit_button')->default(true)->after('show_progress');
        });
    }

    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn([
                'welcome_title', 'welcome_text', 'thank_you_title', 'thank_you_text',
                'primary_color', 'background_color', 'text_color', 'button_text',
                'show_title', 'show_description', 'show_progress', 'show_submit_button'
            ]);
        });
    }
};
