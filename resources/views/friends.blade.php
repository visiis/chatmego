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
        <div class="row justify-content-center">
            <div class="col-md-8">
                @foreach($friends as $friend)
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    @if($friend->avatar)
                                        <img src="{{ avatar_url($friend->avatar) }}" alt="Avatar" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/default-avatar.svg') }}" alt="Default Avatar" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                    @endif
                                </div>
                                <div class="col-md-5">
                                    <h5 class="mb-0">{{ $friend->name }}</h5>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('chat.show', $friend->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-comment"></i> 发消息
                                        </a>
                                        <a href="{{ route('profile', $friend->id) }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-user"></i> 资料
                                        </a>
                                        <form action="{{ route('friends.remove', $friend->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('确定要删除这位好友吗？')">
                                                <i class="fas fa-user-minus"></i> 删除
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
