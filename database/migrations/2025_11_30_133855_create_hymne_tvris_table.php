<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hymne_tvris', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('info')->nullable();
            $table->string('poster')->nullable();
            $table->text('synopsis');
            $table->string('link')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hymne_tvris');
    }
};
