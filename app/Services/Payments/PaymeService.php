<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\PaymentTransaction;

class PaymeService
{
    public const ERROR_ACCOUNT_NOT_FOUND = -31050;

    public const ERROR_INVALID_AMOUNT = -31001;

    public const ERROR_TRANSACTION_NOT_FOUND = -31003;

    public const ERROR_COULD_NOT_PERFORM = -31008;

    public function checkoutUrl(Payment $payment): string
    {
        $params = sprintf(
            'm=%s;ac.payment_id=%d;a=%d',
            config('services.payme.merchant_id'),
            $payment->id,
            (int) round($payment->amount * 100)
        );

        return rtrim(config('services.payme.checkout_url'), '/').'/'.base64_encode($params);
    }

    public function handle(array $request): array
    {
        $method = $request['method'] ?? null;
        $params = $request['params'] ?? [];
        $id = $request['id'] ?? null;

        $result = match ($method) {
            'CheckPerformTransaction' => $this->checkPerformTransaction($params),
            'CreateTransaction' => $this->createTransaction($params),
            'PerformTransaction' => $this->performTransaction($params),
            'CancelTransaction' => $this->cancelTransaction($params),
            'CheckTransaction' => $this->checkTransaction($params),
            default => ['error' => ['code' => -32601, 'message' => 'Method not found']],
        };

        $response = ['jsonrpc' => '2.0', 'id' => $id];

        return array_merge($response, $result);
    }

    protected function checkPerformTransaction(array $params): array
    {
        $error = $this->validateAccountAndAmount($params);

        if ($error) {
            return $error;
        }

        return ['result' => ['allow' => true]];
    }

    protected function createTransaction(array $params): array
    {
        $existing = PaymentTransaction::where('provider', 'payme')
            ->where('provider_transaction_id', $params['id'])
            ->first();

        if ($existing) {
            if ($existing->state === 'cancelled') {
                return ['error' => ['code' => self::ERROR_COULD_NOT_PERFORM, 'message' => 'Tranzaksiya bekor qilingan']];
            }

            return ['result' => $this->transactionState($existing)];
        }

        $error = $this->validateAccountAndAmount($params);

        if ($error) {
            return $error;
        }

        $payment = Payment::find($params['account']['payment_id']);

        $hasActiveTransaction = PaymentTransaction::where('payment_id', $payment->id)
            ->where('provider', 'payme')
            ->where('state', '!=', 'cancelled')
            ->exists();

        if ($hasActiveTransaction) {
            return ['error' => ['code' => self::ERROR_COULD_NOT_PERFORM, 'message' => 'Ushbu to\'lov uchun faol tranzaksiya mavjud']];
        }

        $transaction = PaymentTransaction::create([
            'payment_id' => $payment->id,
            'provider' => 'payme',
            'provider_transaction_id' => $params['id'],
            'amount_tiyin' => $params['amount'],
            'state' => 'created',
        ]);

        return ['result' => $this->transactionState($transaction)];
    }

    protected function performTransaction(array $params): array
    {
        $transaction = $this->findTransaction($params['id']);

        if (! $transaction) {
            return ['error' => ['code' => self::ERROR_TRANSACTION_NOT_FOUND, 'message' => 'Tranzaksiya topilmadi']];
        }

        if ($transaction->state === 'cancelled') {
            return ['error' => ['code' => self::ERROR_COULD_NOT_PERFORM, 'message' => 'Tranzaksiya bekor qilingan']];
        }

        if ($transaction->state === 'created') {
            $transaction->update(['state' => 'performed', 'performed_at' => now()]);
            $transaction->payment->update(['status' => 'paid', 'paid_date' => now()->format('Y-m-d')]);
        }

        return ['result' => $this->transactionState($transaction->fresh())];
    }

    protected function cancelTransaction(array $params): array
    {
        $transaction = $this->findTransaction($params['id']);

        if (! $transaction) {
            return ['error' => ['code' => self::ERROR_TRANSACTION_NOT_FOUND, 'message' => 'Tranzaksiya topilmadi']];
        }

        if ($transaction->state !== 'cancelled') {
            $wasPerformed = $transaction->state === 'performed';

            $transaction->update([
                'state' => 'cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $params['reason'] ?? null,
            ]);

            if ($wasPerformed) {
                $transaction->payment->update(['status' => 'pending', 'paid_date' => null]);
            }
        }

        return ['result' => $this->transactionState($transaction->fresh())];
    }

    protected function checkTransaction(array $params): array
    {
        $transaction = $this->findTransaction($params['id']);

        if (! $transaction) {
            return ['error' => ['code' => self::ERROR_TRANSACTION_NOT_FOUND, 'message' => 'Tranzaksiya topilmadi']];
        }

        return ['result' => $this->transactionState($transaction)];
    }

    protected function validateAccountAndAmount(array $params): ?array
    {
        $payment = Payment::find($params['account']['payment_id'] ?? null);

        if (! $payment || ! in_array($payment->status, ['pending', 'overdue'], true)) {
            return ['error' => ['code' => self::ERROR_ACCOUNT_NOT_FOUND, 'message' => 'To\'lov topilmadi']];
        }

        if ((int) round($payment->amount * 100) !== (int) ($params['amount'] ?? 0)) {
            return ['error' => ['code' => self::ERROR_INVALID_AMOUNT, 'message' => 'Summa mos kelmadi']];
        }

        return null;
    }

    protected function findTransaction(string $providerTransactionId): ?PaymentTransaction
    {
        return PaymentTransaction::where('provider', 'payme')
            ->where('provider_transaction_id', $providerTransactionId)
            ->first();
    }

    protected function transactionState(PaymentTransaction $transaction): array
    {
        $state = match ($transaction->state) {
            'created' => 1,
            'performed' => 2,
            'cancelled' => $transaction->performed_at ? -2 : -1,
        };

        return [
            'create_time' => $transaction->created_at->getTimestampMs(),
            'perform_time' => $transaction->performed_at?->getTimestampMs() ?? 0,
            'cancel_time' => $transaction->cancelled_at?->getTimestampMs() ?? 0,
            'transaction' => (string) $transaction->id,
            'state' => $state,
            'reason' => $transaction->cancel_reason,
        ];
    }
}
