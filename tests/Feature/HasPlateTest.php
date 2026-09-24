<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ParkerVehicle;
use App\Models\Ticket;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;

class HasPlateTest extends TestCase
{
    use RefreshDatabase;

    public function test_plate_is_derived_from_plate_raw(): void
    {
        $t = Ticket::factory()->create(['plate_raw' => '  ab-123   cd ']);

        $this->assertDatabaseHas('tickets', [
            'id' => $t->id, 'plate_raw' => 'ab-123 cd', 'plate' => 'AB123CD',
        ]);
    }

    public function test_plate_cannot_be_mass_assigned(): void
    {
        $t = new Ticket(['plate_raw' => 'ab 1', 'plate' => 'ZZZ']);

        $this->assertSame('AB1', $t->plate);
    }

    public function test_updating_plate_raw_rederives_plate(): void
    {
        $t = Ticket::factory()->create(['plate_raw' => 'AAA 111']);
        $t->update(['plate_raw' => 'bbb-222']);

        $this->assertSame('BBB222', $t->fresh()->plate);
    }

    public function test_where_plate_matches_any_input_format_on_both_models(): void
    {
        Ticket::factory()->create(['plate_raw' => 'ab-123-cd']);
        ParkerVehicle::factory()->create(['plate_raw' => 'AB 123 CD']);

        $this->assertTrue(Ticket::wherePlate('AB123CD')->exists());
        $this->assertTrue(Ticket::wherePlate('ab 123 cd')->exists());
        $this->assertTrue(ParkerVehicle::wherePlate('ab-123-cd')->exists());
    }

    public function test_null_plate_raw_is_rejected_by_the_database(): void
    {
        $this->expectException(QueryException::class);

        Ticket::factory()->create(['plate_raw' => null]);
    }

    // Only if the conditional migration is applied:
    public function test_second_open_ticket_for_same_plate_is_rejected(): void
    {
        Ticket::factory()->create(['plate_raw' => 'AB-123-CD', 'exit_time' => null]);

        $this->expectException(UniqueConstraintViolationException::class);
        Ticket::factory()->create(['plate_raw' => 'ab 123 cd', 'exit_time' => null]);
    }

    public function test_same_plate_allowed_after_previous_ticket_closed(): void
    {
        Ticket::factory()->create(['plate_raw' => 'AB-123-CD', 'exit_time' => now()]);
        Ticket::factory()->create(['plate_raw' => 'AB-123-CD', 'exit_time' => null]);

        $this->assertSame(2, Ticket::wherePlate('AB123CD')->count());
    }
}
