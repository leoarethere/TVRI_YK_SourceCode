<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan nama tabelnya 'tugas_fungsis' dan kolom-kolomnya ada
        Schema::create('tugas_fungsis', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['tugas', 'fungsi']); // Pembeda: Tugas atau Fungsi
            $table->integer('order')->default(0);       // Urutan
            $table->string('image')->nullable();        // Gambar/Ikon
            $table->text('content');                    // Isi Konten
            $table->boolean('is_active')->default(true); // Status Aktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_fungsis');
    }
};
