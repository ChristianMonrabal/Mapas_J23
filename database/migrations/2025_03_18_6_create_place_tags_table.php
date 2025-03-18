<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('place_tags', function (Blueprint $table) {
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->primary(['place_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('place_tags');
    }
};
