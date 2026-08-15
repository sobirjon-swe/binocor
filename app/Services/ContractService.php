<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class ContractService
{
    public function create(array $data): Contract
    {
        return DB::transaction(function () use ($data) {
            $contract = Contract::create($data);

            if ($contract->status === 'active') {
                $contract->property->update(['status' => 'sold']);
            }

            return $contract;
        });
    }

    public function update(Contract $contract, array $data): Contract
    {
        return DB::transaction(function () use ($contract, $data) {
            $previousPropertyId = $contract->property_id;
            $previousStatus = $contract->status;

            $contract->update($data);

            if ($previousPropertyId !== $contract->property_id) {
                $this->releasePropertyIfNoActiveContract($previousPropertyId);
            }

            if ($contract->status === 'active') {
                $contract->property->update(['status' => 'sold']);
            } elseif ($previousStatus === 'active') {
                $this->releasePropertyIfNoActiveContract($contract->property_id);
            }

            return $contract;
        });
    }

    protected function releasePropertyIfNoActiveContract(int $propertyId): void
    {
        $hasActiveContract = Contract::where('property_id', $propertyId)
            ->where('status', 'active')
            ->exists();

        if (! $hasActiveContract) {
            Property::whereKey($propertyId)->update(['status' => 'available']);
        }
    }
}
