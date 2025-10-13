<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained()->onDelete('cascade');
            $table->integer('episode_number')->nullable();
            $table->string('title')->nullable();
            $table->string('episode_page_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('duration')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};

