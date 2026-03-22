<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserGift extends Model
{
    protected $fillable = [
        'user_id',
        'gift_id',
        'quantity',
        'is_redeemed',
    ];

    protected $casts = [
        'is_redeemed' => 'boolean',
        'quantity' => 'integer',
    ];

    /**
     * 获取礼物
     */
    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }

    /**
     * 获取用户
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取兑换记录
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(GiftRedemption::class);
    }

    /**
     * 获取显示用的数量（如果有多个）
     */
    public function getDisplayQuantityAttribute(): string
    {
        return $this->quantity > 1 ? "×{$this->quantity}" : '';
    }
}
