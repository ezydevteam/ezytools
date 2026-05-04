<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_tool_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->unique()->constrained('tools')->cascadeOnDelete();

            // Free tier
            $table->foreignId('provider_id')->nullable()->constrained('ai_providers')->nullOnDelete();
            $table->foreignId('model_id')->nullable()->constrained('ai_models')->nullOnDelete();

            // Pro tier
            $table->unsignedBigInteger('pro_provider_id')->nullable();
            $table->unsignedBigInteger('pro_model_id')->nullable();

            // Fallback
            $table->unsignedBigInteger('fallback_provider_id')->nullable();
            $table->unsignedBigInteger('fallback_model_id')->nullable();

            // Config
            $table->text('system_prompt')->nullable();
            $table->unsignedInteger('max_tokens_free')->default(500);
            $table->unsignedInteger('max_tokens_pro')->default(2000);
            $table->decimal('temperature', 3, 2)->default(0.70);

            $table->timestamps();

            // Foreign keys for pro/fallback
            $table->foreign('pro_provider_id')->references('id')->on('ai_providers')->nullOnDelete();
            $table->foreign('pro_model_id')->references('id')->on('ai_models')->nullOnDelete();
            $table->foreign('fallback_provider_id')->references('id')->on('ai_providers')->nullOnDelete();
            $table->foreign('fallback_model_id')->references('id')->on('ai_models')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_tool_configs');
    }
};
