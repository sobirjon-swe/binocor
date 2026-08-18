<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitcherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_switching_locale_persists_in_session_and_affects_next_request(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->post(route('locale.update', 'ru'))->assertRedirect();
        $this->assertSame('ru', session('locale'));

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $this->assertSame('ru', app()->getLocale());
    }

    public function test_unknown_locale_is_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->post(route('locale.update', 'fr'))->assertNotFound();
    }

    public function test_guest_can_switch_locale_on_login_page(): void
    {
        $this->post(route('locale.update', 'en'))->assertRedirect();
        $this->assertSame('en', session('locale'));
    }
}
