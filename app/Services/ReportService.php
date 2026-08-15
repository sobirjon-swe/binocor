<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    protected const UZBEK_MONTHS = [
        1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
        5 => 'May', 6 => 'Iyun', 7 => 'Iyul', 8 => 'Avgust',
        9 => 'Sentyabr', 10 => 'Oktyabr', 11 => 'Noyabr', 12 => 'Dekabr',
    ];

    public function monthlySales(int $months = 12): array
    {
        $start = now()->subMonths($months - 1)->startOfMonth();

        $contracts = Contract::where('status', 'active')
            ->where('signed_date', '>=', $start)
            ->get(['total_price', 'signed_date']);

        return $this->bucketByMonth($contracts, 'signed_date', 'total_price', $months);
    }

    public function monthlyCollected(int $months = 12): array
    {
        $start = now()->subMonths($months - 1)->startOfMonth();

        $payments = Payment::where('status', 'paid')
            ->whereNotNull('paid_date')
            ->where('paid_date', '>=', $start)
            ->get(['amount', 'paid_date']);

        return $this->bucketByMonth($payments, 'paid_date', 'amount', $months);
    }

    protected function bucketByMonth(Collection $items, string $dateField, string $amountField, int $months): array
    {
        $buckets = [];
        $cursor = now()->subMonths($months - 1)->startOfMonth();

        for ($i = 0; $i < $months; $i++) {
            $key = $cursor->format('Y-m');
            $buckets[$key] = [
                'label' => self::UZBEK_MONTHS[$cursor->month].' '.$cursor->year,
                'count' => 0,
                'total' => 0.0,
            ];
            $cursor->addMonth();
        }

        foreach ($items as $item) {
            /** @var Carbon $date */
            $date = $item->{$dateField};
            $key = $date->format('Y-m');

            if (isset($buckets[$key])) {
                $buckets[$key]['count']++;
                $buckets[$key]['total'] += (float) $item->{$amountField};
            }
        }

        return array_values($buckets);
    }

    public function propertyStatusBreakdown(): array
    {
        $counts = Property::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'available' => (int) ($counts['available'] ?? 0),
            'reserved' => (int) ($counts['reserved'] ?? 0),
            'sold' => (int) ($counts['sold'] ?? 0),
        ];
    }

    public function topProjectsByRevenue(int $limit = 5): Collection
    {
        return Contract::where('status', 'active')
            ->with('property.project')
            ->get()
            ->groupBy(fn (Contract $contract) => $contract->property->project->name)
            ->map(fn (Collection $group, string $name) => [
                'name' => $name,
                'contracts_count' => $group->count(),
                'total' => $group->sum('total_price'),
            ])
            ->sortByDesc('total')
            ->values()
            ->take($limit);
    }

    public function paymentStatusBreakdown(): array
    {
        $counts = Payment::selectRaw('status, count(*) as count, sum(amount) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'pending' => ['count' => (int) ($counts['pending']->count ?? 0), 'total' => (float) ($counts['pending']->total ?? 0)],
            'overdue' => ['count' => (int) ($counts['overdue']->count ?? 0), 'total' => (float) ($counts['overdue']->total ?? 0)],
            'paid' => ['count' => (int) ($counts['paid']->count ?? 0), 'total' => (float) ($counts['paid']->total ?? 0)],
        ];
    }
}
