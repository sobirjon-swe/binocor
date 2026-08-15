<div>
    <x-input-label for="name" value="Nomi" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $project->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="address" value="Manzil" />
    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $project->address ?? '')" />
    <x-input-error :messages="$errors->get('address')" class="mt-2" />
</div>

<div>
    <x-input-label for="start_date" value="Boshlanish sanasi" />
    <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', optional($project->start_date ?? null)->format('Y-m-d'))" />
    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
</div>

<div>
    <x-input-label for="status" value="Holat" />
    <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        @foreach (['active' => 'Faol', 'completed' => 'Yakunlangan', 'paused' => 'To\'xtatilgan'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $project->status ?? 'active') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>
