<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_seo_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->constrained()->cascadeOnDelete();
            $table->string('how_to_title')->nullable();
            $table->string('how_to_title_en')->nullable();
            $table->text('how_to_content')->nullable();
            $table->text('how_to_content_en')->nullable();
            $table->json('how_to_steps')->nullable();
            $table->string('about_title')->nullable();
            $table->string('about_title_en')->nullable();
            $table->text('about_content')->nullable();
            $table->text('about_content_en')->nullable();
            $table->json('use_cases')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_seo_contents');
    }
};
