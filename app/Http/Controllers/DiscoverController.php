<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAlbum;
use Illuminate\Http\Request;

class DiscoverController extends Controller
{
    public function cards()
    {
        $users = User::whereHas('albums', function($query) {
            $query->where('privacy', false)->orWhere('privacy', true);
        })->with(['albums' => function($query) {
            $query->with('photos')->orderBy('created_at', 'desc');
        }])->inRandomOrder()->limit(20)->get();

        $users = $users->filter(function($user) {
            return $user->albums->isNotEmpty() && 
                   $user->albums->flatMap(function($album) {
                       return $album->photos;
                   })->isNotEmpty();
        });

        return view('discover.cards', compact('users'));
    }
}
