<?php

namespace Database\Factories;

use App\Models\ParkerVehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MonthlyParker;

/**
 * @extends Factory<ParkerVehicle>
 */
class ParkerVehicleFactory extends Factory
{
    protected $model = ParkerVehicle::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'monthly_parker_id' => MonthlyParker::factory(),
            'plate_raw'         => fake()->unique()->bothify('??-###-??'),
        ];
    }

    public function forPlate(string $plate): static
    {
        return $this->state(fn () => ['plate_raw' => $plate]);
    }
}
