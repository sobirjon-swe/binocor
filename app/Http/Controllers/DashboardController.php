<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Property;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'projects_count' => Project::count(),
            'properties_count' => Property::count(),
            'properties_sold' => Property::where('status', 'sold')->count(),
            'properties_available' => Property::where('status', 'available')->count(),
            'customers_count' => Customer::count(),
            'contracts_count' => Contract::where('status', 'active')->count(),
            'total_sales' => Contract::where('status', 'active')->sum('total_price'),
            'collected' => Payment::where('status', 'paid')->sum('amount'),
            'outstanding' => Payment::where('status', '!=', 'paid')->sum('amount'),
            'overdue_count' => Payment::where('status', 'pending')->whereDate('due_date', '<', now())->count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
