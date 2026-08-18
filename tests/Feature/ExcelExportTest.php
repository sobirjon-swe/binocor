<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExcelExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_accountant_can_download_payments_export(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');
        Payment::factory()->create();

        $response = $this->actingAs($accountant)->get(route('payments.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_sales_agent_cannot_download_payments_export(): void
    {
        $agent = User::factory()->create();
        $agent->assignRole('sales_agent');

        $this->actingAs($agent)->get(route('payments.export'))->assertForbidden();
    }

    public function test_manager_can_download_contracts_export(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');
        Contract::factory()->create();

        $response = $this->actingAs($manager)->get(route('contracts.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_sales_manager_can_download_top_projects_export(): void
    {
        $salesManager = User::factory()->create();
        $salesManager->assignRole('sales_manager');

        $response = $this->actingAs($salesManager)->get(route('reports.top-projects.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_lawyer_cannot_download_top_projects_export(): void
    {
        $lawyer = User::factory()->create();
        $lawyer->assignRole('lawyer');

        $this->actingAs($lawyer)->get(route('reports.top-projects.export'))->assertForbidden();
    }

    public function test_contracts_export_respects_ownership_for_sales_agent(): void
    {
        $agentA = User::factory()->create();
        $agentA->assignRole('sales_agent');
        $agentB = User::factory()->create();
        $agentB->assignRole('sales_agent');

        $customerA = Customer::factory()->create(['user_id' => $agentA->id]);
        $customerB = Customer::factory()->create(['user_id' => $agentB->id]);
        Contract::factory()->create(['user_id' => $agentA->id, 'customer_id' => $customerA->id]);
        Contract::factory()->create(['user_id' => $agentB->id, 'customer_id' => $customerB->id]);

        $export = new \App\Exports\ContractsExport($agentA);

        $this->assertSame(1, $export->collection()->count());
    }
}
