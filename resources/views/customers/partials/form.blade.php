<div>
    <x-input-label for="full_name" value="To'liq ism" />
    <x-text-input id="full_name" name="full_name" type="text" class="mt-1 block w-full" :value="old('full_name', $customer->full_name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
</div>

<div>
    <x-input-label for="phone" value="Telefon" />
    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $customer->phone ?? '')" required />
    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
</div>

<div>
    <x-input-label for="passport_number" value="Pasport raqami" />
    <x-text-input id="passport_number" name="passport_number" type="text" class="mt-1 block w-full" :value="old('passport_number', $customer->passport_number ?? '')" />
    <x-input-error :messages="$errors->get('passport_number')" class="mt-2" />
</div>

<div>
    <x-input-label for="address" value="Manzil" />
    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $customer->address ?? '')" />
    <x-input-error :messages="$errors->get('address')" class="mt-2" />
</div>

<div>
    <x-input-label for="lead_status" value="Bosqich" />
    <select id="lead_status" name="lead_status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        @foreach (['interested' => 'Qiziqdi', 'viewed' => 'Ko\'rdi', 'reserved' => 'Band qildi', 'contracted' => 'Shartnoma tuzdi'] as $value => $label)
            <option value="{{ $value }}" @selected(old('lead_status', $customer->lead_status ?? 'interested') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('lead_status')" class="mt-2" />
</div>
