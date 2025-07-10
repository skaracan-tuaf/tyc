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
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', [
                'multi_role_fighter',
                'air_superiority_fighter',
                'bomber',
                'attack_aircraft',
                'reconnaissance_aircraft',
                'electronic_warfare_aircraft',
                'tanker_aircraft',
                'trainer_aircraft',
                'transport_aircraft',
                'attack_helicopter',
                'transport_helicopter',
                'uav',
                'ucav',
                'other'
            ]);
            $table->string('origin', 2); // Ülke kodu (ör: TR, US)
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platforms');
    }
};
