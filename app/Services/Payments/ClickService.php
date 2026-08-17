<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\PaymentTransaction;

class ClickService
{
    public const ERROR_SIGN_FAILED = -1;

    public const ERROR_INVALID_AMOUNT = -2;

    public const ERROR_ACTION_NOT_FOUND = -3;

    public const ERROR_ALREADY_PAID = -4;

    public const ERROR_ORDER_NOT_FOUND = -5;

    public const ERROR_TRANSACTION_NOT_FOUND = -6;

    public const ERROR_TRANSACTION_CANCELLED = -9;

    public function checkoutUrl(Payment $payment, string $returnUrl): string
    {
        $params = http_build_query([
            'service_id' => config('services.click.service_id'),
            'merchant_id' => config('services.click.merchant_id'),
            'amount' => number_format($payment->amount, 2, '.', ''),
            'transaction_param' => $payment->id,
            'return_url' => $returnUrl,
        ]);

        return config('services.click.checkout_url').'?'.$params;
    }

    public function handle(array $params): array
    {
        if (! $this->signatureValid($params)) {
            return $this->error($params, self::ERROR_SIGN_FAILED, 'SIGN CHECK FAILED!');
        }

        return match ((int) ($params['action'] ?? -1)) {
            0 => $this->prepare($params),
            1 => $this->complete($params),
            default => $this->error($params, self::ERROR_ACTION_NOT_FOUND, 'Action not found'),
        };
    }

    protected function prepare(array $params): array
    {
        $payment = Payment::find($params['merchant_trans_id'] ?? null);

        if (! $payment) {
            return $this->error($params, self::ERROR_ORDER_NOT_FOUND, 'Order not found');
        }

        if ((float) $params['amount'] !== (float) $payment->amount) {
            return $this->error($params, self::ERROR_INVALID_AMOUNT, 'Incorrect parameter amount');
        }

        if ($payment->status === 'paid') {
            return $this->error($params, self::ERROR_ALREADY_PAID, 'Already paid');
        }

        $transaction = PaymentTransaction::updateOrCreate(
            ['provider' => 'click', 'provider_transaction_id' => $params['click_trans_id']],
            [
                'payment_id' => $payment->id,
                'amount_tiyin' => (int) round($payment->amount * 100),
                'state' => 'created',
            ]
        );

        return [
            'click_trans_id' => $params['click_trans_id'],
            'merchant_trans_id' => $payment->id,
            'merchant_prepare_id' => $transaction->id,
            'error' => 0,
            'error_note' => 'Success',
        ];
    }

    protected function complete(array $params): array
    {
        $transaction = PaymentTransaction::where('provider', 'click')
            ->find($params['merchant_prepare_id'] ?? null);

        if (! $transaction || $transaction->provider_transaction_id !== (string) $params['click_trans_id']) {
            return $this->error($params, self::ERROR_TRANSACTION_NOT_FOUND, 'Transaction not found');
        }

        if ($transaction->state === 'cancelled') {
            return $this->error($params, self::ERROR_TRANSACTION_CANCELLED, 'Transaction cancelled');
        }

        if ((int) ($params['error'] ?? 0) < 0) {
            $transaction->update(['state' => 'cancelled', 'cancelled_at' => now()]);

            return [
                'click_trans_id' => $params['click_trans_id'],
                'merchant_trans_id' => $transaction->payment_id,
                'merchant_confirm_id' => $transaction->id,
                'error' => 0,
                'error_note' => 'Success',
            ];
        }

        if ($transaction->state !== 'performed') {
            $transaction->update(['state' => 'performed', 'performed_at' => now()]);
            $transaction->payment->update(['status' => 'paid', 'paid_date' => now()->format('Y-m-d')]);
        }

        return [
            'click_trans_id' => $params['click_trans_id'],
            'merchant_trans_id' => $transaction->payment_id,
            'merchant_confirm_id' => $transaction->id,
            'error' => 0,
            'error_note' => 'Success',
        ];
    }

    protected function signatureValid(array $params): bool
    {
        $secret = config('services.click.secret_key');
        $action = (int) ($params['action'] ?? -1);

        $pieces = [$params['click_trans_id'] ?? '', $params['service_id'] ?? '', $secret, $params['merchant_trans_id'] ?? ''];

        if ($action === 1) {
            $pieces[] = $params['merchant_prepare_id'] ?? '';
        }

        $pieces[] = $params['amount'] ?? '';
        $pieces[] = $action;
        $pieces[] = $params['sign_time'] ?? '';

        return hash_equals(md5(implode('', $pieces)), $params['sign_string'] ?? '');
    }

    protected function error(array $params, int $code, string $note): array
    {
        return [
            'click_trans_id' => $params['click_trans_id'] ?? null,
            'merchant_trans_id' => $params['merchant_trans_id'] ?? null,
            'error' => $code,
            'error_note' => $note,
        ];
    }
}
