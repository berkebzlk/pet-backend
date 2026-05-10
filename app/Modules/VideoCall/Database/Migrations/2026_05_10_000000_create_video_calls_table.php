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
        Schema::create('video_calls', function (Blueprint $column) {
            $column->uuid('id')->primary();
            $column->foreignId('caller_id')->constrained('users')->onDelete('cascade');
            $column->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $column->enum('status', ['pending', 'accepted', 'rejected', 'ended', 'busy', 'no_answer'])->default('pending');
            $column->string('room_name')->unique();
            $column->timestamp('started_at')->nullable();
            $column->timestamp('ended_at')->nullable();
            $column->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_calls');
    }
};
