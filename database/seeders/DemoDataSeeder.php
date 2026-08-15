<?php

namespace Database\Seeders;

use App\Models\ConstructionStage;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Property;
use App\Services\ContractService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Barcha sanalar joriy vaqtga nisbatan (relative) hisoblanadi — shunda demo
     * ma'lumotlar hisobotlardagi "so'nggi 12 oy" grafiklarida har doim ko'rinadi,
     * seed qachon ishga tushirilishidan qat'i nazar.
     */
    public function run(): void
    {
        $project = Project::create([
            'name' => 'Bogishamol Residence',
            'address' => 'Toshkent sh., Chilonzor tumani',
            'start_date' => now()->subMonths(8)->format('Y-m-d'),
            'status' => 'active',
        ]);

        $property1 = Property::create([
            'project_id' => $project->id,
            'type' => 'apartment',
            'area' => 65.5,
            'floor' => 4,
            'rooms_count' => 2,
            'price' => 450_000_000,
            'status' => 'available',
        ]);

        $property2 = Property::create([
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

        $customer2 = Customer::create([
            'full_name' => 'Dilnoza Yusupova',
            'phone' => '+998907654321',
            'lead_status' => 'contracted',
        ]);

        // Shartnoma "installment" turida yaratiladi — bu PaymentScheduleService orqali
        // boshlang'ich to'lov + oylik to'lovlarni avtomatik generatsiya qiladi.
        $contract1 = app(ContractService::class)->create([
            'customer_id' => $customer->id,
            'property_id' => $property1->id,
            'total_price' => $property1->price,
            'payment_type' => 'installment',
            'down_payment' => 150_000_000,
            'installment_months' => 3,
            'signed_date' => now()->subMonths(3)->startOfMonth()->addDays(10)->format('Y-m-d'),
            'status' => 'active',
        ]);

        // Boshlang'ich to'lov va birinchi oylik to'lov allaqachon to'langan.
        $contract1->payments()->orderBy('due_date')->take(2)->get()->each(
            fn ($payment) => $payment->update(['status' => 'paid', 'paid_date' => $payment->due_date])
        );

        // "Naqd" shartnoma — bitta to'liq to'lov avtomatik yaratiladi va darhol to'langan deb belgilanadi.
        $contract2 = app(ContractService::class)->create([
            'customer_id' => $customer2->id,
            'property_id' => $property2->id,
            'total_price' => $property2->price,
            'payment_type' => 'cash',
            'signed_date' => now()->subMonth()->startOfMonth()->addDays(5)->format('Y-m-d'),
            'status' => 'active',
        ]);

        $contract2->payments()->first()->update([
            'status' => 'paid',
            'paid_date' => $contract2->signed_date,
        ]);

        ConstructionStage::create([
            'project_id' => $project->id,
            'name' => 'Fundament',
            'progress_percent' => 100,
            'planned_date' => now()->subMonths(7)->format('Y-m-d'),
            'actual_date' => now()->subMonths(7)->addDays(3)->format('Y-m-d'),
        ]);

        ConstructionStage::create([
            'project_id' => $project->id,
            'name' => 'Devor',
            'progress_percent' => 100,
            'planned_date' => now()->subMonths(5)->format('Y-m-d'),
            'actual_date' => now()->subMonths(5)->addDays(4)->format('Y-m-d'),
        ]);

        ConstructionStage::create([
            'project_id' => $project->id,
            'name' => 'Tom',
            'progress_percent' => 60,
            'planned_date' => now()->subMonths(2)->format('Y-m-d'),
            'actual_date' => null,
        ]);

        ConstructionStage::create([
            'project_id' => $project->id,
            'name' => 'Ichki ishlar',
            'progress_percent' => 10,
            'planned_date' => now()->addMonth()->format('Y-m-d'),
            'actual_date' => null,
        ]);
    }
}
