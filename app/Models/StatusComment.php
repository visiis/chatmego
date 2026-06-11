<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_id',
        'user_id',
        'content',
        'parent_id',
        'status',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(StatusComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(StatusComment::class, 'parent_id');
    }
}
