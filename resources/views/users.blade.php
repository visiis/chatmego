@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-4">{{ __('messages.users.title') }}</h2>
    
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
                            <div class="mb-3">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle mx-auto d-block" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e9ecef;" alt="{{ $user->name }}">
                                @else
                                    <img src="{{ asset('images/default-avatar.svg') }}" class="rounded-circle mx-auto d-block" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e9ecef;" alt="{{ $user->name }}">
                                @endif
                            </div>
                            
                            <h5 class="card-title">{{ $user->name }}</h5>
                            <p class="text-muted small mb-1">
                                {{ $user->gender == 'male' ? __('messages.profile.male') : __('messages.profile.female') }} · {{ $user->age }} {{ __('messages.profile.age') }}
                            </p>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-ruler-vertical"></i> {{ $user->height }} cm · 
                                <i class="fas fa-weight"></i> {{ $user->weight }} kg
                            </p>
                            
                            <!-- 操作按钮 -->
                            <div class="mt-4 text-center">
                                <a href="{{ route('profile', $user->id) }}" class="btn btn-primary">
                                    {{ __('messages.users.view_profile') }}
                                </a>
                                @if(auth()->id() != $user->id)
                                    <a href="{{ route('chat.show', $user->id) }}" class="btn btn-success">
                                        <i class="fas fa-comments"></i> 立即聊天
                                    </a>
                                    @if(isset($user->friendship_status) && $user->friendship_status === 'accepted')
                                        <button type="button" class="btn btn-secondary" disabled>
                                            <i class="fas fa-check"></i> 已是好友
                                        </button>
                                    @elseif(isset($user->friendship_status) && $user->friendship_status === 'pending' && $user->requester_id === $user->id)
                                        <button type="button" class="btn btn-secondary" disabled>
                                            <i class="fas fa-clock"></i> 已发送
                                        </button>
                                    @else
                                        <form action="{{ route('friends.request', $user->id) }}" method="POST" class="d-inline friend-request-form">
                                            @csrf
                                            <button type="submit" class="btn btn-success friend-request-btn">
                                                <i class="fas fa-user-plus"></i> 加入好友
                                            </button>
                                        </form>
                                    @endif
                                @endif
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