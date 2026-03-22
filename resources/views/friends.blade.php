@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">
        <i class="fas fa-users"></i> 我的好友
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

    @if($friends->isEmpty())
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-3x mb-3"></i>
            <h4>还没有好友</h4>
            <p>去发现页面找找新朋友吧！</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-2">
                <i class="fas fa-compass"></i> 发现
            </a>
        </div>
    @else
        <div class="row">
            @foreach($friends as $friend)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <!-- 头像 -->
                            <div class="mb-3">
                                @if($friend->avatar)
                                    <div class="ratio ratio-1x1 d-inline-block" style="width: 150px; height: 150px;">
                                        <img src="{{ asset('storage/' . $friend->avatar) }}" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                    </div>
                                @else
                                    <div class="ratio ratio-1x1 d-inline-block" style="width: 150px; height: 150px;">
                                        <img src="{{ asset('images/default-avatar.svg') }}" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                    </div>
                                @endif
                            </div>

                            <!-- 名字 -->
                            <h3 class="card-title mb-4">{{ $friend->name }}</h3>

                            <!-- 按钮 -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('chat.show', $friend->id) }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-comment"></i> 发消息
                                </a>
                                <a href="{{ route('profile', $friend->id) }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-user"></i> 资料
                                </a>
                                <form action="{{ route('friends.remove', $friend->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-lg" onclick="return confirm('确定要删除这位好友吗？')">
                                        <i class="fas fa-user-minus"></i> 删除
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
