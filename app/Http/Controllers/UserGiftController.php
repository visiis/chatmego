<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\UserGift;
use App\Models\GiftRedemption;
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
        
        // 获取用户的礼物
        $allUserGifts = UserGift::with('gift')
            ->where('user_id', $user->id)
            ->where('is_redeemed', false)
            ->where('quantity', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // 分别处理实体礼物和虚拟礼物
        $physicalGifts = $allUserGifts->filter(function($userGift) {
            return $userGift->gift->type === 'physical';
        })->flatMap(function($userGift) {
            // 实体礼物：按数量展开为多条记录
            $items = [];
            for ($i = 0; $i < $userGift->quantity; $i++) {
                $clone = clone $userGift;
                $items[] = $clone;
            }
            return $items;
        })->values();
        
        $virtualGifts = $allUserGifts->filter(function($userGift) {
            return $userGift->gift->type === 'virtual';
        })->values();
        
        // 检查用户是否已填写兑换信息
        $hasRedemptionInfo = !empty($user->recipient_name) && !empty($user->phone) && !empty($user->address);
        
        return view('user.gifts.index', compact('virtualGifts', 'physicalGifts', 'hasRedemptionInfo'));
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
    
    /**
     * 提交实体礼物兑换申请
     */
    public function storeRedeem(Request $request, UserGift $userGift)
    {
        $user = auth()->user();
        
        // 验证权限
        if ($userGift->user_id !== $user->id) {
            return back()->with('error', '无权操作此礼物');
        }
        
        // 获取该用户此礼物的所有记录
        $allUserGifts = UserGift::where('user_id', $user->id)
            ->where('gift_id', $userGift->gift_id)
            ->orderBy('created_at')
            ->get();
        
        // 计算总可用数量
        $totalAvailableQuantity = $allUserGifts->sum(function($item) {
            return ($item->is_redeemed || $item->quantity <= 0) ? 0 : $item->quantity;
        });
        
        if ($totalAvailableQuantity <= 0) {
            return back()->with('error', '此礼物已无可用数量');
        }
        
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $totalAvailableQuantity,
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'recipient_phone' => 'nullable|string|max:20',
        ]);
        
        $redeemQuantity = $validated['quantity'];
        
        DB::beginTransaction();
        try {
            // 创建兑换申请（使用第一条还有数量的记录）
            $mainUserGift = $allUserGifts->first(function($item) {
                return !$item->is_redeemed && $item->quantity > 0;
            });
            
            GiftRedemption::create([
                'user_id' => $user->id,
                'user_gift_id' => $mainUserGift->id,
                'gift_id' => $userGift->gift_id,
                'recipient_name' => $validated['recipient_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'recipient_phone' => $validated['recipient_phone'] ?? null,
                'quantity' => $redeemQuantity,
                'status' => 'pending',
            ]);
            
            // 从所有记录中扣减数量（按创建时间顺序）
            $remainingToDeduct = $redeemQuantity;
            foreach ($allUserGifts as $ug) {
                if ($remainingToDeduct <= 0) {
                    break;
                }
                
                // 跳过已兑换或数量为 0 的记录
                if ($ug->is_redeemed || $ug->quantity <= 0) {
                    continue;
                }
                
                // 计算从这条记录扣减多少
                $deductFromThis = min($ug->quantity, $remainingToDeduct);
                $ug->quantity -= $deductFromThis;
                $remainingToDeduct -= $deductFromThis;
                
                // 只有当这条记录数量完全用完时，才标记为已兑换
                if ($ug->quantity <= 0) {
                    $ug->is_redeemed = true;
                    $ug->quantity = 0;
                }
                
                $ug->save();
            }
            
            DB::commit();
            
            return back()->with('success', '兑换申请已提交，请在后台查看处理进度！');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '兑换失败，请稍后重试');
        }
    }
    
    /**
     * 保存兑换信息到用户表
     */
    public function saveRedemptionInfo(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'recipient_phone' => 'nullable|string|max:20',
        ]);
        
        // 保存到用户表
        $user->update([
            'recipient_name' => $validated['recipient_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'recipient_phone' => $validated['recipient_phone'] ?? null,
        ]);
        
        return response()->json(['success' => true, 'message' => '兑换信息已保存']);
    }
    
    /**
     * 批量兑换选中的礼物
     */
    public function redeemMultiple(Request $request)
    {
        $user = auth()->user();
        $userGiftIds = $request->input('user_gift_ids', []);
        
        if (empty($userGiftIds)) {
            return back()->with('error', '请选择要兑换的礼物');
        }
        
        // 检查是否已填写兑换信息
        if (empty($user->recipient_name) || empty($user->phone) || empty($user->address)) {
            return back()->with('error', '请先填写兑换信息（点击"填写兑换信息"按钮）');
        }
        
        DB::beginTransaction();
        try {
            foreach ($userGiftIds as $userGiftId) {
                $userGift = UserGift::where('id', $userGiftId)
                    ->where('user_id', $user->id)
                    ->first();
                
                if (!$userGift || $userGift->is_redeemed || $userGift->quantity <= 0) {
                    continue;
                }
                
                // 创建兑换申请
                GiftRedemption::create([
                    'user_id' => $user->id,
                    'user_gift_id' => $userGift->id,
                    'gift_id' => $userGift->gift_id,
                    'recipient_name' => $user->recipient_name,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'recipient_phone' => $user->recipient_phone ?? null,
                    'quantity' => 1,
                    'status' => 'pending',
                ]);
                
                // 扣减数量
                $userGift->quantity -= 1;
                if ($userGift->quantity <= 0) {
                    $userGift->is_redeemed = true;
                    $userGift->quantity = 0;
                }
                $userGift->save();
            }
            
            DB::commit();
            
            return back()->with('success', '兑换申请已提交，请在后台查看处理进度！');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '兑换失败，请稍后重试');
        }
    }
}
