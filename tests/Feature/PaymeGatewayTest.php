<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymeGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.payme.key' => 'test-merchant-key']);
    }

    protected function rpc(array $body): \Illuminate\Testing\TestResponse
    {
        return $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode('Paycom:test-merchant-key'),
        ])->postJson(route('webhooks.payme'), $body);
    }

    public function test_rejects_requests_without_valid_merchant_key(): void
    {
        $response = $this->postJson(route('webhooks.payme'), [
            'method' => 'CheckPerformTransaction',
            'params' => [],
            'id' => 1,
        ]);

        $response->assertJsonPath('error.code', -32504);
    }

    public function test_check_perform_transaction_validates_amount_and_account(): void
    {
        $payment = Payment::factory()->create(['amount' => 100_000, 'status' => 'pending']);

        $ok = $this->rpc([
            'method' => 'CheckPerformTransaction',
            'params' => ['amount' => 100_000 * 100, 'account' => ['payment_id' => $payment->id]],
            'id' => 1,
        ]);
        $ok->assertJsonPath('result.allow', true);

        $wrongAmount = $this->rpc([
            'method' => 'CheckPerformTransaction',
            'params' => ['amount' => 5_000 * 100, 'account' => ['payment_id' => $payment->id]],
            'id' => 2,
        ]);
        $wrongAmount->assertJsonPath('error.code', -31001);

        $noAccount = $this->rpc([
            'method' => 'CheckPerformTransaction',
            'params' => ['amount' => 100_000 * 100, 'account' => ['payment_id' => 999]],
            'id' => 3,
        ]);
        $noAccount->assertJsonPath('error.code', -31050);
    }

    public function test_full_transaction_lifecycle_marks_payment_paid(): void
    {
        $payment = Payment::factory()->create(['amount' => 250_000, 'status' => 'pending']);

        $create = $this->rpc([
            'method' => 'CreateTransaction',
            'params' => [
                'id' => 'payme-tx-1',
                'time' => 1710000000000,
                'amount' => 250_000 * 100,
                'account' => ['payment_id' => $payment->id],
            ],
            'id' => 1,
        ]);
        $create->assertJsonPath('result.state', 1);

        $perform = $this->rpc([
            'method' => 'PerformTransaction',
            'params' => ['id' => 'payme-tx-1'],
            'id' => 2,
        ]);
        $perform->assertJsonPath('result.state', 2);

        $this->assertSame('paid', $payment->fresh()->status);

        // idempotent re-create must return the same transaction, not a new one
        $recreate = $this->rpc([
            'method' => 'CreateTransaction',
            'params' => [
                'id' => 'payme-tx-1',
                'time' => 1710000000000,
                'amount' => 250_000 * 100,
                'account' => ['payment_id' => $payment->id],
            ],
            'id' => 3,
        ]);
        $recreate->assertJsonPath('result.state', 2);
        $this->assertSame(1, PaymentTransaction::count());
    }

    public function test_cancel_after_perform_reverts_payment_to_pending(): void
    {
        $payment = Payment::factory()->create(['amount' => 250_000, 'status' => 'pending']);

        $this->rpc([
            'method' => 'CreateTransaction',
            'params' => ['id' => 'payme-tx-2', 'time' => 1, 'amount' => 250_000 * 100, 'account' => ['payment_id' => $payment->id]],
            'id' => 1,
        ]);
        $this->rpc(['method' => 'PerformTransaction', 'params' => ['id' => 'payme-tx-2'], 'id' => 2]);

        $this->assertSame('paid', $payment->fresh()->status);

        $cancel = $this->rpc([
            'method' => 'CancelTransaction',
            'params' => ['id' => 'payme-tx-2', 'reason' => 5],
            'id' => 3,
        ]);
        $cancel->assertJsonPath('result.state', -2);

        $this->assertSame('pending', $payment->fresh()->status);
    }
}
