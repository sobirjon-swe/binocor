<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_updating_contract_price_records_activity_with_causer(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $contract = Contract::factory()->create(['total_price' => 100_000_000]);

        $this->actingAs($manager);
        $contract->update(['total_price' => 120_000_000]);

        $activity = $contract->activitiesAsSubject()->latest('id')->first();
        $this->assertNotNull($activity);
        $this->assertSame($manager->id, $activity->causer_id);
        $this->assertSame(100_000_000, (int) $activity->attribute_changes['old']['total_price']);
        $this->assertSame(120_000_000, (int) $activity->attribute_changes['attributes']['total_price']);
    }

    public function test_property_status_change_without_authenticated_user_has_no_causer(): void
    {
        $property = Property::factory()->create(['status' => 'available']);

        $property->update(['status' => 'sold']);

        $activity = $property->activitiesAsSubject()->latest('id')->first();
        $this->assertNotNull($activity);
        $this->assertNull($activity->causer_id);
    }

    public function test_admin_and_manager_can_view_activity_log_but_others_cannot(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $agent = User::factory()->create();
        $agent->assignRole('sales_agent');

        $this->actingAs($admin)->get(route('activity-log.index'))->assertOk();
        $this->actingAs($manager)->get(route('activity-log.index'))->assertOk();
        $this->actingAs($agent)->get(route('activity-log.index'))->assertForbidden();
    }

    public function test_activity_log_page_shows_recorded_change(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $contract = Contract::factory()->create(['status' => 'active']);

        $this->actingAs($manager);
        $contract->update(['status' => 'cancelled']);

        $response = $this->actingAs($manager)->get(route('activity-log.index'));

        $response->assertOk();
        $response->assertSee('Shartnoma');
        $response->assertSee('cancelled');
    }
}
