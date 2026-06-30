<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance',
        'reason'
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance' => 'integer'
    ];
}
