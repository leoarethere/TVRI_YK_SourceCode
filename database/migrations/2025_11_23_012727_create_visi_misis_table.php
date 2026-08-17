<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visi_misis', function (Blueprint $table) {
            $table->id();
            // Kita gunakan enum untuk membedakan apakah ini 'Visi' atau 'Misi'
            $table->enum('type', ['visi', 'misi']);
            $table->text('content'); // Isi teks visi/misi
            $table->integer('order')->default(0); // Untuk mengatur urutan tampilan
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visi_misis');
    }
};
