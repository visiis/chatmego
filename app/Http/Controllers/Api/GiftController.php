<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\GiftRedemption;
use App\Models\User;
use App\Models\UserGift;
use Illuminate\Http\Request;

class GiftController extends Controller
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

    public function getUserGifts(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $physicalGifts = UserGift::where('user_id', $user->id)
            ->where('is_redeemed', false)
            ->with('gift')
            ->whereHas('gift', function ($query) {
                $query->where('type', 'physical');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $virtualGifts = UserGift::where('user_id', $user->id)
            ->where('is_redeemed', false)
            ->with('gift')
            ->whereHas('gift', function ($query) {
                $query->where('type', 'virtual');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $hasRedemptionInfo = !empty($user->recipient_name) && !empty($user->phone) && !empty($user->address);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => [
                'physical_gifts' => $physicalGifts->map(function ($ug) {
                    return [
                        'id' => $ug->id,
                        'gift_id' => $ug->gift->id,
                        'name' => $ug->gift->name,
                        'image' => $ug->gift->image,
                        'description' => $ug->gift->description,
                        'price_type' => $ug->gift->price_type,
                        'price' => $ug->gift->price,
                        'quantity' => $ug->quantity,
                        'created_at' => $ug->created_at ? $ug->created_at->toISOString() : null
                    ];
                }),
                'virtual_gifts' => $virtualGifts->map(function ($ug) {
                    return [
                        'id' => $ug->id,
                        'gift_id' => $ug->gift->id,
                        'name' => $ug->gift->name,
                        'image' => $ug->gift->image,
                        'description' => $ug->gift->description,
                        'price_type' => $ug->gift->price_type,
                        'price' => $ug->gift->price,
                        'quantity' => $ug->quantity,
                        'created_at' => $ug->created_at ? $ug->created_at->toISOString() : null
                    ];
                }),
                'has_redemption_info' => $hasRedemptionInfo
            ]
        ]);
    }

    public function getRedemptionHistory(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $redemptions = GiftRedemption::where('user_id', $user->id)
            ->with('gift')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $redemptions->map(function ($r) {
                $statusLabels = [
                    'pending' => '待处理',
                    'processing' => '处理中',
                    'shipped' => '已发货',
                    'completed' => '已完成',
                    'cancelled' => '已取消',
                ];
                
                return [
                    'id' => $r->id,
                    'gift_id' => $r->gift->id,
                    'gift_name' => $r->gift->name,
                    'gift_image' => $r->gift->image,
                    'quantity' => $r->quantity,
                    'recipient_name' => $r->recipient_name,
                    'phone' => $r->phone,
                    'address' => $r->address,
                    'recipient_phone' => $r->recipient_phone,
                    'status' => $r->status,
                    'status_label' => $statusLabels[$r->status] ?? $r->status,
                    'created_at' => $r->created_at ? $r->created_at->toISOString() : null
                ];
            })
        ]);
    }

    public function saveRedemptionInfo(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'recipient_phone' => 'nullable|string|max:20',
        ]);

        $user->fill($validated);
        $user->save();

        return response()->json([
            'code' => 200,
            'message' => '保存成功',
            'data' => [
                'has_redemption_info' => true
            ]
        ]);
    }

    public function redeem(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $validated = $request->validate([
            'user_gift_ids' => 'required|array|min:1',
            'user_gift_ids.*' => 'integer|exists:user_gifts,id',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'recipient_phone' => 'nullable|string|max:20',
        ]);

        foreach ($validated['user_gift_ids'] as $userGiftId) {
            $userGift = UserGift::find($userGiftId);
            
            if (!$userGift || $userGift->user_id != $user->id || $userGift->is_redeemed) {
                continue;
            }

            GiftRedemption::create([
                'user_id' => $user->id,
                'gift_id' => $userGift->gift_id,
                'user_gift_id' => $userGift->id,
                'recipient_name' => $validated['recipient_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'recipient_phone' => $validated['recipient_phone'],
                'quantity' => $userGift->quantity,
                'status' => 'pending',
            ]);

            $userGift->is_redeemed = true;
            $userGift->save();
        }

        return response()->json([
            'code' => 200,
            'message' => '兑换成功'
        ]);
    }

    public function getAllGifts(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        if (!$user) {
            return response()->json(['message' => '未授权'], 401);
        }

        $gifts = Gift::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $gifts->map(function ($gift) {
                return [
                    'id' => $gift->id,
                    'name' => $gift->name,
                    'type' => $gift->type,
                    'price_type' => $gift->price_type,
                    'price' => $gift->price,
                    'image' => $gift->image,
                    'description' => $gift->description,
                    'is_active' => $gift->is_active,
                    'sort_order' => $gift->sort_order,
                ];
            })
        ]);
    }
}
