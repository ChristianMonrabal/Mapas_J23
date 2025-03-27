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
        Schema::create('gymkhana_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_users_id');
            $table->foreign('group_users_id')->references('id')->on('group_users');
            $table->unsignedBigInteger('checkpoint_id')->nullable(); // Permite null
            $table->foreign('checkpoint_id')->references('id')->on('checkpoints');
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gymkhana_progress');
    }
};
