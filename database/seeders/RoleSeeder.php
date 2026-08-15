<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = ['admin', 'manager', 'sales_agent', 'accountant', 'foreman'];

        foreach ($roles as $role) {
            Role::findOrCreate($role);
        }
    }
}
