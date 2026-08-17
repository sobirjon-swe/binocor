<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_view_user_list(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get(route('users.index'))->assertOk();
    }

    public function test_non_admin_cannot_view_user_list(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $this->actingAs($manager)->get(route('users.index'))->assertForbidden();
    }

    public function test_admin_can_create_user_with_roles(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Yangi Xodim',
            'email' => 'new.employee@binocor.uz',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => ['accountant'],
        ]);

        $response->assertRedirect(route('users.index'));

        $created = User::where('email', 'new.employee@binocor.uz')->first();
        $this->assertNotNull($created);
        $this->assertNotNull($created->email_verified_at);
        $this->assertTrue($created->hasRole('accountant'));
    }

    public function test_admin_cannot_delete_the_last_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->delete(route('users.destroy', $admin));

        $response->assertRedirect(route('users.index'));
        $this->assertModelExists($admin);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $secondAdmin = User::factory()->create();
        $secondAdmin->assignRole('admin');

        $response = $this->actingAs($admin)->delete(route('users.destroy', $admin));

        $response->assertRedirect(route('users.index'));
        $this->assertModelExists($admin);
    }

    public function test_admin_can_delete_other_user_when_another_admin_remains(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $secondAdmin = User::factory()->create();
        $secondAdmin->assignRole('admin');

        $this->actingAs($admin)->delete(route('users.destroy', $secondAdmin));

        $this->assertModelMissing($secondAdmin);
    }
}
