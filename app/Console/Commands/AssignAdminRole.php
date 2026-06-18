<?php

namespace App\Console\Commands;

use App\Modules\User\Models\User;
use App\Modules\Role\Models\Role;
use Illuminate\Console\Command;

class AssignAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign the admin Spatie role to a user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found with email: {$email}");
            return Command::FAILURE;
        }

        // Ensure the admin role exists for the web guard (used by Filament)
        $role = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // Assign the role to the user
        if ($user->hasRole($role)) {
            $this->info("User already has the 'admin' role.");
        } else {
            $user->assignRole($role);
            $this->info("Successfully assigned 'admin' role to user: {$email}");
        }

        return Command::SUCCESS;
    }
}
