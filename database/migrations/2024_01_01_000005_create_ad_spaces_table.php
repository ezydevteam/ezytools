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
        Schema::create('ad_spaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('position', [
                'header-banner',
                'footer-banner',
                'tool-top',
                'tool-bottom',
                'tool-sidebar',
                'homepage-middle',
            ]);
            $table->enum('type', ['adsense', 'custom_html', 'image'])->default('adsense');
            $table->text('code')->nullable();
            $table->string('image_url')->nullable();
            $table->string('link_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('show_to', ['all', 'free', 'guest'])->default('all');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_spaces');
    }
};
