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
    public function index(Request $request)
    {
        $role = auth()->user()->role?->slug;

        if ($role === 'pembaca' || $role === 'kontributor') {
            $user = auth()->user();

            $filter = in_array($request->get('filter'), ['published', 'pending', 'rejected'], true)
                ? $request->get('filter')
                : 'all';

            $postsQuery = $user->posts()->with('categories')->latest();

            $allPosts = (clone $postsQuery)->get();

            $stats = [
                'total' => $allPosts->count(),
                'published' => $allPosts->where('status', 'published')->count(),
                'pending' => $allPosts->where('status', 'pending')->count(),
                'rejected' => $allPosts->where('status', 'rejected')->count(),
            ];

            if ($filter !== 'all') {
                $postsQuery->where('status', $filter);
            }

            $posts = $postsQuery->paginate(10)->withQueryString();

            return view('frontend.account.index', [
                'user' => $user,
                'categories' => Category::all(),
                'kecamatans' => Kecamatan::ordered()->get(),
                'posts' => $posts,
                'stats' => $stats,
                'filter' => $filter,
                'activeTab' => $request->get('tab') === 'edit' ? 'edit' : 'posts',
            ]);
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

        return redirect()->route('profile.index', ['tab' => 'edit'])
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
