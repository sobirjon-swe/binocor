<div>
    <x-input-label for="project_id" value="Loyiha" />
    <select id="project_id" name="project_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">— Tanlang —</option>
        @foreach ($projects as $item)
            <option value="{{ $item->id }}" @selected(old('project_id', $stage->project_id ?? '') == $item->id)>{{ $item->name }}</option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="name" value="Bosqich nomi" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $stage->name ?? '')" placeholder="masalan: Fundament, Devor, Tom, Ichki ishlar" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="progress_percent" value="Progress (%)" />
    <x-text-input id="progress_percent" name="progress_percent" type="number" min="0" max="100" class="mt-1 block w-full" :value="old('progress_percent', $stage->progress_percent ?? 0)" required />
    <x-input-error :messages="$errors->get('progress_percent')" class="mt-2" />
</div>

<div>
    <x-input-label for="planned_date" value="Reja qilingan sana" />
    <x-text-input id="planned_date" name="planned_date" type="date" class="mt-1 block w-full" :value="old('planned_date', optional($stage->planned_date ?? null)->format('Y-m-d'))" />
    <x-input-error :messages="$errors->get('planned_date')" class="mt-2" />
</div>

<div>
    <x-input-label for="actual_date" value="Bajarilgan sana" />
    <x-text-input id="actual_date" name="actual_date" type="date" class="mt-1 block w-full" :value="old('actual_date', optional($stage->actual_date ?? null)->format('Y-m-d'))" />
    <x-input-error :messages="$errors->get('actual_date')" class="mt-2" />
</div>
