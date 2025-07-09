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
        Schema::create('munition_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('munition_id')->constrained('munitions')->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained('attributes')->onDelete('cascade');
            $table->string('value', 255)->nullable();
            $table->decimal('score',  8, 2)->default(0);
            $table->decimal('min',  8, 2)->nullable();
            $table->decimal('max',  8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('munition_attributes');
    }
};
