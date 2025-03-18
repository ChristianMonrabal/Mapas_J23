<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gymkhana_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('checkpoints_id');
            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('checkpoints_id')->references('id')->on('checkpoints');
            $table->boolean('completado')->default(false);});
    }

    public function down(): void
    {
        Schema::dropIfExists('gymkhana_progress');
    }
};
