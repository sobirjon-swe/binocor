<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Property;
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

        $contract = Contract::create([
            'customer_id' => $customer->id,
            'property_id' => $soldProperty->id,
            'total_price' => $soldProperty->price,
            'payment_type' => 'installment',
            'signed_date' => '2025-06-15',
            'status' => 'active',
        ]);

        $soldProperty->update(['status' => 'sold']);

        Payment::create([
            'contract_id' => $contract->id,
            'amount' => 150_000_000,
            'due_date' => '2025-06-15',
            'paid_date' => '2025-06-15',
            'status' => 'paid',
        ]);

        Payment::create([
            'contract_id' => $contract->id,
            'amount' => 150_000_000,
            'due_date' => '2025-09-15',
            'paid_date' => null,
            'status' => 'pending',
        ]);

        Payment::create([
            'contract_id' => $contract->id,
            'amount' => 150_000_000,
            'due_date' => '2025-12-15',
            'paid_date' => null,
            'status' => 'pending',
        ]);
    }
}
