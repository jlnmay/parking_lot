<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; 
use App\Models\User;
use App\Models\Role;

class DevAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['administrador', 'supervisor', 'cajero'];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->firstOrFail();

            User::firstOrCreate(
                ['email' => strtolower($roleName) . '@example.com'],
                [
                    'name' => "Dev {$roleName}",
                    'password' => bcrypt('password'),
                    'role_id' => $role->id,
                ]
            );
        }
    }
}
