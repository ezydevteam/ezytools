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
        Schema::create('pdf_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('tool_slug');
            $table->json('input_files');
            $table->string('output_file')->nullable();
            $table->string('status')->default('pending'); // pending/processing/done/failed
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('file_size_input')->nullable();
            $table->unsignedBigInteger('file_size_output')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_jobs');
    }
};
