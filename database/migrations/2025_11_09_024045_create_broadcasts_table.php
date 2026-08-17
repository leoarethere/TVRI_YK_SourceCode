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
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();

            // Terhubung ke tabel 'users' yang ada (seperti Post)
            $table->foreignId('user_id')->constrained(
                table: 'users'
            )->onDelete('cascade');

            // Terhubung ke tabel 'categories' yang ada (seperti Post)
            $table->foreignId('category_id')->constrained(
                table: 'categories'
            )->onDelete('cascade');

            // Kolom untuk "nama siaran"
            $table->string('title');

            // Kolom untuk URL (konsisten dengan Post)
            $table->string('slug')->unique();

            // Kolom untuk "sinopsis"
            $table->text('synopsis')->nullable();

            // Kolom untuk "poster" (menyimpan path/nama file gambar)
            $table->string('poster')->nullable();

            // Kolom untuk "link youtube"
            $table->string('youtube_link')->nullable();

            // Kolom status (konsisten dengan Post)
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
        Schema::dropIfExists('broadcasts');
    }
};
