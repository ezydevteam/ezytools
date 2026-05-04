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
        Schema::table('tool_categories', function (Blueprint $table) {
            $table->renameColumn('name_bn', 'description');
        });

        Schema::table('tools', function (Blueprint $table) {
            $table->renameColumn('name_bn', 'short_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tool_categories', function (Blueprint $table) {
            $table->renameColumn('description', 'name_bn');
        });

        Schema::table('tools', function (Blueprint $table) {
            $table->renameColumn('short_description', 'name_bn');
        });
    }
};
