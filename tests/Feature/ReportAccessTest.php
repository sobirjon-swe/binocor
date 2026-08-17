<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_sales_manager_can_view_reports(): void
    {
        $salesManager = User::factory()->create();
        $salesManager->assignRole('sales_manager');

        $this->actingAs($salesManager)->get(route('reports.index'))->assertOk();
    }

    public function test_lawyer_cannot_view_reports(): void
    {
        $lawyer = User::factory()->create();
        $lawyer->assignRole('lawyer');

        $this->actingAs($lawyer)->get(route('reports.index'))->assertForbidden();
    }

    public function test_foreman_cannot_view_reports(): void
    {
        $foreman = User::factory()->create();
        $foreman->assignRole('foreman');

        $this->actingAs($foreman)->get(route('reports.index'))->assertForbidden();
    }
}
