<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) { $table->boolean('is_admin')->default(false); });
        Schema::create('surveys', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 200); $table->text('description')->nullable();
            $table->boolean('collect_location')->default(false); $table->boolean('is_active')->default(true); $table->timestamps();
        });
        Schema::create('questions', function (Blueprint $table) {
            $table->id(); $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->string('text', 500); $table->string('type', 30)->default('text'); $table->json('options')->nullable(); $table->unsignedInteger('position')->default(0); $table->timestamps();
        });
        Schema::create('survey_submissions', function (Blueprint $table) {
            $table->id(); $table->foreignId('survey_id')->constrained()->cascadeOnDelete(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable(); $table->decimal('latitude', 10, 7)->nullable(); $table->decimal('longitude', 10, 7)->nullable(); $table->timestamps();
        });
        Schema::create('answers', function (Blueprint $table) {
            $table->id(); $table->foreignId('question_id')->constrained()->cascadeOnDelete(); $table->foreignId('submission_id')->constrained('survey_submissions')->cascadeOnDelete(); $table->text('value'); $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('answers'); Schema::dropIfExists('survey_submissions'); Schema::dropIfExists('questions'); Schema::dropIfExists('surveys');
        Schema::table('users', function (Blueprint $table) { $table->dropColumn('is_admin'); });
    }
};
