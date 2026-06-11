@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">
        <i class="fas fa-search"></i> 发现好友
    </h1>
    
    <!-- 搜索和筛选区域 -->
    <div class="card mb-4 shadow-sm rounded-xl overflow-hidden">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('home') }}" class="row align-items-end gap-3">
                <!-- 搜索框 -->
                <div class="col-md-3">
                    <label class="form-label text-gray-600 font-medium mb-2" style="font-size: 14px;">搜索</label>
                    <div class="input-group">
                        <span class="input-group-text bg-gray-100 border-gray-200" style="height: 40px;">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="用户名或简介..." value="{{ $search ?? '' }}" style="height: 40px; font-size: 14px;">
                    </div>
                </div>
                
                <!-- 性别筛选 -->
                <div class="col-md-1.5">
                    <label class="form-label text-gray-600 font-medium mb-2" style="font-size: 14px;">性别</label>
                    <select name="gender" class="form-select" style="height: 40px; font-size: 14px;">
                        <option value="">全部</option>
                        <option value="male" {{ ($gender ?? '') == 'male' ? 'selected' : '' }}>男</option>
                        <option value="female" {{ ($gender ?? '') == 'female' ? 'selected' : '' }}>女</option>
                    </select>
                </div>
                
                <!-- 年龄范围 -->
                <div class="col-md-2">
                    <label class="form-label text-gray-600 font-medium mb-2" style="font-size: 14px;">年龄</label>
                    <div class="d-flex items-center gap-1" style="height: 40px;">
                        <input type="number" name="min_age" class="form-control" placeholder="最小" min="18" max="100" value="{{ $minAge ?? '' }}" style="height: 40px; font-size: 14px; flex: 1;">
                        <span class="text-gray-400 px-1" style="font-size: 14px;">-</span>
                        <input type="number" name="max_age" class="form-control" placeholder="最大" min="18" max="100" value="{{ $maxAge ?? '' }}" style="height: 40px; font-size: 14px; flex: 1;">
                    </div>
                </div>
                
                <!-- 身高范围 -->
                <div class="col-md-2">
                    <label class="form-label text-gray-600 font-medium mb-2" style="font-size: 14px;">身高(cm)</label>
                    <div class="d-flex items-center gap-1" style="height: 40px;">
                        <input type="number" name="min_height" class="form-control" placeholder="最小" min="100" max="250" value="{{ $minHeight ?? '' }}" style="height: 40px; font-size: 14px; flex: 1;">
                        <span class="text-gray-400 px-1" style="font-size: 14px;">-</span>
                        <input type="number" name="max_height" class="form-control" placeholder="最大" min="100" max="250" value="{{ $maxHeight ?? '' }}" style="height: 40px; font-size: 14px; flex: 1;">
                    </div>
                </div>
                
                <!-- 会员等级 -->
                <div class="col-md-1.5">
                    <label class="form-label text-gray-600 font-medium mb-2" style="font-size: 14px;">会员</label>
                    <select name="member_level" class="form-select" style="height: 40px; font-size: 14px;">
                        <option value="">全部</option>
                        <option value="1" {{ ($memberLevel ?? '') == '1' ? 'selected' : '' }}>VIP1</option>
                        <option value="2" {{ ($memberLevel ?? '') == '2' ? 'selected' : '' }}>VIP2</option>
                        <option value="3" {{ ($memberLevel ?? '') == '3' ? 'selected' : '' }}>VIP3</option>
                        <option value="4" {{ ($memberLevel ?? '') == '4' ? 'selected' : '' }}>VIP4</option>
                        <option value="5" {{ ($memberLevel ?? '') == '5' ? 'selected' : '' }}>VIP5</option>
                    </select>
                </div>
                
                <!-- 操作按钮 -->
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-1" style="height: 40px; font-size: 14px; font-weight: 500;">
                        <i class="fas fa-filter me-1"></i> 筛选
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-outline-gray-300 flex-1" style="height: 40px; font-size: 14px;">
                        <i class="fas fa-undo me-1"></i> 重置
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    @if($users->isEmpty())
        <div class="alert alert-info text-center">
            {{ __('messages.users.no_users') }}
        </div>
    @else
        <div class="row g-4">
            @foreach($users as $user)
                <div class="col-md-4 col-lg-3">
                    <div class="card text-center shadow-sm h-100">
                        <div class="card-body">
                            <!-- 头像 -->
                            <div class="mb-3" style="width: 120px; height: 120px; margin: 0 auto;">
                                @if($user->avatar)
                                    <div class="avatar-container w-100 h-100">
                                        <img src="{{ avatar_url($user->avatar) }}" loading="lazy" class="lazy-image rounded-circle w-100 h-100 object-fit-cover" style="border: 3px solid #e9ecef;" alt="{{ $user->name }}">
                                    </div>
                                @else
                                    <img src="{{ asset('images/default-avatar.svg') }}" class="rounded-circle w-100 h-100 object-fit-cover" style="border: 3px solid #e9ecef;" alt="{{ $user->name }}">
                                @endif
                            </div>
                            
                            <h5 class="card-title">{{ $user->name }}</h5>
                            
                            <!-- 会员等级徽章 -->
                            <div class="mb-2">
                                <x-member-level-badge :user="$user" />
                            </div>
                            
                            <p class="text-muted small mb-1">
                                {{ $user->gender == 'male' ? __('messages.profile.male') : __('messages.profile.female') }} · {{ $user->age }} {{ __('messages.profile.age') }}
                            </p>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-ruler-vertical"></i> {{ $user->height }} cm · 
                                <i class="fas fa-weight"></i> {{ $user->weight }} kg
                            </p>
                            
                            <!-- 操作按钮 -->
                            <div class="mt-4">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('profile.show', $user->id) }}" class="btn btn-primary btn-block">
                                        <i class="fas fa-home"></i> {{ __('messages.users.view_profile') }}
                                    </a>
                                    @if(auth()->id() != $user->id)
                                        <a href="{{ route('chat.show', $user->id) }}" class="btn btn-success btn-block">
                                            <i class="fas fa-comments"></i> 立即聊天
                                        </a>
                                        @if(isset($user->friendship_status) && $user->friendship_status === 'accepted')
                                            <button type="button" class="btn btn-secondary btn-block" disabled>
                                                <i class="fas fa-check"></i> 已是好友
                                            </button>
                                        @elseif(isset($user->friendship_status) && $user->friendship_status === 'pending' && $user->requester_id === auth()->id())
                                            <button type="button" class="btn btn-secondary btn-block" disabled>
                                                <i class="fas fa-clock"></i> 已发送
                                            </button>
                                        @else
                                            <form action="{{ route('friends.request', $user->id) }}" method="POST" class="friend-request-form">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-block friend-request-btn">
                                                    <i class="fas fa-user-plus"></i> 加入好友
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    body {
        background: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .card {
        border-radius: 15px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 处理好友申请表单提交
    const forms = document.querySelectorAll('.friend-request-form');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = form.querySelector('.friend-request-btn');
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            // 禁用按钮
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-clock"></i> 已发送';
            button.classList.remove('btn-success');
            button.classList.add('btn-secondary');
            
            // 发送请求
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                console.log('Response data:', data);
                if (data.success) {
                    console.log('Success: replacing form with static button');
                    // 成功：替换整个表单为静态文本
                    const staticButton = document.createElement('button');
                    staticButton.type = 'button';
                    staticButton.className = 'btn btn-secondary btn-user-action';
                    staticButton.disabled = true;
                    staticButton.innerHTML = '<i class="fas fa-clock"></i> 已发送';
                    form.parentNode.replaceChild(staticButton, form);
                    alert('好友申请已经提交！');
                } else {
                    console.log('Failed:', data.message);
                    // 失败：恢复按钮
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-user-plus"></i> 加入好友';
                    button.classList.remove('btn-secondary');
                    button.classList.add('btn-success');
                    alert(data.message || '发送失败');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                // 网络错误：恢复按钮
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-user-plus"></i> 加入好友';
                button.classList.remove('btn-secondary');
                button.classList.add('btn-success');
                alert('发送失败，请重试');
            });
        });
    });
});
</script>
@endpush