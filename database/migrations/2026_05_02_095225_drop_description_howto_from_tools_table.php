<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn(['description', 'description_bn', 'how_to_use']);
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->text('description_bn')->nullable()->after('description');
            $table->text('how_to_use')->nullable()->after('description_bn');
        });
    }
};
