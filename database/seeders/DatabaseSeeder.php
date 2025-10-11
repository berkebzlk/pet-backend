<?php

namespace Database\Seeders;

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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'berke bozlak',
            'email' => 'berke@example.com',
            'password' => Hash::make('123123123'),
        ]);

        $this->call([
            RoleSeeder::class,
        ]);
    }
}
