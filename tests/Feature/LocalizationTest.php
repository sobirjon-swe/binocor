<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_validation_errors_are_uzbek_by_default_not_english(): void
    {
        $agent = User::factory()->create();
        $agent->assignRole('sales_agent');

        $response = $this->actingAs($agent)->post(route('customers.store'), []);

        $response->assertSessionHasErrors('full_name');
        $errors = session('errors');

        // Guards against the pre-existing gap where no lang/ directory
        // existed at all, silently leaving every validation message in
        // Laravel's built-in English ("The full name field is required.")
        // even though the rest of the UI is Uzbek.
        $this->assertStringNotContainsString('The', $errors->first('full_name'));
        $this->assertStringContainsString("to'ldirilishi shart", $errors->first('full_name'));
    }

    public function test_validation_errors_switch_language_with_locale(): void
    {
        $agent = User::factory()->create();
        $agent->assignRole('sales_agent');

        app()->setLocale('ru');
        $this->actingAs($agent)->post(route('customers.store'), [])
            ->assertInvalid(['full_name' => 'обязательно для заполнения']);

        app()->setLocale('en');
        $this->actingAs($agent)->post(route('customers.store'), [])
            ->assertInvalid(['full_name' => 'The full name field is required.']);
    }
}
