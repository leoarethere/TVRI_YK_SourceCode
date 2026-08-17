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
        Schema::create('info_kunjungan_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question'); // Kolom Pertanyaan
            $table->text('answer'); // Kolom Jawaban (Tipe Text agar muat panjang)
            $table->integer('order')->default(0); // Untuk mengatur urutan (1, 2, 3...)
            $table->boolean('is_active')->default(true); // Untuk draft/publish
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_kunjungan_faqs');
    }
};
