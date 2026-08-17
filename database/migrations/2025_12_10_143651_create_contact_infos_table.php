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
        Schema::create('contact_infos', function (Blueprint $table) {
            $table->id();
            // Menyimpan nomor telepon admin
            $table->string('admin_phone');

            // Menyimpan nomor telepon kerjasama
            $table->string('partnership_phone');

            // Menyimpan hotline penyiaran
            $table->string('hotline_phone');

            // Menyimpan alamat lengkap (menggunakan text karena bisa panjang)
            $table->text('address');

            // Opsional: Email instansi (saya tambahkan sebagai pelengkap standar)
            $table->string('email')->nullable();

            // Status aktif/tidak (jika ingin menyembunyikan sementara)
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_infos');
    }
};
