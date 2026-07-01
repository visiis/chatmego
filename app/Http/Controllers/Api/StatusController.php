<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\StatusComment;
use App\Models\StatusLike;
use App\Models\User;
use Illuminate\Http\Request;

class StatusController extends Controller
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

    public function index(Request $request)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        $statuses = Status::where('status', 1)
            ->where('is_private', false)
            ->orderBy('created_at', 'desc')
            ->with(['user' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }, 'comments' => function ($query) {
                $query->where('status', 1)
                    ->orderBy('created_at', 'desc')
                    ->with(['user' => function ($q) {
                        $q->select('id', 'name', 'avatar');
                    }]);
            }])
            ->paginate($limit, ['*'], 'page', $page);

        $likedStatusIds = StatusLike::where('user_id', $user->id)
            ->whereIn('status_id', $statuses->pluck('id'))
            ->pluck('status_id')
            ->toArray();

        foreach ($statuses as $status) {
            $status->is_liked = in_array($status->id, $likedStatusIds);
            $status->user->avatar = $status->user->avatar_url;
            foreach ($status->comments as $comment) {
                $comment->user->avatar = $comment->user->avatar_url;
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $statuses->items(),
            'has_more' => $statuses->hasMorePages()
        ]);
    }

    public function getUserStatuses(Request $request, $userId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        $targetUser = User::find($userId);
        if (!$targetUser) {
            return response()->json(['code' => 404, 'message' => '用户不存在']);
        }

        $statuses = Status::where('user_id', $userId)
            ->where('status', 1)
            ->where(function ($query) use ($userId, $user) {
                if ($userId != $user->id) {
                    $query->where('is_private', false);
                }
            })
            ->orderBy('created_at', 'desc')
            ->with(['user' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }, 'comments' => function ($query) {
                $query->where('status', 1)
                    ->orderBy('created_at', 'desc')
                    ->with(['user' => function ($q) {
                        $q->select('id', 'name', 'avatar');
                    }]);
            }])
            ->get();

        $likedStatusIds = StatusLike::where('user_id', $user->id)
            ->whereIn('status_id', $statuses->pluck('id'))
            ->pluck('status_id')
            ->toArray();

        foreach ($statuses as $status) {
            $status->liked = in_array($status->id, $likedStatusIds);
            $status->is_liked = $status->liked;
            if ($status->user) {
                $status->user->avatar = $status->user->avatar_url;
            }
            foreach ($status->comments as $comment) {
                if ($comment->user) {
                    $comment->user->avatar = $comment->user->avatar_url;
                }
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $statuses
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        $request->validate([
            'content' => 'nullable|string|max:2000',
            'images' => 'nullable|array',
            'images.*' => 'string',
        ]);

        $status = Status::create([
            'user_id' => $user->id,
            'content' => $request->content,
            'images' => $request->images ?? [],
            'is_private' => $request->is_private ?? false,
        ]);

        return response()->json([
            'code' => 200,
            'message' => '发布成功',
            'data' => $status
        ]);
    }

    public function like(Request $request, $statusId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        $status = Status::find($statusId);
        if (!$status) {
            return response()->json(['code' => 404, 'message' => '说说不存在']);
        }

        $like = StatusLike::where('status_id', $statusId)->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $status->decrement('likes_count');
            return response()->json([
                'code' => 200,
                'message' => '取消点赞',
                'data' => ['liked' => false, 'count' => $status->likes_count]
            ]);
        }

        StatusLike::create([
            'status_id' => $statusId,
            'user_id' => $user->id,
        ]);
        $status->increment('likes_count');

        return response()->json([
            'code' => 200,
            'message' => '点赞成功',
            'data' => ['liked' => true, 'count' => $status->likes_count]
        ]);
    }

    public function comment(Request $request, $statusId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $status = Status::find($statusId);
        if (!$status) {
            return response()->json(['code' => 404, 'message' => '说说不存在']);
        }

        $comment = StatusComment::create([
            'status_id' => $statusId,
            'user_id' => $user->id,
            'content' => $request->content,
            'parent_id' => $request->parent_id ?? null,
        ]);

        $comment->load(['user' => function ($query) {
            $query->select('id', 'name', 'avatar');
        }]);
        
        if ($comment->user) {
            $comment->user->avatar = $comment->user->avatar_url;
        }

        $status->increment('comments_count');

        return response()->json([
            'code' => 200,
            'message' => '评论成功',
            'data' => $comment
        ]);
    }

    public function destroy(Request $request, $statusId)
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['code' => 401, 'message' => '未授权']);
        }

        $status = Status::find($statusId);
        if (!$status) {
            return response()->json(['code' => 404, 'message' => '说说不存在']);
        }

        if ($status->user_id != $user->id) {
            return response()->json(['code' => 403, 'message' => '无权删除此说说']);
        }

        $status->delete();

        return response()->json([
            'code' => 200,
            'message' => '删除成功'
        ]);
    }
}