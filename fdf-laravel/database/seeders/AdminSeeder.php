<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@fdf.org',
            'password' => Hash::make('admin123'),
            'bio' => 'Default administrator for the Friends of the Deaf Foundation website.',
            'is_admin' => true,
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@fdf.org');
        $this->command->info('Password: admin123');
    }
}