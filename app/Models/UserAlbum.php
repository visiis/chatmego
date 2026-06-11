<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAlbum extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'privacy',
        'price',
        'view_count',
        'purchase_count',
        'status',
    ];

    protected $casts = [
        'privacy' => 'boolean',
        'status' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(AlbumPhoto::class, 'album_id')->orderBy('sort_order');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(AlbumPurchase::class);
    }

    public function getPrivacyLabelAttribute(): string
    {
        return $this->privacy ? '公开' : '隐藏';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status ? '启用' : '禁用';
    }
}
