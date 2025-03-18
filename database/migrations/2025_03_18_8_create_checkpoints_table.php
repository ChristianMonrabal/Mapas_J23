<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gymkhana_id')->constrained('gymkhanas')->onDelete('cascade');
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            $table->text('pista');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoints');
    }
};
