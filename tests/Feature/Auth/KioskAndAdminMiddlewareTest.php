<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\EnsureAdminAccess;
use App\Http\Middleware\EnsureKioskAccess;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class KioskAndAdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function userWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    protected function next()
    {
        return fn ($request) => response()->json(['ok' => true]);
    }

    public function test_guest_gets_401_on_kiosk_middleware(): void
    {
        $response = (new EnsureKioskAccess())->handle(Request::create('/x'), $this->next());

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_guest_gets_401_on_admin_middleware(): void
    {
        $response = (new EnsureAdminAccess())->handle(Request::create('/x'), $this->next());

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_attendant_passes_kiosk_middleware(): void
    {
        $this->actingAs($this->userWithRole('cajero'));

        $response = (new EnsureKioskAccess())->handle(Request::create('/x'), $this->next());

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_attendant_gets_403_on_admin_middleware(): void
    {
        $this->actingAs($this->userWithRole('cajero'));

        $response = (new EnsureAdminAccess())->handle(Request::create('/x'), $this->next());

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_supervisor_passes_both_middlewares(): void
    {
        $this->actingAs($this->userWithRole('supervisor'));

        $this->assertEquals(200, (new EnsureKioskAccess())->handle(Request::create('/x'), $this->next())->getStatusCode());
        $this->assertEquals(200, (new EnsureAdminAccess())->handle(Request::create('/x'), $this->next())->getStatusCode());
    }

    public function test_admin_passes_both_middlewares(): void
    {
        $this->actingAs($this->userWithRole('administrador'));

        $this->assertEquals(200, (new EnsureKioskAccess())->handle(Request::create('/x'), $this->next())->getStatusCode());
        $this->assertEquals(200, (new EnsureAdminAccess())->handle(Request::create('/x'), $this->next())->getStatusCode());
    }
}