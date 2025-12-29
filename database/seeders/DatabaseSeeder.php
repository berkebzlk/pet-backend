<?php

namespace Database\Seeders;

use App\Modules\Pet\Database\Seeders\PetSeeder;
use App\Modules\Role\Database\Seeders\RoleSeeder;
use App\Modules\User\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PetSeeder::class,
        ]);
    }
}
