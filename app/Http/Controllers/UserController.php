<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\PicBedService;

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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
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
            if (strpos($user->avatar, 'pic.chatmego.com') !== false) {
                $picBedService = new PicBedService();
                $picBedService->delete($user->avatar);
            } else {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = null;
        }
        
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                if (strpos($user->avatar, 'pic.chatmego.com') !== false) {
                    $picBedService = new PicBedService();
                    $picBedService->delete($user->avatar);
                } else {
                    Storage::disk('public')->delete($user->avatar);
                }
            }
            
            $avatar = $request->file('avatar');
            
            // 尝试上传到图床
            $picBedService = new PicBedService();
            $result = $picBedService->upload($avatar->getPathname(), 'avatars');
            
            if ($result['success']) {
                $validated['avatar'] = $result['url'];
            } else {
                // 图床上传失败，回退到本地存储
                $path = $avatar->store('avatars', 'public');
                $validated['avatar'] = $path;
            }
        }

        $user->update($validated);

        return redirect()->back()->with('success', '个人资料更新成功');
    }
}
