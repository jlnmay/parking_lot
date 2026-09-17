<?php

namespace Tests\Unit\Auth;

use App\Models\Role;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftPolicyTest extends TestCase
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

    public function test_attendant_can_close_own_shift(): void
    {
        $attendant = $this->userWithRole('cajero');
        $shift = $this->shiftFor($attendant);

        $this->assertTrue($attendant->can('close', $shift));
    }

    public function test_attendant_cannot_close_another_attendants_shift(): void
    {
        $owner = $this->userWithRole('cajero');
        $otherAttendant = $this->userWithRole('cajero');
        $shift = $this->shiftFor($owner);

        $this->assertFalse($otherAttendant->can('close', $shift));
    }

    public function test_supervisor_can_close_any_shift(): void
    {
        $attendant = $this->userWithRole('cajero');
        $supervisor = $this->userWithRole('supervisor');
        $shift = $this->shiftFor($attendant);

        $this->assertTrue($supervisor->can('close', $shift));
    }

    public function test_attendant_cannot_reopen_even_own_shift(): void
    {
        $attendant = $this->userWithRole('cajero');
        $shift = $this->shiftFor($attendant);

        $this->assertFalse($attendant->can('reopen', $shift));
    }

    public function test_supervisor_can_reopen_any_shift(): void
    {
        $attendant = $this->userWithRole('cajero');
        $supervisor = $this->userWithRole('supervisor');
        $shift = $this->shiftFor($attendant);

        $this->assertTrue($supervisor->can('reopen', $shift));
    }

    public function test_admin_can_reopen_any_shift(): void
    {
        $attendant = $this->userWithRole('cajero');
        $admin = $this->userWithRole('administrador');
        $shift = $this->shiftFor($attendant);

        $this->assertTrue($admin->can('reopen', $shift));
    }
}