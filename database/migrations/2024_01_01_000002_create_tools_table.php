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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('tool_categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('name_bn');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('description_bn')->nullable();
            $table->string('component_name');
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_premium')->default(false);
            $table->integer('daily_limit_free')->default(10);
            $table->integer('daily_limit_pro')->default(-1); // -1 = unlimited
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->integer('order')->default(0);
            $table->unsignedBigInteger('usage_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
