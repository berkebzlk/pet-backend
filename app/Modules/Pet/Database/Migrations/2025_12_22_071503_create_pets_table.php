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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // cat, dog, bird, etc.
            $table->string('breed')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->date('birthdate');
            $table->decimal('weight', 5, 2)->nullable();
            $table->boolean('is_neutered')->default(false);
            $table->text('bio')->nullable();
            $table->string('image')->nullable();
            $table->string('username')->unique()->index();
            $table->integer('posts_count')->default(0);
            $table->integer('match_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
