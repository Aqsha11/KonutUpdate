<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $roles = Role::with(['users' => fn ($q) => $q->latest()])->orderBy('name')->get();
        $noRole = User::whereNull('role_id')->latest()->get();

        return view('admin.users.index', compact('roles', 'noRole'));
    }

    public function create()
    {
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (! $request->filled('password')) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function verify(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('admin.users.index')->with('success', 'Email '.$user->email.' sudah terverifikasi.');
        }

        $user->markEmailAsVerified();

        return redirect()->route('admin.users.index')->with('success', 'Email '.$user->email.' berhasil diverifikasi.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
