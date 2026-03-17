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
        Schema::create('breeding_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('initiator_pet_id')->constrained('pets')->onDelete('cascade');
            $table->foreignId('target_pet_id')->constrained('pets')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->timestamps();

            // Prevent duplicate active requests between same pets
            $table->unique(['initiator_pet_id', 'target_pet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breeding_connections');
    }
};
