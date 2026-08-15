<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'sales_agent']);
    }

    public function view(User $user, Customer $customer): bool
    {
        if ($user->hasAnyRole(['admin', 'manager'])) {
            return true;
        }

        return $user->hasRole('sales_agent') && $customer->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'sales_agent']);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $this->view($user, $customer);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }
}
