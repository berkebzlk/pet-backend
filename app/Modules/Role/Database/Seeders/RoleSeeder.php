<?php

namespace App\Modules\Role\Database\Seeders;

use App\Modules\Role\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 100 tane random role oluştur
        Role::factory(100)->create();
        
        $this->command->info('100 roles created successfully!');
    }
}
