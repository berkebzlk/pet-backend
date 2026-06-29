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
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                // Drop the unique index first to prevent SQLite errors
                try {
                    $table->dropUnique(['username']);
                } catch (\Exception $e) {
                    // Ignore if not supported or index name doesn't match
                }
                
                $table->dropColumn('username');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->unique()->index()->nullable();
            });
        }
    }
};
