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
        Schema::table('ai_tool_configs', function (Blueprint $table) {
            $table->integer('max_input_length_free')->default(1000)->after('max_tokens_pro');
            $table->integer('max_input_length_pro')->default(5000)->after('max_input_length_free');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_tool_configs', function (Blueprint $table) {
            $table->dropColumn(['max_input_length_free', 'max_input_length_pro']);
        });
    }
};
