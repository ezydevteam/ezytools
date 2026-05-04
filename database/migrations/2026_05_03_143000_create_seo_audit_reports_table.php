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
        Schema::create('seo_audit_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->string('url', 2048);
            $table->string('domain', 255);
            $table->string('target_keyword', 255)->nullable();
            $table->unsignedTinyInteger('overall_score')->default(0);
            $table->unsignedTinyInteger('technical_score')->default(0);
            $table->unsignedTinyInteger('onpage_score')->default(0);
            $table->unsignedTinyInteger('performance_score')->default(0);
            $table->unsignedTinyInteger('ai_readiness_score')->default(0);
            $table->unsignedTinyInteger('issues_critical')->default(0);
            $table->unsignedTinyInteger('issues_warning')->default(0);
            $table->unsignedTinyInteger('issues_passed')->default(0);
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('canonical_url')->nullable();
            $table->text('h1')->nullable();
            $table->unsignedInteger('word_count')->nullable();
            $table->float('load_time')->nullable();
            $table->longText('audit_data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_audit_reports');
    }
};
