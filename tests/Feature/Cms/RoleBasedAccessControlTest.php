<?php

namespace Tests\Feature\Cms;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengurus_cannot_access_user_create_page()
    {
        $this->signIn(User::ROLE_PENGURUS);

        $this->get(route('pengguna.create'))->assertForbidden();
    }

    public function test_santri_cannot_access_user_create_page()
    {
        $this->signIn(User::ROLE_SANTRI);

        $this->get(route('pengguna.create'))->assertForbidden();
    }

    public function test_administrator_can_access_user_create_page()
    {
        $this->signIn(User::ROLE_ADMINISTRATOR);

        $this->get(route('pengguna.create'))->assertOk();
    }

    public function test_administrator_cannot_access_superadmin_attendance_page()
    {
        $this->signIn(User::ROLE_ADMINISTRATOR);

        $this->get(route('kehadiran.index'))->assertForbidden();
    }

    public function test_administrator_cannot_edit_superadmin_user()
    {
        $this->signIn(User::ROLE_ADMINISTRATOR);
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->get(route('pengguna.edit', $superAdmin->id))->assertForbidden();
    }

    public function test_only_superadmin_can_access_rbac_menu_page()
    {
        $this->signIn(User::ROLE_ADMINISTRATOR);
        $this->get(route('rbac.index'))->assertForbidden();

        $this->signIn(User::ROLE_SUPER_ADMIN);
        $this->get(route('rbac.index'))
            ->assertOk()
            ->assertViewIs('rbac.index')
            ->assertSee('Role Based Access Control');
    }

    public function test_superadmin_cannot_access_page_when_permission_disabled_in_rbac()
    {
        $superAdmin = $this->signIn(User::ROLE_SUPER_ADMIN);

        // Turn off 'santri.view' for SuperAdmin
        $this->postJson(route('rbac.toggle'), [
            'permission' => 'santri.view',
            'role' => User::ROLE_SUPER_ADMIN,
        ])->assertOk()
            ->assertJson(['allowed' => false]);

        // Attempting to access 'santri.index' should now be forbidden
        $this->get(route('santri.index'))->assertForbidden();
    }

    public function test_rbac_toggle_changes_route_access_immediately()
    {
        $superAdmin = $this->signIn(User::ROLE_SUPER_ADMIN);

        $this->postJson(route('rbac.toggle'), [
            'permission' => 'users.manage',
            'role' => User::ROLE_ADMINISTRATOR,
        ])->assertOk()
            ->assertJson(['allowed' => false]);

        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
        $this->actingAs($administrator);

        $this->get(route('pengguna.create'))->assertForbidden();

        $this->actingAs($superAdmin);
        $this->postJson(route('rbac.toggle'), [
            'permission' => 'users.manage',
            'role' => User::ROLE_ADMINISTRATOR,
        ])->assertOk()
            ->assertJson(['allowed' => true]);

        $this->actingAs($administrator);
        $this->get(route('pengguna.create'))->assertOk();
    }

    public function test_only_superadmin_can_access_log_activities_page()
    {
        $this->signIn(User::ROLE_ADMINISTRATOR);
        $this->get(route('logs.index'))->assertForbidden();

        $this->signIn(User::ROLE_SUPER_ADMIN);
        $this->get(route('logs.index'))->assertOk();
    }

    public function test_scan_duplicate_does_not_cancel_attendance()
    {
        $this->signIn(User::ROLE_SUPER_ADMIN);
        $santriUser = User::factory()->create(['role' => User::ROLE_SANTRI]);

        Attendance::create([
            'date' => date('Y-m-d'),
            'santri_id' => $santriUser->santri_id,
            'session' => 'Subuh',
            'status' => true,
        ]);

        $this->postJson(route('kehadiran.toggle'), [
            'santri_id' => $santriUser->santri_id,
            'date' => date('Y-m-d'),
            'session' => 'Subuh',
            'source' => 'scan',
        ])->assertOk()
            ->assertJson([
                'duplicate' => true,
                'session' => 'Subuh',
            ]);

        $this->assertDatabaseHas('attendances', [
            'santri_id' => $santriUser->santri_id,
            'session' => 'Subuh',
            'status' => true,
        ]);
    }
}
