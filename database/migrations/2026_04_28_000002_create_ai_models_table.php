<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('ai_providers')->cascadeOnDelete();
            $table->string('name', 100);           // gpt-4o-mini, gemini-2.0-flash
            $table->string('label', 150);           // GPT-4o Mini, Gemini 2.0 Flash
            $table->unsignedInteger('context_window')->default(128000);
            $table->decimal('cost_per_1k_input_tokens', 10, 6)->default(0);
            $table->decimal('cost_per_1k_output_tokens', 10, 6)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['provider_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
