<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_admin_can_find_customer_by_phone(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Customer::factory()->create(['full_name' => 'Aziz Karimov', 'phone' => '+998901112233']);

        $response = $this->actingAs($admin)->get(route('search', ['q' => '901112233']));

        $response->assertOk();
        $response->assertSee('Aziz Karimov');
    }

    public function test_sales_agent_only_finds_own_customers_in_search(): void
    {
        $agentA = User::factory()->create();
        $agentA->assignRole('sales_agent');
        $agentB = User::factory()->create();
        $agentB->assignRole('sales_agent');

        Customer::factory()->create(['user_id' => $agentA->id, 'full_name' => 'Bobur Alimov']);
        Customer::factory()->create(['user_id' => $agentB->id, 'full_name' => 'Bobur Rashidov']);

        $response = $this->actingAs($agentA)->get(route('search', ['q' => 'Bobur']));

        $response->assertSee('Bobur Alimov');
        $response->assertDontSee('Bobur Rashidov');
    }

    public function test_accountant_cannot_see_customer_or_property_results(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');
        Customer::factory()->create(['full_name' => 'Yashirin Mijoz']);
        Property::factory()->create();

        $response = $this->actingAs($accountant)->get(route('search', ['q' => 'Yashirin']));

        $response->assertOk();
        $response->assertDontSee('Yashirin Mijoz');
        $response->assertSee('Hech narsa topilmadi');
    }

    public function test_can_find_property_by_project_name(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $property = Property::factory()->create();
        $property->project()->update(['name' => 'Bogishamol Residence']);

        $response = $this->actingAs($manager)->get(route('search', ['q' => 'Bogishamol']));

        $response->assertSee($property->type);
    }

    public function test_can_find_contract_by_customer_name(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        $customer = Customer::factory()->create(['full_name' => 'Dilnoza Yusupova']);
        $contract = Contract::factory()->create(['customer_id' => $customer->id]);

        $response = $this->actingAs($manager)->get(route('search', ['q' => 'Dilnoza']));

        $response->assertSee('Dilnoza Yusupova');
    }
}
