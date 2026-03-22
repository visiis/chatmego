<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftRedemption extends Model
{
    protected $fillable = [
        'user_id',
        'gift_id',
        'user_gift_id',
        'recipient_name',
        'phone',
        'address',
        'recipient_phone',
        'quantity',
        'status',
        'admin_notes',
    ];

    /**
     * 获取用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取礼物
     */
    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }

    /**
     * 获取用户礼物
     */
    public function userGift(): BelongsTo
    {
        return $this->belongsTo(UserGift::class);
    }

    /**
     * 获取翻译后的状态
     */
    public function getTranslatedStatusAttribute(): string
    {
        return match($this->status) {
            'pending' => __('messages.gifts.status.pending'),
            'processing' => __('messages.gifts.status.processing'),
            'shipped' => __('messages.gifts.status.shipped'),
            'completed' => __('messages.gifts.status.completed'),
            'cancelled' => __('messages.gifts.status.cancelled'),
            default => $this->status,
        };
    }
}
