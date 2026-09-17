<?php

namespace Tests\Unit\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PricingAndUserPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function userWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_only_admin_can_update_pricing(): void
    {
        $admin = $this->userWithRole('administrador');
        $supervisor = $this->userWithRole('supervisor');
        $attendant = $this->userWithRole('cajero');

        $this->assertTrue(Gate::forUser($admin)->allows('update-pricing'));
        $this->assertFalse(Gate::forUser($supervisor)->allows('update-pricing'));
        $this->assertFalse(Gate::forUser($attendant)->allows('update-pricing'));
    }

    public function test_only_admin_can_manage_users(): void
    {
        $admin = $this->userWithRole('administrador');
        $supervisor = $this->userWithRole('supervisor');
        $attendant = $this->userWithRole('cajero');

        $this->assertTrue(Gate::forUser($admin)->allows('manage-users'));
        $this->assertFalse(Gate::forUser($supervisor)->allows('manage-users'));
        $this->assertFalse(Gate::forUser($attendant)->allows('manage-users'));
    }
}