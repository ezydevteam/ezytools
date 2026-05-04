<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the helpfuls table entirely
        Schema::dropIfExists('tool_review_helpfuls');

        // Remove deprecated columns from tool_reviews
        Schema::table('tool_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'guest_name',
                'guest_email',
                'review_text',
                'is_featured',
                'helpful_count',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('tool_reviews', function (Blueprint $table) {
            $table->string('guest_name')->nullable()->after('user_id');
            $table->string('guest_email')->nullable()->after('guest_name');
            $table->text('review_text')->nullable()->after('rating');
            $table->boolean('is_featured')->default(false)->after('is_approved');
            $table->integer('helpful_count')->default(0)->after('is_featured');
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
};
