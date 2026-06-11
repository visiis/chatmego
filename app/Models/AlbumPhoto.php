<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlbumPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id',
        'image_url',
        'thumbnail_url',
        'title',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(UserAlbum::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status ? '启用' : '禁用';
    }
}
