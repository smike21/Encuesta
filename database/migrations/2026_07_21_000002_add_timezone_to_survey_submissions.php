<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('survey_submissions', function (Blueprint $table) {
            $table->string('timezone', 100)->nullable()->after('longitude');
            $table->string('locale', 20)->nullable()->after('timezone');
        });
    }

    public function down(): void
    {
        Schema::table('survey_submissions', function (Blueprint $table) {
            $table->dropColumn(['timezone', 'locale']);
        });
    }
};
