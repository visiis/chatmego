<?php

namespace App\Http\Controllers;

use App\Models\UserAlbum;
use App\Models\AlbumPhoto;
use App\Models\AlbumPurchase;
use App\Models\User;
use App\Services\PicBedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlbumController extends Controller
{
    protected $picBedService;

    public function __construct(PicBedService $picBedService)
    {
        $this->picBedService = $picBedService;
    }

    public function index($userId = null)
    {
        $user = $userId ? User::findOrFail($userId) : Auth::user();
        
        if (!$user) {
            abort(404);
        }

        $albums = UserAlbum::where('user_id', $user->id)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->with(['photos' => function ($query) {
                $query->where('status', 1)->orderBy('sort_order');
            }])
            ->get();

        return view('album.index', compact('user', 'albums'));
    }

    public function show($userId, $albumId)
    {
        $user = User::findOrFail($userId);
        $album = UserAlbum::findOrFail($albumId);
        
        if ($album->user_id != $user->id) {
            abort(404);
        }

        $canView = false;
        $isOwner = Auth::id() == $user->id;

        if ($isOwner || $album->privacy) {
            $canView = true;
        } else {
            $purchase = AlbumPurchase::where('album_id', $albumId)
                ->where('buyer_id', Auth::id())
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

        return view('album.show', compact('user', 'album', 'photos', 'canView', 'isOwner'));
    }

    public function getPhotos($albumId)
    {
        $album = UserAlbum::findOrFail($albumId);
        
        $canView = false;
        $isOwner = Auth::id() == $album->user_id;

        if ($isOwner || $album->privacy) {
            $canView = true;
        } else {
            $purchase = AlbumPurchase::where('album_id', $albumId)
                ->where('buyer_id', Auth::id())
                ->where('status', 1)
                ->where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->first();
            
            if ($purchase) {
                $canView = true;
            }
        }

        if (!$canView) {
            return response()->json(['success' => false, 'message' => '无权限查看'], 403);
        }

        $photos = AlbumPhoto::where('album_id', $albumId)->where('status', 1)->orderBy('sort_order')->get();

        return response()->json(['success' => true, 'photos' => $photos]);
    }

    public function create()
    {
        return view('album.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'privacy' => 'boolean',
            'price' => 'nullable|integer|min:0',
        ]);

        $album = UserAlbum::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'privacy' => $request->privacy ? 1 : 0,
            'price' => $request->privacy ? 0 : ($request->price ?? 0),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => '相册创建成功', 'album' => $album]);
        }

        return redirect()->route('album.show', ['userId' => Auth::id(), 'albumId' => $album->id])
            ->with('success', '相册创建成功');
    }

    public function edit($albumId)
    {
        $album = UserAlbum::findOrFail($albumId);
        
        if ($album->user_id != Auth::id()) {
            abort(403);
        }

        return view('album.edit', compact('album'));
    }

    public function update(Request $request, $albumId)
    {
        $album = UserAlbum::findOrFail($albumId);
        
        if ($album->user_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'privacy' => 'boolean',
            'price' => 'nullable|integer|min:0',
        ]);

        $album->update([
            'name' => $request->name,
            'description' => $request->description,
            'privacy' => $request->privacy ? 1 : 0,
            'price' => $request->privacy ? 0 : ($request->price ?? 0),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => '相册更新成功', 'album' => $album]);
        }

        return redirect()->route('album.show', ['userId' => Auth::id(), 'albumId' => $album->id])
            ->with('success', '相册更新成功');
    }

    public function destroy($albumId)
    {
        $album = UserAlbum::findOrFail($albumId);
        
        if ($album->user_id != Auth::id()) {
            abort(403);
        }

        foreach ($album->photos as $photo) {
            try {
                $this->picBedService->delete($photo->image_url);
            } catch (\Exception $e) {
                \Log::error('Failed to delete photo from picbed', ['photo_id' => $photo->id, 'error' => $e->getMessage()]);
            }
            $photo->delete();
        }

        $album->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => '相册删除成功']);
        }

        return redirect()->route('album.index', ['userId' => Auth::id()])
            ->with('success', '相册删除成功');
    }

    public function uploadPhoto(Request $request, $albumId)
    {
        \Log::info('Album upload started', ['album_id' => $albumId, 'has_file' => $request->hasFile('image')]);
        
        $album = UserAlbum::findOrFail($albumId);
        
        if ($album->user_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'image' => 'required|image|max:10240',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            \Log::info('Album upload file info', ['file_name' => $file->getClientOriginalName(), 'size' => $file->getSize()]);
            $result = $this->picBedService->upload($file->getRealPath());

            if ($result['success']) {
                $imageUrl = $result['url'];
                $thumbnailUrl = preg_replace('/\.(jpg|jpeg|png|webp)$/i', '.th.$1', $imageUrl);

                AlbumPhoto::create([
                    'album_id' => $albumId,
                    'image_url' => $imageUrl,
                    'thumbnail_url' => $thumbnailUrl,
                    'title' => $request->title,
                    'description' => $request->description,
                    'sort_order' => AlbumPhoto::where('album_id', $albumId)->count() + 1,
                ]);

                return response()->json(['success' => true, 'message' => '图片上传成功']);
            }

            return response()->json(['success' => false, 'message' => $result['message']], 400);
        }

        return response()->json(['success' => false, 'message' => '请选择图片'], 400);
    }

    public function uploadPhotos(Request $request, $albumId)
    {
        $album = UserAlbum::findOrFail($albumId);
        
        if ($album->user_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'images.*' => 'image|max:10240',
        ]);

        $results = [
            'success' => [],
            'failed' => []
        ];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $result = $this->picBedService->upload($file->getRealPath());
                
                if ($result['success']) {
                    $imageUrl = $result['url'];
                    $thumbnailUrl = preg_replace('/\.(jpg|jpeg|png|webp)$/i', '.th.$1', $imageUrl);

                    AlbumPhoto::create([
                        'album_id' => $albumId,
                        'image_url' => $imageUrl,
                        'thumbnail_url' => $thumbnailUrl,
                        'sort_order' => AlbumPhoto::where('album_id', $albumId)->count() + 1,
                    ]);
                    
                    $results['success'][] = $file->getClientOriginalName();
                } else {
                    $results['failed'][] = [
                        'name' => $file->getClientOriginalName(),
                        'error' => $result['message'] ?? '上传失败'
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'message' => count($results['success']) . ' 张图片上传成功，' . count($results['failed']) . ' 张图片上传失败'
        ]);
    }

    public function deletePhoto($photoId)
    {
        $photo = AlbumPhoto::findOrFail($photoId);
        $album = $photo->album;

        if ($album->user_id != Auth::id()) {
            abort(403);
        }

        $this->picBedService->delete($photo->image_url);
        $photo->delete();

        return response()->json(['success' => true, 'message' => '图片删除成功']);
    }

    public function purchase(Request $request, $albumId)
    {
        $album = UserAlbum::findOrFail($albumId);
        
        if ($album->privacy) {
            return response()->json(['success' => false, 'message' => '该相册是公开的，无需购买'], 400);
        }

        if ($album->user_id == Auth::id()) {
            return response()->json(['success' => false, 'message' => '不能购买自己的相册'], 400);
        }

        $user = Auth::user();
        
        if ($user->coins < $album->price) {
            return response()->json(['success' => false, 'message' => '金币不足'], 400);
        }

        $existingPurchase = AlbumPurchase::where('album_id', $albumId)
            ->where('buyer_id', Auth::id())
            ->where('status', 1)
            ->first();

        if ($existingPurchase) {
            return response()->json(['success' => false, 'message' => '您已购买过该相册'], 400);
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
                'buyer_id' => Auth::id(),
                'seller_id' => $album->user_id,
                'price' => $album->price,
                'seller_earned' => $sellerEarned,
                'platform_earned' => $platformEarned,
                'expires_at' => now()->addDays(30),
            ]);

            $album->increment('purchase_count');

            DB::commit();

            return response()->json(['success' => true, 'message' => '购买成功']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => '购买失败: ' . $e->getMessage()], 500);
        }
    }

    public function checkPurchase($albumId)
    {
        $purchase = AlbumPurchase::where('album_id', $albumId)
            ->where('buyer_id', Auth::id())
            ->where('status', 1)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        return response()->json(['purchased' => !!$purchase]);
    }
}
