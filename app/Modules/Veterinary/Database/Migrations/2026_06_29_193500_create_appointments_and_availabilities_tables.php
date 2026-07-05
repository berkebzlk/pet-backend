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
        Schema::create('veterinary_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veterinary_profile_id')->constrained('veterinary_profiles')->onDelete('cascade');
            $table->unsignedTinyInteger('day_of_week'); // 0 = Sunday, 6 = Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('slot_duration')->default(30); // in minutes
            $table->timestamps();

            // A clinic can only have one availability range per day of the week
            $table->unique(['veterinary_profile_id', 'day_of_week'], 'vet_avail_unique');
        });

        Schema::create('veterinary_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veterinary_profile_id')->constrained('veterinary_profiles')->onDelete('cascade');
            $table->date('date');
            $table->boolean('is_working')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();

            // A clinic can only have one exception per date
            $table->unique(['veterinary_profile_id', 'date'], 'vet_except_unique');
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veterinary_profile_id')->constrained('veterinary_profiles')->onDelete('cascade');
            $table->foreignId('pet_id')->constrained('pets')->onDelete('cascade');
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status')->default('confirmed'); // confirmed, completed, no_show, cancelled_by_user, cancelled_by_clinic
            $table->text('notes')->nullable();
            $table->timestamps();
            // Add standard indexes on the foreign key columns
            $table->index('pet_id');
            $table->index('veterinary_profile_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('veterinary_exceptions');
        Schema::dropIfExists('veterinary_availabilities');
    }
};
