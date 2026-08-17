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
        Schema::create('news', function (Blueprint $table) {
            $table->id();

            // Relasi ke User (Penulis)
            $table->foreignId('user_id')->constrained(
                table: 'users',
                indexName: 'news_user_id'
            )->onDelete('cascade');

            // Relasi ke Kategori Berita (Khusus Berita)
            $table->foreignId('news_category_id')->constrained(
                table: 'news_categories',
                indexName: 'news_category_id'
            )->onDelete('restrict');

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body');

            // Kolom pendukung tampilan
            $table->string('featured_image')->nullable();
            $table->text('excerpt')->nullable();

            // Kolom link eksternal (mengikuti pola link_postingan)
            $table->string('link_berita')->nullable();

            // Status & SEO
            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->unsignedBigInteger('views')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
