<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gift extends Model
{
    protected $fillable = [
        'name',
        'type',
        'price_type',
        'price',
        'image',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'integer',
    ];

    /**
     * 获取用户礼物
     */
    public function userGifts(): HasMany
    {
        return $this->hasMany(UserGift::class);
    }

    /**
     * 获取兑换记录
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(GiftRedemption::class);
    }

    /**
     * 获取翻译后的类型
     */
    public function getTranslatedTypeAttribute(): string
    {
        return $this->type === 'virtual' 
            ? __('messages.gifts.types.virtual') 
            : __('messages.gifts.types.physical');
    }

    /**
     * 获取翻译后的价格类型
     */
    public function getTranslatedPriceTypeAttribute(): string
    {
        return $this->price_type === 'activity_points' 
            ? __('messages.gifts.activity_points') 
            : __('messages.gifts.coins');
    }
}
