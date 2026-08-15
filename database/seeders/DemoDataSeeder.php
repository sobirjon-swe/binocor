<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Property;
use App\Services\ContractService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $project = Project::create([
            'name' => 'Bogishamol Residence',
            'address' => 'Toshkent sh., Chilonzor tumani',
            'start_date' => '2025-03-01',
            'status' => 'active',
        ]);

        $soldProperty = Property::create([
            'project_id' => $project->id,
            'type' => 'apartment',
            'area' => 65.5,
            'floor' => 4,
            'rooms_count' => 2,
            'price' => 450_000_000,
            'status' => 'available',
        ]);

        Property::create([
            'project_id' => $project->id,
            'type' => 'apartment',
            'area' => 82.0,
            'floor' => 7,
            'rooms_count' => 3,
            'price' => 620_000_000,
            'status' => 'available',
        ]);

        Property::create([
            'project_id' => $project->id,
            'type' => 'office',
            'area' => 40.0,
            'floor' => 1,
            'rooms_count' => 1,
            'price' => 380_000_000,
            'status' => 'available',
        ]);

        $customer = Customer::create([
            'full_name' => 'Aziz Karimov',
            'phone' => '+998901234567',
            'passport_number' => 'AB1234567',
            'address' => 'Toshkent sh., Yunusobod tumani',
            'lead_status' => 'contracted',
        ]);

        Customer::create([
            'full_name' => 'Dilnoza Yusupova',
            'phone' => '+998907654321',
            'lead_status' => 'interested',
        ]);

        // Shartnoma "installment" turida yaratiladi — bu PaymentScheduleService orqali
        // boshlang'ich to'lov + oylik to'lovlarni avtomatik generatsiya qiladi.
        $contract = app(ContractService::class)->create([
            'customer_id' => $customer->id,
            'property_id' => $soldProperty->id,
            'total_price' => $soldProperty->price,
            'payment_type' => 'installment',
            'down_payment' => 150_000_000,
            'installment_months' => 2,
            'signed_date' => '2025-06-15',
            'status' => 'active',
        ]);

        // Boshlang'ich to'lov allaqachon to'langan deb belgilaymiz.
        $contract->payments()->oldest('due_date')->first()->update([
            'paid_date' => '2025-06-15',
            'status' => 'paid',
        ]);
    }
}
