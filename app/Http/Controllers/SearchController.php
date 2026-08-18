<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Property;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->string('q'));
        $user = $request->user();

        $customers = collect();
        $contracts = collect();
        $properties = collect();

        if ($query !== '') {
            if ($user->can('viewAny', Customer::class)) {
                $customers = Customer::when(
                    $user->hasRole('sales_agent'),
                    fn ($q) => $q->where('user_id', $user->id)
                )
                    ->where(fn ($q) => $q->where('full_name', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%"))
                    ->limit(10)
                    ->get();
            }

            if ($user->can('viewAny', Contract::class)) {
                $contracts = Contract::with('customer', 'property.project')
                    ->when(
                        $user->hasRole('sales_agent'),
                        fn ($q) => $q->where('user_id', $user->id)
                    )
                    ->where(fn ($q) => $q
                        ->whereHas('customer', fn ($cq) => $cq->where('full_name', 'like', "%{$query}%")
                            ->orWhere('phone', 'like', "%{$query}%"))
                        ->orWhereHas('property.project', fn ($pq) => $pq->where('name', 'like', "%{$query}%")))
                    ->limit(10)
                    ->get();
            }

            if ($user->can('viewAny', Property::class)) {
                $properties = Property::with('project')
                    ->where(fn ($q) => $q->where('type', 'like', "%{$query}%")
                        ->orWhereHas('project', fn ($pq) => $pq->where('name', 'like', "%{$query}%")))
                    ->limit(10)
                    ->get();
            }
        }

        return view('search.index', compact('query', 'customers', 'contracts', 'properties'));
    }
}
