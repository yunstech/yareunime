<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // siapa yang menandai
            $table->foreignId('movie_id')->constrained()->onDelete('cascade'); // film yang ditandai
            $table->timestamps();

            // Mencegah duplikasi favorit (user tidak bisa favorit film yang sama dua kali)
            $table->unique(['user_id', 'movie_id']);
        });
    }

    /**
     * Batalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
