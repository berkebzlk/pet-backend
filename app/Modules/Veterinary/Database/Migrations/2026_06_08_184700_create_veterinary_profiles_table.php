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
        Schema::create('veterinary_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('clinic_name');
            $table->string('city');
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('about')->nullable();
            $table->json('specialties')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('cover_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veterinary_profiles');
    }
};
