<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentScheduleService
{
    public function generate(Contract $contract): void
    {
        if ($contract->payment_type === 'installment') {
            $this->generateInstallments($contract);

            return;
        }

        $this->generateSinglePayment($contract);
    }

    protected function generateSinglePayment(Contract $contract): void
    {
        Payment::create([
            'contract_id' => $contract->id,
            'amount' => $contract->total_price,
            'due_date' => $contract->signed_date,
            'status' => 'pending',
        ]);
    }

    protected function generateInstallments(Contract $contract): void
    {
        $downPayment = round((float) ($contract->down_payment ?? 0), 2);
        $months = max(1, (int) ($contract->installment_months ?? 1));
        $remaining = round((float) $contract->total_price - $downPayment, 2);
        $signedDate = Carbon::parse($contract->signed_date);

        if ($downPayment > 0) {
            Payment::create([
                'contract_id' => $contract->id,
                'amount' => $downPayment,
                'due_date' => $signedDate->copy(),
                'status' => 'pending',
            ]);
        }

        $monthlyAmount = round($remaining / $months, 2);
        $allocated = 0.0;

        for ($month = 1; $month <= $months; $month++) {
            $amount = $month < $months ? $monthlyAmount : round($remaining - $allocated, 2);
            $allocated += $amount;

            Payment::create([
                'contract_id' => $contract->id,
                'amount' => $amount,
                'due_date' => $signedDate->copy()->addMonthsNoOverflow($month),
                'status' => 'pending',
            ]);
        }
    }
}
