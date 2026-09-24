<?php

namespace Database\Factories;

use App\Models\MonthlyParker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonthlyParker>
 */
class MonthlyParkerFactory extends Factory
{
    protected $model = MonthlyParker::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'plan'          => fake()->randomElement(['Monthly Standard', 'Monthly Plus']),
            'expiration_date'    => now()->addDays(fake()->numberBetween(15, 60))->toDateString(),
        ];
    }

    public function expiringSoon(int $days = 5): static
    {
        return $this->state(fn () => ['expires_on' => now()->addDays($days)->toDateString()]);
    }

    public function expired(int $daysAgo = 3): static
    {
        return $this->state(fn () => ['expires_on' => now()->subDays($daysAgo)->toDateString()]);
    }
}
