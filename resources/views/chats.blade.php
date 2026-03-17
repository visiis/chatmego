@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">
        <i class="fas fa-comments"></i> 我的聊天
    </h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(empty($chatList) || count($chatList) === 0)
        <div class="alert alert-info text-center">
            <i class="fas fa-comment-slash fa-3x mb-3"></i>
            <h4>还没有聊天</h4>
            <p>添加好友后就可以开始聊天了！</p>
            <a href="{{ route('friends') }}" class="btn btn-primary mt-2">
                <i class="fas fa-users"></i> 我的好友
            </a>
        </div>
    @else
        <div class="list-group">
            @foreach($chatList as $chat)
                @php
                    $friend = $chat['friend'];
                    $lastMessage = $chat['last_message'];
                    $unreadCount = $chat['unread_count'];
                @endphp
                <a href="{{ route('chat.show', $friend->id) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="position-relative me-3">
                                @if($friend->avatar)
                                    <div class="ratio ratio-1x1 d-inline-block" style="width: 60px; height: 60px;">
                                        <img src="{{ asset('storage/' . $friend->avatar) }}" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                    </div>
                                @else
                                    <div class="ratio ratio-1x1 d-inline-block" style="width: 60px; height: 60px;">
                                        <img src="{{ asset('images/default-avatar.svg') }}" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                    </div>
                                @endif
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $friend->name }}</h5>
                                @if($lastMessage)
                                    <p class="mb-1 text-muted">
                                        <small>
                                            @if($lastMessage->type === 'image')
                                                <i class="fas fa-image"></i> [图片]
                                            @elseif($lastMessage->type === 'voice')
                                                <i class="fas fa-microphone"></i> [语音]
                                            @elseif($lastMessage->type === 'video')
                                                <i class="fas fa-video"></i> [视频]
                                            @elseif($lastMessage->type === 'gift')
                                                <i class="fas fa-gift"></i> [礼物]
                                            @elseif($lastMessage->type === 'emoji')
                                                <i class="fas fa-smile"></i> [表情]
                                            @else
                                                {{ Str::limit($lastMessage->message, 30) }}
                                            @endif
                                        </small>
                                    </p>
                                @else
                                    <p class="mb-1 text-muted">
                                        <small>还没有消息</small>
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            @if($lastMessage)
                                <small class="text-muted">
                                    {{ $lastMessage->created_at->diffForHumans() }}
                                </small>
                            @endif
                            @if($unreadCount > 0)
                                <span class="badge bg-danger rounded-pill ms-2">{{ $unreadCount }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ route('friends') }}" class="btn btn-primary">
            <i class="fas fa-users"></i> 我的好友
        </a>
    </div>
</div>
@endsection
