<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('pet_id')->nullable()->change();
            $table->foreignId('veterinary_profile_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('pet_id')->nullable(false)->change();
            $table->dropForeign(['veterinary_profile_id']);
            $table->dropColumn('veterinary_profile_id');
        });
    }
};
