<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function profile($id = null)
    {
        if ($id) {
            $user = \App\Models\User::findOrFail($id);
        } else {
            $user = auth()->user() ?: \App\Models\User::first();
        }
        return view('profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:255|unique:users,phone,' . auth()->id(),
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'gender' => 'nullable|string',
            'age' => 'nullable|integer|min:18|max:120',
            'height' => 'nullable|integer|min:100|max:250',
            'weight' => 'nullable|integer|min:30|max:300',
            'hobbies' => 'nullable|string',
            'specialty' => 'nullable|string',
            'love_declaration' => 'nullable|string',
        ]);

        $user = auth()->user();
        
        if ($request->has('delete_avatar') && $user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $validated['avatar'] = null;
        }
        
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatar = $request->file('avatar');
            $fileName = time() . '_' . $avatar->getClientOriginalName();
            $avatar->storeAs('avatars', $fileName, 'public');
            $validated['avatar'] = 'avatars/' . $fileName;
        }

        $user->update($validated);

        return redirect()->back()->with('success', '个人资料更新成功');
    }
}
