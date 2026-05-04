<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_tool_configs', function (Blueprint $table) {
            $table->unsignedSmallInteger('credit_cost')->default(1)->after('enable_rtl_support');
        });
    }

    public function down(): void
    {
        Schema::table('ai_tool_configs', function (Blueprint $table) {
            $table->dropColumn('credit_cost');
        });
    }
};
