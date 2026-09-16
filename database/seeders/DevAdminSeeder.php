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
        User::firstOrCreate(
            ['email' => 'admin@parkinglot.test'],
            [
                'name' => 'Dev Admin',
                'password' => Hash::make('password'),
                'role_id' => Role::where('name', 'Administrador')->first()->id,
                'status' => 'active',
            ]
        );
    }
}
