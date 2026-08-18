<?php

namespace App\Http\Controllers;

use App\Exports\PaymentsExport;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Contract;
use App\Models\Payment;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('contract.customer', 'contract.property')->latest('due_date')->paginate(15);

        return view('payments.index', compact('payments'));
    }

    public function export()
    {
        return Excel::download(new PaymentsExport, 'tolovlar.xlsx');
    }

    public function create()
    {
        $contracts = Contract::with('customer')->where('status', 'active')->get();

        return view('payments.create', compact('contracts'));
    }

    public function store(StorePaymentRequest $request)
    {
        Payment::create($request->validated());

        return redirect()->route('payments.index')->with('status', 'To\'lov qo\'shildi.');
    }

    public function edit(Payment $payment)
    {
        $contracts = Contract::with('customer')->where('status', 'active')->get();

        return view('payments.edit', compact('payment', 'contracts'));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment->update($request->validated());

        return redirect()->route('payments.index')->with('status', 'To\'lov yangilandi.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index')->with('status', 'To\'lov o\'chirildi.');
    }
}
