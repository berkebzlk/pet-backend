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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('initiator_pet_id')->constrained('pets')->onDelete('cascade');
            $table->foreignId('target_pet_id')->constrained('pets')->onDelete('cascade');
            $table->tinyInteger('status')->default(4); // StatusEnum::PENDING
            $table->timestamps();

            $table->unique(['initiator_pet_id', 'target_pet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
