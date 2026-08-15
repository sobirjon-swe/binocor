<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@binocor.uz',
        ]);
        $admin->assignRole('admin');

        $manager = User::factory()->create([
            'name' => 'Menejer',
            'email' => 'manager@binocor.uz',
        ]);
        $manager->assignRole('manager');

        $sales = User::factory()->create([
            'name' => 'Sotuvchi',
            'email' => 'sales@binocor.uz',
        ]);
        $sales->assignRole('sales_agent');

        $accountant = User::factory()->create([
            'name' => 'Buxgalter',
            'email' => 'accountant@binocor.uz',
        ]);
        $accountant->assignRole('accountant');

        $foreman = User::factory()->create([
            'name' => 'Prorab',
            'email' => 'foreman@binocor.uz',
        ]);
        $foreman->assignRole('foreman');

        $salesManager = User::factory()->create([
            'name' => 'Sotuv bo\'limi boshlig\'i',
            'email' => 'sales.manager@binocor.uz',
        ]);
        $salesManager->assignRole('sales_manager');

        $lawyer = User::factory()->create([
            'name' => 'Yurist',
            'email' => 'lawyer@binocor.uz',
        ]);
        $lawyer->assignRole('lawyer');

        $chiefEngineer = User::factory()->create([
            'name' => 'Bosh muhandis',
            'email' => 'chief.engineer@binocor.uz',
        ]);
        $chiefEngineer->assignRole('chief_engineer');

        $this->call(DemoDataSeeder::class);
    }
}
