<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Shift;

/**
 * @extends Factory<Factory>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Only plate_raw. `plate` is derived by HasPlate.
            'plate_raw'  => fake()->unique()->bothify('??-###-??'),
            'shift_id'   => Shift::factory(),
            'entry_time' => now()->subMinutes(fake()->numberBetween(5, 240)),
            'exit_time'  => null,
            'fee'        => null,
            // ticket_number is intentionally omitted (see note below)
        ];
    }

    /** Vehicle already left. Fee is set because exit closes the record. */
    public function closed(?float $fee = null): static
    {
        return $this->state(function (array $attributes) use ($fee) {
            $exit = (clone $attributes['entry_time'])->addMinutes(fake()->numberBetween(20, 300));

            return [
                'exit_time' => $exit,
                'fee'       => $fee ?? fake()->randomElement([5.00, 7.00, 9.00, 18.00]),
            ];
        });
    }

    /** Use a specific plate, in any layout. Handy for wherePlate() tests. */
    public function forPlate(string $plate): static
    {
        return $this->state(fn () => ['plate_raw' => $plate]);
    }

    public function enteredAt(\DateTimeInterface $time): static
    {
        return $this->state(fn () => ['entry_time' => $time]);
    }
}
