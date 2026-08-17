<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('info_magangs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique(); // Untuk URL ramah SEO
            $table->text('description'); // Keterangan Magang (Rich Text)
            $table->string('source_link'); // Link Sumber
            $table->string('cover_image')->nullable(); // Cover Gambar
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_magangs');
    }
};
