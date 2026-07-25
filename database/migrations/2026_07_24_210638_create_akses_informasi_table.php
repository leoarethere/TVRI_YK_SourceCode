<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akses_informasi', function (Blueprint $table) {
            $table->id();
            // 'internal' atau 'publik' — hanya 2 baris yang akan ada
            $table->enum('type', ['internal', 'publik'])->unique();
            $table->string('label')->default('');       // Label tombol (opsional override)
            $table->string('url')->nullable();          // URL tujuan link
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akses_informasi');
    }
};
