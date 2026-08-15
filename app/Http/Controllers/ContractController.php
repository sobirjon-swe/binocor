<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Property;
use App\Services\ContractService;
use Barryvdh\DomPDF\Facade\Pdf;

class ContractController extends Controller
{
    public function __construct(protected ContractService $contractService) {}

    public function index()
    {
        $contracts = Contract::with('customer', 'property')->latest()->paginate(15);

        return view('contracts.index', compact('contracts'));
    }

    public function create()
    {
        $customers = Customer::orderBy('full_name')->get();
        $properties = Property::where('status', 'available')->with('project')->get();

        return view('contracts.create', compact('customers', 'properties'));
    }

    public function store(StoreContractRequest $request)
    {
        $this->contractService->create($request->validated());

        return redirect()->route('contracts.index')->with('status', 'Shartnoma yaratildi.');
    }

    public function show(Contract $contract)
    {
        $contract->load('customer', 'property.project', 'payments');

        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $customers = Customer::orderBy('full_name')->get();
        $properties = Property::where('status', 'available')
            ->orWhere('id', $contract->property_id)
            ->with('project')
            ->get();

        return view('contracts.edit', compact('contract', 'customers', 'properties'));
    }

    public function update(UpdateContractRequest $request, Contract $contract)
    {
        $this->contractService->update($contract, $request->validated());

        return redirect()->route('contracts.index')->with('status', 'Shartnoma yangilandi.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('contracts.index')->with('status', 'Shartnoma o\'chirildi.');
    }

    public function pdf(Contract $contract)
    {
        $contract->load('customer', 'property.project', 'payments');

        $pdf = Pdf::loadView('contracts.pdf', compact('contract'))->setPaper('a4');

        return $pdf->download("shartnoma-{$contract->id}.pdf");
    }
}
