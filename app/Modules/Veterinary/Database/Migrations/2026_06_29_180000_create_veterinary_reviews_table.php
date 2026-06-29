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
        // Add columns to veterinary_profiles
        Schema::table('veterinary_profiles', function (Blueprint $table) {
            $table->decimal('average_rating', 3, 2)->default(0.00)->after('cover_photo');
            $table->unsignedInteger('reviews_count')->default(0)->after('average_rating');
        });

        // Create veterinary_reviews table
        Schema::create('veterinary_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veterinary_profile_id')->constrained('veterinary_profiles')->onDelete('cascade');
            $table->foreignId('pet_id')->constrained('pets')->onDelete('cascade');
            $table->unsignedTinyInteger('rating'); // 1 to 5
            $table->text('comment')->nullable();
            $table->timestamps();

            // Ensure a pet can review a clinic only once
            $table->unique(['veterinary_profile_id', 'pet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veterinary_reviews');

        Schema::table('veterinary_profiles', function (Blueprint $table) {
            $table->dropColumn(['average_rating', 'reviews_count']);
        });
    }
};
