<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftOwnershipTest extends TestCase
{
    use RefreshDatabase;

    protected function userWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    protected function shiftFor(User $attendant): Shift
    {
        return Shift::factory()->create(['attendant_id' => $attendant->id]);
    }

    public function test_attendant_owns_their_own_shift(): void
    {
        $attendant = $this->userWithRole('cajero');
        $shift = $this->shiftFor($attendant);

        $this->actingAs($attendant);

        $this->assertTrue($shift->belongsToCurrentAttendant());
    }

    public function test_attendant_does_not_own_another_attendants_shift(): void
    {
        $owner = $this->userWithRole('cajero');
        $otherAttendant = $this->userWithRole('cajero');
        $shift = $this->shiftFor($owner);

        $this->actingAs($otherAttendant);

        $this->assertFalse($shift->belongsToCurrentAttendant());
    }

    public function test_supervisor_owns_any_shift(): void
    {
        $attendant = $this->userWithRole('cajero');
        $supervisor = $this->userWithRole('supervisor');
        $shift = $this->shiftFor($attendant);

        $this->actingAs($supervisor);

        $this->assertTrue($shift->belongsToCurrentAttendant());
    }

    public function test_admin_owns_any_shift(): void
    {
        $attendant = $this->userWithRole('cajero');
        $admin = $this->userWithRole('administrador');
        $shift = $this->shiftFor($attendant);

        $this->actingAs($admin);

        $this->assertTrue($shift->belongsToCurrentAttendant());
    }

    public function test_guest_does_not_own_any_shift(): void
    {
        $attendant = $this->userWithRole('cajero');
        $shift = $this->shiftFor($attendant);

        $this->assertFalse($shift->belongsToCurrentAttendant());
    }
}