<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Default password for all users: password
     */
    public function run(): void
    {
        // Create HR Admin
        $hrAdmin = User::create([
            'name' => 'Patricia Williams',
            'email' => 'hr@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'hr_admin',
            'department' => 'Human Resources',
        ]);

        // Create 2 managers (reporting to HR Admin)
        $manager1 = User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah.johnson@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'manager_id' => $hrAdmin->id,
            'department' => 'Engineering',
        ]);

        $manager2 = User::create([
            'name' => 'Michael Chen',
            'email' => 'michael.chen@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'manager_id' => $hrAdmin->id,
            'department' => 'Product',
        ]);

        // Create employees reporting to Manager 1 (Engineering)
        User::create([
            'name' => 'Emily Rodriguez',
            'email' => 'emily.rodriguez@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'manager_id' => $manager1->id,
            'department' => 'Engineering',
        ]);

        User::create([
            'name' => 'David Park',
            'email' => 'david.park@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'manager_id' => $manager1->id,
            'department' => 'Engineering',
        ]);

        User::create([
            'name' => 'Lisa Thompson',
            'email' => 'lisa.thompson@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'manager_id' => $manager1->id,
            'department' => 'Engineering',
        ]);

        User::create([
            'name' => 'James Wilson',
            'email' => 'james.wilson@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'manager_id' => $manager1->id,
            'department' => 'Engineering',
        ]);

        User::create([
            'name' => 'Anna Martinez',
            'email' => 'anna.martinez@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'manager_id' => $manager1->id,
            'department' => 'Engineering',
        ]);

        // Create employees reporting to Manager 2 (Product)
        User::create([
            'name' => 'Robert Kim',
            'email' => 'robert.kim@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'manager_id' => $manager2->id,
            'department' => 'Product',
        ]);

        User::create([
            'name' => 'Jennifer Lee',
            'email' => 'jennifer.lee@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'manager_id' => $manager2->id,
            'department' => 'Product',
        ]);

        User::create([
            'name' => 'Christopher Brown',
            'email' => 'christopher.brown@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'manager_id' => $manager2->id,
            'department' => 'Product',
        ]);

        User::create([
            'name' => 'Michelle Davis',
            'email' => 'michelle.davis@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'manager_id' => $manager2->id,
            'department' => 'Product',
        ]);

        User::create([
            'name' => 'Daniel Anderson',
            'email' => 'daniel.anderson@horizondynamics.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'manager_id' => $manager2->id,
            'department' => 'Product',
        ]);

        $this->command->info('Created ' . User::count() . ' users:');
        $this->command->info('- 1 HR Admin (hr@horizondynamics.com)');
        $this->command->info('- 2 Managers');
        $this->command->info('- 10 Employees');
        $this->command->info('Default password for all users: password');
    }
}
