<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'sales_agent', 'sales_manager', 'lawyer']);
    }

    public function view(User $user, Customer $customer): bool
    {
        if ($user->hasAnyRole(['admin', 'manager', 'sales_manager', 'lawyer'])) {
            return true;
        }

        return $user->hasRole('sales_agent') && $customer->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'sales_agent', 'sales_manager']);
    }

    public function update(User $user, Customer $customer): bool
    {
        if ($user->hasAnyRole(['admin', 'manager', 'sales_manager'])) {
            return true;
        }

        return $user->hasRole('sales_agent') && $customer->user_id === $user->id;
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'sales_manager']);
    }
}
