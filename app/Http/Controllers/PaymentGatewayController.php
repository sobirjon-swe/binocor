<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payments\ClickService;
use App\Services\Payments\PaymeService;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function redirect(Request $request, Payment $payment, string $provider, PaymeService $payme, ClickService $click)
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless(in_array($provider, ['payme', 'click'], true), 404);
        abort_if($payment->status === 'paid', 404);

        $url = match ($provider) {
            'payme' => $payme->checkoutUrl($payment),
            'click' => $click->checkoutUrl($payment, route('payments.pay.return', $payment)),
        };

        return redirect()->away($url);
    }

    public function returnPage(Payment $payment)
    {
        return view('payments.gateway-return', compact('payment'));
    }

    public function payme(Request $request, PaymeService $payme)
    {
        if ($request->getUser() !== 'Paycom' || $request->getPassword() !== config('services.payme.key')) {
            return response()->json([
                'jsonrpc' => '2.0',
                'id' => $request->input('id'),
                'error' => ['code' => -32504, 'message' => 'Insufficient privilege to perform this method'],
            ], 200);
        }

        return response()->json($payme->handle($request->all()));
    }

    public function click(Request $request, ClickService $click)
    {
        return response()->json($click->handle($request->all()));
    }
}
