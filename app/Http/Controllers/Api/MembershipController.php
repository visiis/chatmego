<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberLevel;
use App\Models\MembershipPlan;

class MembershipController extends Controller
{
    /**
     * 获取会员等级列表
     */
    public function levels()
    {
        $user = auth()->guard('api')->user();
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $levels = MemberLevel::orderBy('level_order')->get();

        $result = $levels->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'icon' => $item->icon,
                'level_order' => $item->level_order,
                'min_points' => $item->min_points,
                'max_points' => $item->max_points,
                'privileges' => json_decode($item->privileges, true) ?? []
            ];
        });

        return response()->json([
            'message' => 'success',
            'data' => $result
        ]);
    }
}
