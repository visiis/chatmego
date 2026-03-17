<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
    ];

    /**
     * 获取发起好友请求的用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取好友用户
     */
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    /**
     * 检查是否是好友
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * 检查是否等待中
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * 接受好友请求
     */
    public function accept()
    {
        $this->update(['status' => 'accepted']);
    }

    /**
     * 拒绝好友请求
     */
    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * 阻止用户
     */
    public function block()
    {
        $this->update(['status' => 'blocked']);
    }
}
