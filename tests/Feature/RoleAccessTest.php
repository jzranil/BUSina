<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/portal')->assertRedirect('/login');
        $this->get('/my/reservations')->assertRedirect('/login');
    }

    public function test_dashboard_redirects_each_role_to_its_portal(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/dashboard')->assertRedirect(route('portal.dashboard'));
        $this->actingAs(User::factory()->employee()->create())->get('/dashboard')->assertRedirect(route('portal.dashboard'));
        $this->actingAs(User::factory()->customer()->create())->get('/dashboard')->assertRedirect(route('customer.dashboard'));
    }

    public function test_dashboards_render_for_their_roles(): void
    {
        $this->actingAs(User::factory()->employee()->create())->get('/portal')->assertOk();
        $this->actingAs(User::factory()->customer()->create())->get('/my/dashboard')->assertOk();
    }

    public function test_customers_cannot_open_the_staff_portal(): void
    {
        $customer = User::factory()->customer()->create();

        $this->actingAs($customer)->get('/portal')->assertForbidden();
        $this->actingAs($customer)->post('/portal/buses', [])->assertForbidden();
    }

    public function test_staff_cannot_use_the_customer_portal(): void
    {
        $this->actingAs(User::factory()->employee()->create())->get('/my/dashboard')->assertForbidden();
    }

    public function test_employees_cannot_manage_users_or_view_logs(): void
    {
        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)->get('/portal/users')->assertForbidden();
        $this->actingAs($employee)->post('/portal/users', [])->assertForbidden();
        $this->actingAs($employee)->get('/portal/activity-logs')->assertForbidden();
    }

    public function test_admin_can_create_an_employee_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/portal/users', [
            'name' => 'New Staff',
            'email' => 'staff@busina.test',
            'role' => 'employee',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertRedirect(route('portal.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'staff@busina.test', 'role' => 'employee']);
        $this->assertDatabaseHas('activity_logs', ['action' => 'user.created']);
    }

    public function test_admin_cannot_remove_their_own_admin_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put("/portal/users/{$admin->user_id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'customer',
        ]);

        $this->assertSame('admin', $admin->fresh()->role);
    }
}
