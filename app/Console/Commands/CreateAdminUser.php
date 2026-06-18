<?php

namespace App\Console\Commands;

use App\Modules\User\Models\User;
use App\Modules\Role\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin {name} {username} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user and assign the admin Spatie role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $username = $this->argument('username');
        $email = $this->argument('email');
        $password = $this->argument('password');

        // Check if user already exists
        if (User::where('email', $email)->exists() || User::where('username', $username)->exists()) {
            $this->error("A user with this email or username already exists.");
            return Command::FAILURE;
        }

        // Create the user
        $user = User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("User '{$username}' created successfully.");

        // Ensure the admin role exists for the web guard
        $role = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // Assign the role
        $user->assignRole($role);
        $this->info("Successfully assigned 'admin' role to user: {$email}");

        return Command::SUCCESS;
    }
}
