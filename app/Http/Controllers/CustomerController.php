<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        $customers = Customer::when(
            $request->user()->hasRole('sales_agent'),
            fn ($query) => $query->where('user_id', $request->user()->id)
        )->latest()->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $this->authorize('create', Customer::class);

        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $this->authorize('create', Customer::class);

        Customer::create([...$request->validated(), 'user_id' => $request->user()->id]);

        return redirect()->route('customers.index')->with('status', 'Mijoz yaratildi.');
    }

    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);

        $customer->load('contracts.property');

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);

        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $this->authorize('update', $customer);

        $customer->update($request->validated());

        return redirect()->route('customers.index')->with('status', 'Mijoz yangilandi.');
    }

    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);

        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Mijoz o\'chirildi.');
    }
}
