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
        Schema::create('jadwal_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Senin, Selasa, Senin-Jumat
            $table->string('slug')->unique();
            $table->string('color')->default('blue'); // Untuk warna badge
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_categories');
    }
};
