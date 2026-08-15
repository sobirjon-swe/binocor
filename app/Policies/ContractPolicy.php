<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'sales_agent']);
    }

    public function view(User $user, Contract $contract): bool
    {
        if ($user->hasAnyRole(['admin', 'manager'])) {
            return true;
        }

        return $user->hasRole('sales_agent') && $contract->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'sales_agent']);
    }

    public function update(User $user, Contract $contract): bool
    {
        return $this->view($user, $contract);
    }

    public function delete(User $user, Contract $contract): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }
}
