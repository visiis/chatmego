@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <i class="fas fa-exclamation-triangle"></i> 账户待激活
                </div>

                <div class="card-body text-center">
                    <div class="alert alert-warning" role="alert">
                        <h4 class="alert-heading">
                            <i class="fas fa-user-clock"></i> 账户未激活
                        </h4>
                        <p class="mb-0">
                            您的账户还未激活，请联系管理员激活账户。
                        </p>
                    </div>

                    <hr>

                    <h5 class="mb-4">
                        <i class="fas fa-user-shield"></i> 管理员列表
                    </h5>

                    @if($admins->isEmpty())
                        <div class="alert alert-info">
                            暂无管理员信息
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($admins as $admin)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        @if($admin->avatar)
                                            <img src="{{ avatar_url($admin->avatar) }}" 
                                                 alt="{{ $admin->name }}" 
                                                 class="rounded-circle me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('images/default-avatar.svg') }}" 
                                                 alt="{{ $admin->name }}" 
                                                 class="rounded-circle me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $admin->name }}</h6>
                                            <small class="text-muted">{{ $admin->email }}</small>
                                        </div>
                                    </div>
                                    <a href="{{ route('chat.show', $admin->id) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-comments"></i> 联系管理员
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <hr>

                    <div class="mt-4">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-sign-out-alt"></i> 退出登录
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
