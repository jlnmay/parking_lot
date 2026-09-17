<?php

namespace Tests\Feature;

use App\Models\Role;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_exactly_the_three_roles(): void
    {
        $this->seed(RoleSeeder::class);

        $names = Role::pluck('name')->sort()->values()->all();

        $this->assertEquals(['administrador', 'cajero', 'supervisor'], $names);
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(RoleSeeder::class); // run twice

        $this->assertEquals(3, Role::count());
    }
}