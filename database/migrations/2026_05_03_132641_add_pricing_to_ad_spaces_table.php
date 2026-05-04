<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ad_spaces', function (Blueprint $table) {
            $table->text('description')->nullable()->after('position');
            $table->string('dimensions')->nullable()->after('description');
            $table->string('est_impressions')->nullable()->after('dimensions');
            $table->decimal('price_3d', 8, 2)->default(0)->after('est_impressions');
            $table->decimal('price_7d', 8, 2)->default(0)->after('price_3d');
            $table->decimal('price_30d', 8, 2)->default(0)->after('price_7d');
            $table->boolean('is_available')->default(true)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('ad_spaces', function (Blueprint $table) {
            $table->dropColumn(['description', 'dimensions', 'est_impressions', 'price_3d', 'price_7d', 'price_30d', 'is_available']);
        });
    }
};
