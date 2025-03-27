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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('codigo', 6)->unique();
            $table->unsignedBigInteger('creador');
            // La capacidad máxima debe ser como mínimo 2 y como máximo 4
            $table->integer('max_miembros')->check('max_miembros >= 2 AND max_miembros <= 4');
            $table->boolean('game_started')->default(false);

            $table->timestamps();

            $table->foreign('creador')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
