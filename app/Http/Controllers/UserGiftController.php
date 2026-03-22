<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\UserGift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserGiftController extends Controller
{
    /**
     * 显示用户的礼物列表
     */
    public function index()
    {
        $user = auth()->user();
        
        // 获取用户的礼物，按礼物类型分组
        $userGifts = UserGift::with('gift')
            ->where('user_id', $user->id)
            ->where('is_redeemed', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($userGift) {
                return $userGift->gift->type;
            });
        
        $virtualGifts = $userGifts['virtual'] ?? collect();
        $physicalGifts = $userGifts['physical'] ?? collect();
        
        return view('user.gifts.index', compact('virtualGifts', 'physicalGifts'));
    }
    
    /**
     * 购买礼物（用活跃度或金币）
     */
    public function purchase(Request $request, Gift $gift)
    {
        $user = auth()->user();
        
        // 检查礼物是否启用
        if (!$gift->is_active) {
            return back()->with('error', __('messages.gifts.redeem_error'));
        }
        
        DB::beginTransaction();
        try {
            // 检查余额
            if ($gift->price_type === 'activity_points') {
                if ($user->activity_points < $gift->price) {
                    return back()->with('error', 'Insufficient activity points');
                }
                $user->activity_points -= $gift->price;
            } else {
                if ($user->coins < $gift->price) {
                    return back()->with('error', 'Insufficient coins');
                }
                $user->coins -= $gift->price;
            }
            
            // 检查用户是否已有此礼物
            $existingUserGift = UserGift::where('user_id', $user->id)
                ->where('gift_id', $gift->id)
                ->where('is_redeemed', false)
                ->first();
            
            if ($existingUserGift) {
                // 增加数量
                $existingUserGift->increment('quantity');
            } else {
                // 创建新记录
                UserGift::create([
                    'user_id' => $user->id,
                    'gift_id' => $gift->id,
                    'quantity' => 1,
                    'is_redeemed' => false,
                ]);
            }
            
            $user->save();
            DB::commit();
            
            return back()->with('success', __('messages.gifts.redeem_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('messages.gifts.redeem_error'));
        }
    }
}
