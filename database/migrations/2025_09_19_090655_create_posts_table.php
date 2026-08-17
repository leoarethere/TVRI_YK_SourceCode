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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // Mengganti nama author_id menjadi user_id agar sesuai dengan konvensi dan controller
            $table->foreignId('user_id')->constrained(
                table: 'users',
                indexName: 'posts_user_id'
            )->onDelete('cascade'); // Menambahkan onDelete cascade

            $table->foreignId('category_id')->constrained(
                table: 'categories',
                indexName: 'posts_category_id'
            )->onDelete('restrict'); // Ubah ke restrict

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body');

            // Kolom-kolom yang ditambahkan agar sesuai dengan formulir
            $table->string('featured_image')->nullable();
            $table->text('excerpt')->nullable();

            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->timestamp('published_at')->nullable();

            // Kolom tambahan untuk SEO dan tracking (opsional tapi bagus)
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
        Schema::dropIfExists('posts');
    }
};
