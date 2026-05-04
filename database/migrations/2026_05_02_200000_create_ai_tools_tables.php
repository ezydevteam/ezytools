<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // AI Voices table
        Schema::create('ai_voices', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 30); // elevenlabs, openai, google
            $table->string('provider_voice_id', 100);
            $table->string('name', 100);
            $table->string('language', 20); // bangla, english, hindi, arabic, urdu
            $table->string('gender', 10); // male, female
            $table->string('accent', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_pro_only')->default(false);
            $table->string('preview_url')->nullable();
            $table->timestamps();
        });

        // AI Voice Jobs table
        Schema::create('ai_voice_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45);
            $table->text('text_input');
            $table->string('language', 20);
            $table->foreignId('voice_id')->constrained('ai_voices')->cascadeOnDelete();
            $table->decimal('speed', 3, 2)->default(1.00);
            $table->decimal('pitch', 3, 2)->default(1.00);
            $table->string('output_path')->nullable();
            $table->unsignedInteger('file_size')->default(0);
            $table->decimal('duration_seconds', 6, 2)->default(0);
            $table->string('status', 20)->default('pending'); // pending, processing, done, failed
            $table->string('provider', 30);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });

        // AI Detection Results table
        Schema::create('ai_detection_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_usage_id')->nullable()->constrained('tool_usages')->nullOnDelete();
            $table->text('input_text');
            $table->unsignedInteger('input_length');
            $table->string('language_detected', 20); // bangla, english, mixed
            $table->decimal('overall_score', 5, 2)->default(0);
            $table->string('verdict', 10); // human, mixed, ai
            $table->json('sentence_scores')->nullable();
            $table->decimal('burstiness_score', 5, 2)->default(0);
            $table->decimal('perplexity_score', 5, 2)->default(0);
            $table->string('provider_used', 20)->default('internal');
            $table->unsignedInteger('processing_ms')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('verdict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_detection_results');
        Schema::dropIfExists('ai_voice_jobs');
        Schema::dropIfExists('ai_voices');
    }
};
