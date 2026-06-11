<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlbumPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id',
        'buyer_id',
        'seller_id',
        'price',
        'seller_earned',
        'platform_earned',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(UserAlbum::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status ? '有效' : '无效';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }
}
