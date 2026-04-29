@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">
        <i class="fas fa-clock"></i> 待处理的好友请求
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

    @if($requests->isEmpty())
        <div class="alert alert-info text-center">
            <i class="fas fa-check-circle fa-3x mb-3"></i>
            <h4>没有待处理的好友请求</h4>
            <p>当有人向你发送好友请求时，会显示在这里</p>
        </div>
    @else
        <div class="row">
            @foreach($requests as $request)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    @if($request->user->avatar)
                                        <div class="ratio ratio-1x1 d-inline-block" style="width: 80px; height: 80px;">
                                            <img src="{{ avatar_url($request->user->avatar) }}" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                        </div>
                                    @else
                                        <div class="ratio ratio-1x1 d-inline-block" style="width: 80px; height: 80px;">
                                            <img src="{{ asset('images/default-avatar.svg') }}" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h3 class="mb-1">{{ $request->user->name }}</h3>
                                    <p class="text-muted mb-0">
                                        <small>请求时间：{{ $request->created_at->diffForHumans() }}</small>
                                    </p>
                                </div>
                                <div class="col-md-3">
                                    <form action="{{ route('friends.accept', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm mb-2">
                                            <i class="fas fa-check"></i> 接受
                                        </button>
                                    </form>
                                    <form action="{{ route('friends.reject', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm mb-2">
                                            <i class="fas fa-times"></i> 拒绝
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
