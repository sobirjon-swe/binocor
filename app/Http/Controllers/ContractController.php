<?php

namespace App\Http\Controllers;

use App\Exports\ContractsExport;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Property;
use App\Services\ContractService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ContractController extends Controller
{
    public function __construct(protected ContractService $contractService) {}

    public function export(Request $request)
    {
        $this->authorize('viewAny', Contract::class);

        return Excel::download(new ContractsExport($request->user()), 'shartnomalar.xlsx');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Contract::class);

        $contracts = Contract::with('customer', 'property')
            ->when(
                $request->user()->hasRole('sales_agent'),
                fn ($query) => $query->where('user_id', $request->user()->id)
            )
            ->latest()
            ->paginate(15);

        return view('contracts.index', compact('contracts'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Contract::class);

        $customers = Customer::when(
            $request->user()->hasRole('sales_agent'),
            fn ($query) => $query->where('user_id', $request->user()->id)
        )->orderBy('full_name')->get();
        $properties = Property::where('status', 'available')->with('project')->get();

        return view('contracts.create', compact('customers', 'properties'));
    }

    public function store(StoreContractRequest $request)
    {
        $this->authorize('create', Contract::class);

        $this->contractService->create([...$request->validated(), 'user_id' => $request->user()->id]);

        return redirect()->route('contracts.index')->with('status', 'Shartnoma yaratildi.');
    }

    public function show(Contract $contract)
    {
        $this->authorize('view', $contract);

        $contract->load('customer', 'property.project', 'payments');

        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract, Request $request)
    {
        $this->authorize('update', $contract);

        $customers = Customer::when(
            $request->user()->hasRole('sales_agent'),
            fn ($query) => $query->where('user_id', $request->user()->id)
        )->orderBy('full_name')->get();
        $properties = Property::where('status', 'available')
            ->orWhere('id', $contract->property_id)
            ->with('project')
            ->get();

        return view('contracts.edit', compact('contract', 'customers', 'properties'));
    }

    public function update(UpdateContractRequest $request, Contract $contract)
    {
        $this->authorize('update', $contract);

        $this->contractService->update($contract, $request->validated());

        return redirect()->route('contracts.index')->with('status', 'Shartnoma yangilandi.');
    }

    public function destroy(Contract $contract)
    {
        $this->authorize('delete', $contract);

        $contract->delete();

        return redirect()->route('contracts.index')->with('status', 'Shartnoma o\'chirildi.');
    }

    public function pdf(Contract $contract)
    {
        $this->authorize('view', $contract);

        $contract->load('customer', 'property.project', 'payments');

        $pdf = Pdf::loadView('contracts.pdf', compact('contract'))->setPaper('a4');

        return $pdf->download("shartnoma-{$contract->id}.pdf");
    }
}
