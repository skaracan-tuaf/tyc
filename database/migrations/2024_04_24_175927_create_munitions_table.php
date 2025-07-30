<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('munitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('SET NULL');
            $table->string('origin')->nullable()->default('Bilinmiyor');
            $table->decimal('price', 12, 2)->default(0);
            $table->enum('target_type', ['SOFT', 'HARD']);
            $table->integer('score')->default(0);
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('munitions');
    }
};
