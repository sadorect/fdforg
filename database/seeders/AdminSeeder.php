<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::updateOrCreate([
            'email' => 'admin@fdf.org',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('admin123'),
            'bio' => 'Default administrator for the Friends of the Deaf Foundation website.',
            'is_admin' => true,
        ]);

        $superAdminRole = Role::where('slug', 'super-admin')->first();

        if ($superAdminRole) {
            $adminUser->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@fdf.org');
        $this->command->info('Password: admin123');
        $this->command->info($superAdminRole
            ? 'Super Admin role assigned.'
            : 'Super Admin role not found. Run RolePermissionSeeder first.');
    }
}