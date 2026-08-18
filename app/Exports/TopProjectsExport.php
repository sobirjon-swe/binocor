<?php

namespace App\Exports;

use App\Services\ReportService;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TopProjectsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Enumerable
    {
        return app(ReportService::class)->topProjectsByRevenue(limit: 100);
    }

    public function headings(): array
    {
        return ['Loyiha', 'Shartnomalar soni', 'Jami summa'];
    }

    public function map(mixed $row): array
    {
        return [
            $row['name'],
            $row['contracts_count'],
            (float) $row['total'],
        ];
    }
}
