<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\StatusComment;
use App\Models\StatusLike;
use App\Models\UserAlbum;
use App\Models\User;
use App\Services\PicBedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    protected $picBedService;

    public function __construct(PicBedService $picBedService)
    {
        $this->picBedService = $picBedService;
    }

    public function index($userId)
    {
        $user = User::findOrFail($userId);
        
        $statuses = Status::where('user_id', $userId)
            ->where('status', 1)
            ->where(function ($query) use ($userId) {
                if ($userId != Auth::id()) {
                    $query->where('is_private', false);
                }
            })
            ->orderBy('created_at', 'desc')
            ->with(['comments' => function ($query) {
                $query->where('status', 1)
                    ->orderBy('created_at', 'desc')
                    ->with('user');
            }])
            ->get();

        $likedStatusIds = StatusLike::where('user_id', Auth::id())
            ->whereIn('status_id', $statuses->pluck('id'))
            ->pluck('status_id')
            ->toArray();

        foreach ($statuses as $status) {
            $status->liked = in_array($status->id, $likedStatusIds);
        }

        $albums = UserAlbum::where('user_id', $userId)
            ->where('status', 1)
            ->get();

        return view('profile', compact('user', 'statuses', 'albums'));
    }

    public function section($userId, $section)
    {
        $user = User::findOrFail($userId);
        
        switch ($section) {
            case 'profile':
                return view('profile.sections.profile', compact('user'));
                break;
                
            case 'statuses':
                $statuses = Status::where('user_id', $userId)
                    ->where('status', 1)
                    ->where(function ($query) use ($userId) {
                        if ($userId != Auth::id()) {
                            $query->where('is_private', false);
                        }
                    })
                    ->orderBy('created_at', 'desc')
                    ->with(['comments' => function ($query) {
                        $query->where('status', 1)
                            ->orderBy('created_at', 'desc')
                            ->with('user');
                    }])
                    ->get();

                $likedStatusIds = StatusLike::where('user_id', Auth::id())
                    ->whereIn('status_id', $statuses->pluck('id'))
                    ->pluck('status_id')
                    ->toArray();

                foreach ($statuses as $status) {
                    $status->liked = in_array($status->id, $likedStatusIds);
                }
                
                return view('profile.sections.statuses', compact('user', 'statuses'));
                break;
                
            case 'albums':
                $albums = UserAlbum::where('user_id', $userId)
                    ->where('status', 1)
                    ->get();
                
                return view('profile.sections.albums', compact('user', 'albums'));
                break;
                
            case 'edit':
                return view('profile.sections.edit', compact('user'));
                break;
                
            default:
                abort(404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string|max:2000',
            'images.*' => 'image|max:5120',
        ]);

        $images = [];
        $uploadErrors = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $result = $this->picBedService->upload($file->getRealPath());
                if ($result['success']) {
                    $images[] = $result['url'];
                } else {
                    $uploadErrors[] = $result['message'] ?? '图片上传失败';
                }
            }
        }

        Status::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'images' => $images,
            'is_private' => $request->has('is_private'),
        ]);

        if (!empty($uploadErrors)) {
            return redirect()->back()->with('success', '发布成功，但部分图片上传失败: ' . implode(', ', $uploadErrors));
        }
        
        return redirect()->back()->with('success', '发布成功');
    }

    public function like($statusId)
    {
        $status = Status::findOrFail($statusId);
        $like = StatusLike::where('status_id', $statusId)->where('user_id', Auth::id())->first();

        if ($like) {
            $like->delete();
            $status->decrement('likes_count');
            return response()->json(['success' => true, 'liked' => false, 'count' => $status->likes_count]);
        }

        StatusLike::create([
            'status_id' => $statusId,
            'user_id' => Auth::id(),
        ]);
        $status->increment('likes_count');

        return response()->json(['success' => true, 'liked' => true, 'count' => $status->likes_count]);
    }

    public function comment(Request $request, $statusId)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        StatusComment::create([
            'status_id' => $statusId,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id ?? null,
        ]);

        $status = Status::findOrFail($statusId);
        $status->increment('comments_count');

        return response()->json(['success' => true]);
    }

    public function destroy($statusId)
    {
        $status = Status::findOrFail($statusId);
        
        if ($status->user_id != Auth::id()) {
            return response()->json(['success' => false, 'message' => '无权删除此说说'], 403);
        }
        
        $status->delete();
        
        return response()->json(['success' => true]);
    }
}
