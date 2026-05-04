<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_id')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('company')->nullable();
            $table->string('website')->nullable();
            $table->json('ad_spaces');
            $table->string('duration');
            $table->string('budget')->nullable();
            $table->text('message');
            $table->enum('status', ['pending', 'contacted', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_inquiries');
    }
};
