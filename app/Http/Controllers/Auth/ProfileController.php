<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Kecamatan;
use App\Rules\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role?->slug;

        if ($role === 'pembaca') {
            $categories = Category::all();
            $kecamatans = Kecamatan::ordered()->get();

            return view('frontend.account.index', ['user' => auth()->user(), 'categories' => $categories, 'kecamatans' => $kecamatans]);
        }

        if ($role === 'kontributor') {
            $categories = Category::all();
            $kecamatans = Kecamatan::ordered()->get();

            return view('frontend.account.index', ['user' => auth()->user(), 'categories' => $categories, 'kecamatans' => $kecamatans]);
        }

        return redirect()->route('admin.profile.index');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => ['nullable', 'confirmed', new StrongPassword],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->hasFile('avatar')) {
            $user->update(['avatar' => uploadAvatar($request->file('avatar'), $user->avatar)]);
        }

        if ($request->filled('new_password')) {
            $user->update(['password' => Hash::make($validated['new_password'])]);
        }

        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
