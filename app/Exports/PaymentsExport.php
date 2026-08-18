<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Enumerable
    {
        return Payment::with('contract.customer', 'contract.property.project')
            ->latest('due_date')
            ->get();
    }

    public function headings(): array
    {
        return ['Mijoz', 'Obyekt', 'Summa', 'Muddat', "To'langan sana", 'Holat'];
    }

    public function map(mixed $payment): array
    {
        return [
            $payment->contract->customer->full_name,
            $payment->contract->property->type.' — '.$payment->contract->property->project->name,
            (float) $payment->amount,
            optional($payment->due_date)->format('Y-m-d'),
            optional($payment->paid_date)->format('Y-m-d'),
            $payment->status,
        ];
    }
}
