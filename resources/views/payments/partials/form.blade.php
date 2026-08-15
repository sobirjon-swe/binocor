<div>
    <x-input-label for="contract_id" value="Shartnoma" />
    <select id="contract_id" name="contract_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">— Tanlang —</option>
        @foreach ($contracts as $item)
            <option value="{{ $item->id }}" @selected(old('contract_id', $payment->contract_id ?? '') == $item->id)>{{ $item->customer->full_name }} — {{ number_format($item->total_price, 0, '.', ' ') }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('contract_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="amount" value="Summa" />
    <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('amount', $payment->amount ?? '')" required />
    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
</div>

<div>
    <x-input-label for="due_date" value="To'lov muddati" />
    <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date', optional($payment->due_date ?? null)->format('Y-m-d'))" required />
    <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
</div>

<div>
    <x-input-label for="paid_date" value="To'langan sana" />
    <x-text-input id="paid_date" name="paid_date" type="date" class="mt-1 block w-full" :value="old('paid_date', optional($payment->paid_date ?? null)->format('Y-m-d'))" />
    <x-input-error :messages="$errors->get('paid_date')" class="mt-2" />
</div>

<div>
    <x-input-label for="status" value="Holat" />
    <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        @foreach (['pending' => 'Kutilmoqda', 'paid' => 'To\'langan', 'overdue' => 'Kechikkan'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $payment->status ?? 'pending') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>
