<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tool_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->tinyInteger('rating');
            $table->text('review_text')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['tool_id', 'is_approved']);
        });

        Schema::create('tool_review_helpfuls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('tool_reviews')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['review_id', 'ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tool_review_helpfuls');
        Schema::dropIfExists('tool_reviews');
    }
};
