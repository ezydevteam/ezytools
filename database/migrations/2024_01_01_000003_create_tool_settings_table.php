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
        Schema::create('tool_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->enum('type', ['text', 'number', 'json', 'boolean'])->default('text');
            $table->string('label')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['tool_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_settings');
    }
};
