<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClickGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.click.secret_key' => 'test-secret']);
    }

    protected function sign(array $params): string
    {
        $pieces = [$params['click_trans_id'], $params['service_id'], config('services.click.secret_key'), $params['merchant_trans_id']];

        if ((int) $params['action'] === 1) {
            $pieces[] = $params['merchant_prepare_id'];
        }

        $pieces[] = $params['amount'];
        $pieces[] = $params['action'];
        $pieces[] = $params['sign_time'];

        return md5(implode('', $pieces));
    }

    public function test_rejects_invalid_signature(): void
    {
        $payment = Payment::factory()->create(['amount' => 100_000, 'status' => 'pending']);

        $response = $this->post(route('webhooks.click'), [
            'click_trans_id' => '1', 'service_id' => '1', 'merchant_trans_id' => $payment->id,
            'amount' => '100000.00', 'action' => 0, 'sign_time' => '2026-08-17 00:00:00',
            'sign_string' => 'wrong',
        ]);

        $response->assertJson(['error' => -1]);
    }

    public function test_prepare_then_complete_marks_payment_paid(): void
    {
        $payment = Payment::factory()->create(['amount' => 150_000, 'status' => 'pending']);

        $prepareParams = [
            'click_trans_id' => '555', 'service_id' => '10', 'merchant_trans_id' => $payment->id,
            'amount' => '150000.00', 'action' => 0, 'sign_time' => '2026-08-17 00:00:00',
        ];
        $prepareParams['sign_string'] = $this->sign($prepareParams);

        $prepare = $this->post(route('webhooks.click'), $prepareParams);
        $prepare->assertJson(['error' => 0]);
        $prepareId = $prepare->json('merchant_prepare_id');

        $completeParams = [
            'click_trans_id' => '555', 'service_id' => '10', 'merchant_trans_id' => $payment->id,
            'merchant_prepare_id' => $prepareId, 'amount' => '150000.00', 'action' => 1,
            'sign_time' => '2026-08-17 00:05:00', 'error' => 0,
        ];
        $completeParams['sign_string'] = $this->sign($completeParams);

        $complete = $this->post(route('webhooks.click'), $completeParams);
        $complete->assertJson(['error' => 0]);

        $this->assertSame('paid', $payment->fresh()->status);
        $this->assertSame(1, PaymentTransaction::where('provider', 'click')->count());
    }

    public function test_prepare_rejects_amount_mismatch(): void
    {
        $payment = Payment::factory()->create(['amount' => 150_000, 'status' => 'pending']);

        $params = [
            'click_trans_id' => '556', 'service_id' => '10', 'merchant_trans_id' => $payment->id,
            'amount' => '1.00', 'action' => 0, 'sign_time' => '2026-08-17 00:00:00',
        ];
        $params['sign_string'] = $this->sign($params);

        $response = $this->post(route('webhooks.click'), $params);
        $response->assertJson(['error' => -2]);
    }
}
