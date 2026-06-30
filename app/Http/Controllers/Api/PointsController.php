<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PointsController extends Controller
{
    /**
     * 获取积分记录
     */
    public function getHistory(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $records = \App\Models\PointsLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'records' => $records->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => $item->type,
                        'amount' => $item->amount,
                        'balance' => $item->balance,
                        'reason' => $item->reason,
                        'created_at' => $item->created_at ? $item->created_at->toISOString() : null
                    ];
                }),
                'total' => $records->total(),
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage()
            ]
        ]);
    }

    /**
     * 积分兑换金币
     */
    public function convertToCoins(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $request->validate([
            'points' => 'required|integer|min:100|max:100000'
        ]);

        $points = $request->input('points');

        if ($user->points < $points) {
            return response()->json([
                'code' => 400,
                'message' => '积分不足'
            ], 400);
        }

        if ($points % 100 !== 0) {
            return response()->json([
                'code' => 400,
                'message' => '兑换积分必须是100的倍数'
            ], 400);
        }

        $coins = floor($points / 100);
        
        \DB::transaction(function () use ($user, $points, $coins) {
            $user->convertPointsToCoins($points);
            
            \App\Models\PointsLog::create([
                'user_id' => $user->id,
                'type' => 'convert',
                'amount' => -$points,
                'balance' => $user->points,
                'reason' => '积分兑换金币 ' . $coins . ' 个'
            ]);
        });

        return response()->json([
            'code' => 200,
            'message' => '兑换成功',
            'data' => [
                'coins_obtained' => $coins,
                'remaining_points' => $user->points,
                'current_coins' => $user->coins
            ]
        ]);
    }

    /**
     * 获取金币记录
     */
    public function getCoinsHistory(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $records = \App\Models\CoinsLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'records' => $records->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => $item->type,
                        'amount' => $item->amount,
                        'balance' => $item->balance,
                        'reason' => $item->reason,
                        'created_at' => $item->created_at ? $item->created_at->toISOString() : null
                    ];
                }),
                'total' => $records->total(),
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage()
            ]
        ]);
    }

    protected function getUserFromToken(Request $request)
    {
        $token = $request->bearerToken() ?: $request->header('Authorization');
        
        if ($token && strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        if (!$token) {
            return null;
        }

        $user = \App\Models\User::where('api_token', $token)->first();
        
        if (!$user) {
            try {
                $decoded = \Firebase\JWT\JWT::decode(
                    $token,
                    new \Firebase\JWT\Key(env('JWT_SECRET'), 'HS256')
                );
                $user = \App\Models\User::find($decoded->sub);
            } catch (\Exception $e) {
                return null;
            }
        }

        return $user;
    }
}
