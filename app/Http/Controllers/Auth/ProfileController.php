<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $stats = [
            'total_images' => $user->images()->count(),
            'active_images' => $user->activeImages()->count(),
            'trashed_images' => $user->trashedImages()->count(),
            'storage_used' => $user->images()->sum('size'),
        ];

        return view('profile', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password',
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if ($request->filled('current_password') && !Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }
}
