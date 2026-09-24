<?php

namespace Database\Factories;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends Factory<Shift>
 */
class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    public function definition(): array
    {
        return [
            'attendant_id' => User::factory()->cajero(),
            'opening_cash' => $this->faker->randomFloat(2, 100, 300),
            'opened_at' => now(),
        ];
    }
}
