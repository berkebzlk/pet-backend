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
        Schema::table('appointments', function (Blueprint $table) {
            // Add regular indexes first so MySQL can drop the unique indexes
            $table->index('pet_id');
            $table->index('veterinary_profile_id');
            
            $table->dropUnique('pet_app_unique');
            $table->dropUnique('vet_app_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->unique(['pet_id', 'appointment_date', 'start_time'], 'pet_app_unique');
            $table->unique(['veterinary_profile_id', 'appointment_date', 'start_time'], 'vet_app_unique');
            
            $table->dropIndex(['pet_id']);
            $table->dropIndex(['veterinary_profile_id']);
        });
    }
};
