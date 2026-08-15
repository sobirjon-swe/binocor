<div>
    <x-input-label for="customer_id" value="Mijoz" />
    <select id="customer_id" name="customer_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">— Tanlang —</option>
        @foreach ($customers as $item)
            <option value="{{ $item->id }}" @selected(old('customer_id', $contract->customer_id ?? '') == $item->id)>{{ $item->full_name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="property_id" value="Obyekt" />
    <select id="property_id" name="property_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">— Tanlang —</option>
        @foreach ($properties as $item)
            <option value="{{ $item->id }}" @selected(old('property_id', $contract->property_id ?? '') == $item->id)>{{ $item->project->name }} — {{ $item->type }} ({{ $item->area }} m²)</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('property_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="total_price" value="Umumiy narx" />
    <x-text-input id="total_price" name="total_price" type="number" step="0.01" class="mt-1 block w-full" :value="old('total_price', $contract->total_price ?? '')" required />
    <x-input-error :messages="$errors->get('total_price')" class="mt-2" />
</div>

<div>
    <x-input-label for="payment_type" value="To'lov turi" />
    <select id="payment_type" name="payment_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        @foreach (['cash' => 'Naqd', 'installment' => 'Rassrochka'] as $value => $label)
            <option value="{{ $value }}" @selected(old('payment_type', $contract->payment_type ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('payment_type')" class="mt-2" />
</div>

<div>
    <x-input-label for="signed_date" value="Shartnoma sanasi" />
    <x-text-input id="signed_date" name="signed_date" type="date" class="mt-1 block w-full" :value="old('signed_date', optional($contract->signed_date ?? null)->format('Y-m-d'))" required />
    <x-input-error :messages="$errors->get('signed_date')" class="mt-2" />
</div>

<div>
    <x-input-label for="status" value="Holat" />
    <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        @foreach (['active' => 'Faol', 'cancelled' => 'Bekor qilingan', 'completed' => 'Yakunlangan'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $contract->status ?? 'active') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>
