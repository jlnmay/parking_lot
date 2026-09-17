<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['administrador', 'supervisor', 'cajero']),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['name' => 'administrador']);
    }

    public function supervisor(): static
    {
        return $this->state(fn () => ['name' => 'supervisor']);
    }

    public function attendant(): static
    {
        return $this->state(fn () => ['name' => 'cajero']);
    }
}