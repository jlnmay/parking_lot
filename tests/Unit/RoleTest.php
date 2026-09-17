<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    protected function userWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_is_admin_true_only_for_admin_role(): void
    {
        $this->assertTrue($this->userWithRole('administrador')->isAdmin());
        $this->assertFalse($this->userWithRole('supervisor')->isAdmin());
        $this->assertFalse($this->userWithRole('cajero')->isAdmin());
    }

    public function test_is_supervisor_true_only_for_supervisor_role(): void
    {
        $this->assertTrue($this->userWithRole('supervisor')->isSupervisor());
        $this->assertFalse($this->userWithRole('administrador')->isSupervisor());
        $this->assertFalse($this->userWithRole('cajero')->isSupervisor());
    }

    public function test_is_attendant_true_only_for_attendant_role(): void
    {
        $this->assertTrue($this->userWithRole('cajero')->isAttendant());
        $this->assertFalse($this->userWithRole('administrador')->isAttendant());
        $this->assertFalse($this->userWithRole('supervisor')->isAttendant());
    }
}