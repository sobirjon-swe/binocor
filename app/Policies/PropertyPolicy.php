<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'sales_agent', 'sales_manager', 'chief_engineer']);
    }

    public function view(User $user, Property $property): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'sales_agent', 'sales_manager', 'chief_engineer']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function update(User $user, Property $property): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }
}
