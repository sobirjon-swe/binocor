<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('roles')->latest()->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        $roles = Role::orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        // Admin tomonidan qo'shilgan xodim email tasdiqlashsiz darhol tizimga kira oladi.
        $user->forceFill(['email_verified_at' => now()])->save();

        $user->syncRoles($request->validated('roles'));

        return redirect()->route('users.index')->with('status', 'Foydalanuvchi qo\'shildi.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = Role::orderBy('name')->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user->update([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            ...$request->validated('password') ? ['password' => Hash::make($request->validated('password'))] : [],
        ]);

        $user->syncRoles($request->validated('roles'));

        return redirect()->route('users.index')->with('status', 'Foydalanuvchi yangilandi.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'O\'zingizni o\'chira olmaysiz.');
        }

        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return redirect()->route('users.index')->with('error', 'Tizimda kamida bitta admin qolishi kerak.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('status', 'Foydalanuvchi o\'chirildi.');
    }
}
