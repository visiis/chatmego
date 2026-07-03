<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlbumPhoto;
use App\Models\AlbumPurchase;
use App\Models\User;
use App\Models\UserAlbum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlbumController extends Controller
{
    protected function getUserFromToken(Request $request): ?User
    {
        $token = $request->header('Authorization');
        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }
        
        if (!$token) {
            return null;
        }
        
        return User::where('api_token', $token)->first();
    }

    public function getUserAlbums(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $albums = UserAlbum::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $albums->map(function ($album) {
                $photos = $album->photos()->where('status', 1)->take(4)->get();
                return [
                    'id' => $album->id,
                    'name' => $album->name,
                    'description' => $album->description,
                    'privacy' => $album->privacy,
                    'price' => $album->price,
                    'view_count' => $album->view_count,
                    'purchase_count' => $album->purchase_count,
                    'cover_photos' => $photos->pluck('thumbnail_url'),
                    'photos_count' => $album->photos()->where('status', 1)->count(),
                    'created_at' => $album->created_at ? $album->created_at->toISOString() : null
                ];
            })
        ]);
    }

    public function getAlbum(Request $request, $albumId)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $album = UserAlbum::find($albumId);
        
        if (!$album || !$album->status) {
            return response()->json(['message' => '相册不存在'], 404);
        }

        $isOwner = $user->id == $album->user_id;
        $canView = false;

        if ($isOwner || $album->privacy) {
            $canView = true;
        } else {
            $purchase = AlbumPurchase::where('album_id', $albumId)
                ->where('buyer_id', $user->id)
                ->where('status', 1)
                ->where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->first();
            
            if ($purchase) {
                $canView = true;
            }
        }

        $photos = AlbumPhoto::where('album_id', $albumId)->where('status', 1)->orderBy('sort_order')->get();

        $album->increment('view_count');

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'id' => $album->id,
                'name' => $album->name,
                'description' => $album->description,
                'privacy' => $album->privacy,
                'price' => $album->price,
                'view_count' => $album->view_count,
                'purchase_count' => $album->purchase_count,
                'is_owner' => $isOwner,
                'can_view' => $canView,
                'photos' => $photos->map(function ($photo) use ($canView) {
                    return [
                        'id' => $photo->id,
                        'image_url' => $canView ? $photo->image_url : $photo->thumbnail_url,
                        'thumbnail_url' => $photo->thumbnail_url,
                        'title' => $photo->title,
                        'description' => $photo->description,
                        'can_view_full' => $canView
                    ];
                }),
                'owner' => [
                    'id' => $album->user->id,
                    'name' => $album->user->name,
                    'avatar' => $album->user->avatar_url
                ]
            ]
        ]);
    }

    public function createAlbum(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'privacy' => 'boolean',
            'price' => 'nullable|integer|min:0',
        ]);

        $album = UserAlbum::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'privacy' => $validated['privacy'] ? 1 : 0,
            'price' => $validated['privacy'] ? 0 : ($validated['price'] ?? 0),
        ]);

        return response()->json([
            'code' => 200,
            'message' => '相册创建成功',
            'data' => [
                'id' => $album->id,
                'name' => $album->name,
                'privacy' => $album->privacy,
                'price' => $album->price
            ]
        ]);
    }

    public function updateAlbum(Request $request, $albumId)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $album = UserAlbum::find($albumId);
        
        if (!$album || $album->user_id != $user->id) {
            return response()->json(['message' => '无权限'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'privacy' => 'boolean',
            'price' => 'nullable|integer|min:0',
        ]);

        $album->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'privacy' => $validated['privacy'] ? 1 : 0,
            'price' => $validated['privacy'] ? 0 : ($validated['price'] ?? 0),
        ]);

        return response()->json([
            'code' => 200,
            'message' => '相册更新成功',
            'data' => $album
        ]);
    }

    public function deleteAlbum(Request $request, $albumId)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $album = UserAlbum::find($albumId);
        
        if (!$album || $album->user_id != $user->id) {
            return response()->json(['message' => '无权限'], 403);
        }

        foreach ($album->photos as $photo) {
            $photo->delete();
        }

        $album->delete();

        return response()->json([
            'code' => 200,
            'message' => '相册删除成功'
        ]);
    }

    public function uploadPhoto(Request $request, $albumId)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $album = UserAlbum::find($albumId);
        
        if (!$album || $album->user_id != $user->id) {
            return response()->json(['message' => '无权限'], 403);
        }

        $request->validate([
            'image' => 'required|image|max:10240',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $picBedService = app(\App\Services\PicBedService::class);
            $result = $picBedService->upload($file->getRealPath());

            if ($result['success']) {
                $imageUrl = $result['url'];
                $thumbnailUrl = str_contains($imageUrl, '.th.') ? $imageUrl : preg_replace('/\.(jpg|jpeg|png|webp)$/i', '.th.$1', $imageUrl);

                $photo = AlbumPhoto::create([
                    'album_id' => $albumId,
                    'image_url' => $imageUrl,
                    'thumbnail_url' => $thumbnailUrl,
                    'title' => $request->title,
                    'description' => $request->description,
                    'sort_order' => AlbumPhoto::where('album_id', $albumId)->count() + 1,
                ]);

                return response()->json([
                    'code' => 200,
                    'message' => '图片上传成功',
                    'data' => [
                        'id' => $photo->id,
                        'url' => $photo->image_url,
                        'thumbnail_url' => $photo->thumbnail_url
                    ]
                ]);
            }

            return response()->json(['code' => 400, 'message' => $result['message']]);
        }

        return response()->json(['code' => 400, 'message' => '请选择图片']);
    }

    public function deletePhoto(Request $request, $photoId)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $photo = AlbumPhoto::find($photoId);
        $album = $photo->album;

        if (!$photo || $album->user_id != $user->id) {
            return response()->json(['message' => '无权限'], 403);
        }

        try {
            $picBedService = app(\App\Services\PicBedService::class);
            $picBedService->delete($photo->image_url);
        } catch (\Exception $e) {
        }
        
        $photo->delete();

        return response()->json([
            'code' => 200,
            'message' => '图片删除成功'
        ]);
    }

    public function purchaseAlbum(Request $request, $albumId)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $album = UserAlbum::find($albumId);
        
        if (!$album || !$album->status) {
            return response()->json(['message' => '相册不存在'], 404);
        }

        if ($album->privacy) {
            return response()->json(['message' => '该相册是公开的，无需购买'], 400);
        }

        if ($album->user_id == $user->id) {
            return response()->json(['message' => '不能购买自己的相册'], 400);
        }

        if ($user->coins < $album->price) {
            return response()->json(['message' => '金币不足'], 400);
        }

        $existingPurchase = AlbumPurchase::where('album_id', $albumId)
            ->where('buyer_id', $user->id)
            ->where('status', 1)
            ->first();

        if ($existingPurchase) {
            return response()->json(['message' => '您已购买过该相册'], 400);
        }

        DB::beginTransaction();

        try {
            $user->decrement('coins', $album->price);
            
            $seller = User::find($album->user_id);
            $sellerEarned = (int)($album->price * 0.5);
            $platformEarned = $album->price - $sellerEarned;
            
            $seller->increment('coins', $sellerEarned);

            AlbumPurchase::create([
                'album_id' => $albumId,
                'buyer_id' => $user->id,
                'seller_id' => $album->user_id,
                'price' => $album->price,
                'seller_earned' => $sellerEarned,
                'platform_earned' => $platformEarned,
                'expires_at' => now()->addDays(30),
            ]);

            $album->increment('purchase_count');

            DB::commit();

            return response()->json([
                'code' => 200,
                'message' => '购买成功',
                'data' => [
                    'expires_at' => now()->addDays(30)->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => '购买失败: ' . $e->getMessage()], 500);
        }
    }

    public function checkPurchase(Request $request, $albumId)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $purchase = AlbumPurchase::where('album_id', $albumId)
            ->where('buyer_id', $user->id)
            ->where('status', 1)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'purchased' => !!$purchase,
                'expires_at' => $purchase ? $purchase->expires_at : null
            ]
        ]);
    }

    public function getPurchaseHistory(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $purchases = AlbumPurchase::where('buyer_id', $user->id)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $purchases->map(function ($purchase) {
                $album = $purchase->album;
                return [
                    'id' => $purchase->id,
                    'album_id' => $album->id,
                    'album_name' => $album->name,
                    'album_cover' => $album->photos()->where('status', 1)->first()?->thumbnail_url,
                    'price' => $purchase->price,
                    'seller_name' => $purchase->seller->name,
                    'seller_avatar' => $purchase->seller->avatar_url,
                    'expires_at' => $purchase->expires_at ? $purchase->expires_at->toISOString() : null,
                    'created_at' => $purchase->created_at ? $purchase->created_at->toISOString() : null
                ];
            })
        ]);
    }
}
