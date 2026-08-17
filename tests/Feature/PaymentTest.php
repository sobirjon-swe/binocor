<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\OverduePaymentNotification;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_accountant_can_view_and_create_payments(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');
        $contract = Contract::factory()->create();

        $this->actingAs($accountant)->get(route('payments.index'))->assertOk();

        $response = $this->actingAs($accountant)->post(route('payments.store'), [
            'contract_id' => $contract->id,
            'amount' => 15_000_000,
            'due_date' => now()->addMonth()->format('Y-m-d'),
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('payments.index'));
        $this->assertDatabaseHas('payments', ['contract_id' => $contract->id, 'amount' => 15_000_000]);
    }

    public function test_payments_index_shows_gateway_links_for_unpaid_payments(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');
        Payment::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($accountant)->get(route('payments.index'));

        $response->assertOk();
        $response->assertSee('Payme havolasi');
        $response->assertSee('Click havolasi');
    }

    public function test_sales_agent_cannot_view_payments(): void
    {
        $agent = User::factory()->create();
        $agent->assignRole('sales_agent');

        $this->actingAs($agent)->get(route('payments.index'))->assertForbidden();
    }

    public function test_overdue_command_flags_past_due_payments_and_notifies_staff(): void
    {
        Notification::fake();

        $accountant = User::factory()->create();
        $accountant->assignRole('accountant');

        $overdue = Payment::factory()->overdue()->create();
        $upcoming = Payment::factory()->create(['due_date' => now()->addWeek(), 'status' => 'pending']);

        $this->artisan('payments:check-overdue')->assertSuccessful();

        $this->assertSame('overdue', $overdue->fresh()->status);
        $this->assertSame('pending', $upcoming->fresh()->status);

        Notification::assertSentTo($accountant, OverduePaymentNotification::class);
    }
}
