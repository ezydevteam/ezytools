<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->json('supported_languages')->nullable()->after('meta_keywords');
            $table->string('default_language', 20)->default('english_us')->after('supported_languages');
        });

        Schema::table('ai_tool_configs', function (Blueprint $table) {
            $table->json('supported_languages')->nullable()->after('temperature');
            $table->string('default_language', 20)->default('english_us')->after('supported_languages');
            $table->string('output_format', 20)->default('text')->after('default_language');
            $table->boolean('show_language_selector')->default(true)->after('output_format');
            $table->boolean('enable_rtl_support')->default(true)->after('show_language_selector');
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn(['supported_languages', 'default_language']);
        });

        Schema::table('ai_tool_configs', function (Blueprint $table) {
            $table->dropColumn(['supported_languages', 'default_language', 'output_format', 'show_language_selector', 'enable_rtl_support']);
        });
    }
};
