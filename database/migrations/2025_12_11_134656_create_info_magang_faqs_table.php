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
        Schema::create('info_magang_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question'); // Pertanyaan
            $table->text('answer'); // Jawaban (bisa panjang)
            $table->integer('order')->default(0); // Untuk mengatur urutan tampilan (1, 2, 3...)
            $table->boolean('is_active')->default(true); // Agar bisa disembunyikan tanpa dihapus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_magang_faqs');
    }
};
