<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_installment_contract_generates_down_payment_and_monthly_schedule(): void
    {
        $agent = User::factory()->create();
        $agent->assignRole('sales_agent');
        $customer = Customer::factory()->create(['user_id' => $agent->id]);
        $property = Property::factory()->create(['price' => 300_000_000]);

        $response = $this->actingAs($agent)->post(route('contracts.store'), [
            'customer_id' => $customer->id,
            'property_id' => $property->id,
            'total_price' => 300_000_000,
            'payment_type' => 'installment',
            'down_payment' => 60_000_000,
            'installment_months' => 6,
            'signed_date' => now()->format('Y-m-d'),
            'status' => 'active',
        ]);

        $response->assertRedirect();

        $contract = $customer->contracts()->first();
        $this->assertNotNull($contract);
        $this->assertSame('sold', $property->fresh()->status);
        $this->assertSame(7, $contract->payments()->count());

        $total = $contract->payments()->sum('amount');
        $this->assertEqualsWithDelta(300_000_000, $total, 0.01);
    }

    public function test_cash_contract_generates_single_payment(): void
    {
        $agent = User::factory()->create();
        $agent->assignRole('sales_agent');
        $customer = Customer::factory()->create(['user_id' => $agent->id]);
        $property = Property::factory()->create(['price' => 400_000_000]);

        $this->actingAs($agent)->post(route('contracts.store'), [
            'customer_id' => $customer->id,
            'property_id' => $property->id,
            'total_price' => 400_000_000,
            'payment_type' => 'cash',
            'signed_date' => now()->format('Y-m-d'),
            'status' => 'active',
        ]);

        $contract = $customer->contracts()->first();
        $this->assertSame(1, $contract->payments()->count());
        $this->assertEqualsWithDelta(400_000_000, $contract->payments()->first()->amount, 0.01);
    }

    public function test_sales_agent_only_sees_own_customers(): void
    {
        $agentA = User::factory()->create();
        $agentA->assignRole('sales_agent');
        $agentB = User::factory()->create();
        $agentB->assignRole('sales_agent');

        Customer::factory()->create(['user_id' => $agentA->id, 'full_name' => 'A mijozi']);
        Customer::factory()->create(['user_id' => $agentB->id, 'full_name' => 'B mijozi']);

        $response = $this->actingAs($agentA)->get(route('customers.index'));

        $response->assertOk();
        $response->assertSee('A mijozi');
        $response->assertDontSee('B mijozi');
    }

    public function test_sales_manager_sees_whole_team_customers(): void
    {
        $agentA = User::factory()->create();
        $agentA->assignRole('sales_agent');
        $salesManager = User::factory()->create();
        $salesManager->assignRole('sales_manager');

        Customer::factory()->create(['user_id' => $agentA->id, 'full_name' => 'Jamoa mijozi']);

        $response = $this->actingAs($salesManager)->get(route('customers.index'));

        $response->assertOk();
        $response->assertSee('Jamoa mijozi');
    }
}
