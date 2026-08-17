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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();

            // Data utama
            $table->string('title'); // Judul timeline/era
            $table->text('content'); // Konten deskripsi

            // Media
            $table->string('image')->nullable(); // Gambar ilustrasi

            // Status
            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
