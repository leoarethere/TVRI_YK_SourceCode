<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            $table->string('title');            // Judul Prestasi (misal: Juara 1 Lomba Web)
            $table->string('award_name');       // Nama Penghargaan (misal: Piala Gubernur)
            $table->string('type');             // Jenis Prestasi (misal: Emas, Perak, Juara Harapan)
            $table->string('category');         // Kategori (misal: Nasional, Internasional)
            $table->year('year');               // Tahun (Format YYYY)
            $table->text('description')->nullable(); // Deskripsi/Keterangan (Trix)
            $table->string('image')->nullable();     // Foto Dokumentasi
            $table->boolean('is_active')->default(true); // Status Tampil
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestasis');
    }
};
