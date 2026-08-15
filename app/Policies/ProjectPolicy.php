<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'sales_manager', 'chief_engineer']);
    }

    public function view(User $user, Project $project): bool
    {
        return $user->hasAnyRole(['admin', 'manager', 'sales_manager', 'chief_engineer']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }
}
