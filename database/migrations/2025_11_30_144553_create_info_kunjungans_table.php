<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('info_kunjungans', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul Informasi
            $table->string('slug')->unique();
            $table->text('description'); // Keterangan Kunjungan
            $table->string('source_link'); // File Sumber (Link)
            $table->string('cover_image')->nullable(); // Cover Gambar
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_kunjungans');
    }
};
