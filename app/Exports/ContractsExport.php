<?php

namespace App\Exports;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContractsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected User $user) {}

    public function collection(): Enumerable
    {
        return Contract::with('customer', 'property.project')
            ->when(
                $this->user->hasRole('sales_agent'),
                fn ($query) => $query->where('user_id', $this->user->id)
            )
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return ['Mijoz', 'Obyekt', 'Narx', "To'lov turi", 'Imzolangan sana', 'Holat'];
    }

    public function map(mixed $contract): array
    {
        return [
            $contract->customer->full_name,
            $contract->property->type.' — '.$contract->property->project->name,
            (float) $contract->total_price,
            $contract->payment_type,
            optional($contract->signed_date)->format('Y-m-d'),
            $contract->status,
        ];
    }
}
