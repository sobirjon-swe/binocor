<?php

namespace Tests\Feature;

use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PaymentGatewayRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_link_redirects_to_provider_checkout(): void
    {
        config(['services.click.checkout_url' => 'https://my.click.uz/services/pay']);
        $payment = Payment::factory()->create(['status' => 'pending']);

        $url = URL::signedRoute('payments.pay', ['payment' => $payment, 'provider' => 'click']);

        $response = $this->get($url);

        $response->assertRedirect();
        $this->assertStringStartsWith('https://my.click.uz/services/pay', $response->headers->get('Location'));
    }

    public function test_unsigned_link_is_rejected(): void
    {
        $payment = Payment::factory()->create(['status' => 'pending']);

        $this->get(route('payments.pay', ['payment' => $payment, 'provider' => 'click']))
            ->assertForbidden();
    }

    public function test_paid_payment_link_returns_not_found(): void
    {
        $payment = Payment::factory()->create(['status' => 'paid']);

        $url = URL::signedRoute('payments.pay', ['payment' => $payment, 'provider' => 'click']);

        $this->get($url)->assertNotFound();
    }
}
