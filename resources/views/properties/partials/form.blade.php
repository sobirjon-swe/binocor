<div>
    <x-input-label for="project_id" value="Loyiha" />
    <select id="project_id" name="project_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">— Tanlang —</option>
        @foreach ($projects as $item)
            <option value="{{ $item->id }}" @selected(old('project_id', $property->project_id ?? '') == $item->id)>{{ $item->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="type" value="Turi" />
    <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        @foreach (['apartment' => 'Kvartira', 'office' => 'Ofis', 'land' => 'Uchastka'] as $value => $label)
            <option value="{{ $value }}" @selected(old('type', $property->type ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('type')" class="mt-2" />
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="area" value="Maydon (m²)" />
        <x-text-input id="area" name="area" type="number" step="0.01" class="mt-1 block w-full" :value="old('area', $property->area ?? '')" required />
        <x-input-error :messages="$errors->get('area')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="floor" value="Qavat" />
        <x-text-input id="floor" name="floor" type="number" class="mt-1 block w-full" :value="old('floor', $property->floor ?? '')" />
        <x-input-error :messages="$errors->get('floor')" class="mt-2" />
    </div>
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="rooms_count" value="Xonalar soni" />
        <x-text-input id="rooms_count" name="rooms_count" type="number" class="mt-1 block w-full" :value="old('rooms_count', $property->rooms_count ?? '')" />
        <x-input-error :messages="$errors->get('rooms_count')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="price" value="Narx" />
        <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" :value="old('price', $property->price ?? '')" required />
        <x-input-error :messages="$errors->get('price')" class="mt-2" />
    </div>
</div>

<div>
    <x-input-label for="status" value="Holat" />
    <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        @foreach (['available' => 'Bo\'sh', 'reserved' => 'Band', 'sold' => 'Sotilgan'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $property->status ?? 'available') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>
