<?php

namespace App\Http\Controllers;

use App\Models\GiftRedemption;
use App\Models\UserGift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftRedemptionController extends Controller
{
    /**
     * 显示兑换表单
     */
    public function create(UserGift $userGift)
    {
        $user = auth()->user();
        
        // 验证礼物属于用户且是实体礼物且未兑换
        if ($userGift->user_id !== $user->id 
            || $userGift->gift->type !== 'physical' 
            || $userGift->is_redeemed) {
            return redirect()->route('user.gifts.index')
                ->with('error', __('messages.gifts.already_redeemed'));
        }
        
        return view('user.gifts.redeem', compact('userGift'));
    }
    
    /**
     * 处理兑换
     */
    public function store(Request $request, UserGift $userGift)
    {
        $user = auth()->user();
        
        // 验证
        if ($userGift->user_id !== $user->id 
            || $userGift->gift->type !== 'physical' 
            || $userGift->is_redeemed) {
            return back()->with('error', __('messages.gifts.already_redeemed'));
        }
        
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);
        
        DB::beginTransaction();
        try {
            // 创建兑换记录
            GiftRedemption::create([
                'user_id' => $user->id,
                'gift_id' => $userGift->gift_id,
                'user_gift_id' => $userGift->id,
                'recipient_name' => $validated['recipient_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'status' => 'pending',
            ]);
            
            // 标记用户礼物为已兑换
            $userGift->update(['is_redeemed' => true]);
            
            DB::commit();
            
            return redirect()->route('user.gifts.index')
                ->with('success', __('messages.gifts.redeem_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('messages.gifts.redeem_error'));
        }
    }
    
    /**
     * 显示用户的兑换记录
     */
    public function history()
    {
        $user = auth()->user();
        
        $redemptions = GiftRedemption::with('gift')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('user.gifts.history', compact('redemptions'));
    }
}
