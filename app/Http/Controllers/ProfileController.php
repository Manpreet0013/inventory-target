<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Determine layout based on role
        $roleName = strtolower($user->roles->first()->name ?? '');
        $layout = match ($roleName) {
            'admin' => 'layouts.admin',
            'inventory manager' => 'layouts.inventory',
            'executive' => 'layouts.executive',
            'accountant' => 'layouts.accountant',
            default => 'layouts.app',
        };

        return view('profile.edit', compact('user', 'layout'));
    }

    /**
     * Update profile info.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * Delete user account.
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
