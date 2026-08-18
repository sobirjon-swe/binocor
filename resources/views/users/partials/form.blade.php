<div>
    <x-input-label for="name" value="Ism" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="email" value="Email" />
    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email ?? '')" required />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div>
    <x-input-label for="password" :value="isset($user) ? 'Yangi parol (ixtiyoriy)' : 'Parol'" />
    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" :required="! isset($user)" autocomplete="new-password" />
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<div>
    <x-input-label for="password_confirmation" value="Parolni tasdiqlang" />
    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
</div>

<div>
    <x-input-label value="Rollar" />
    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
        @php $selectedRoles = old('roles', isset($user) ? $user->roles->pluck('name')->toArray() : []); @endphp
        @foreach ($roles as $role)
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(in_array($role->name, $selectedRoles))>
                {{ config('roles.labels')[$role->name] ?? $role->name }}
            </label>
        @endforeach
    </div>
    <x-input-error :messages="$errors->get('roles')" class="mt-2" />
</div>
