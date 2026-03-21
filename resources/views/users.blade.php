@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mb-4">{{ __('messages.users.title') }}</h1>
    
    @if($users->isEmpty())
        <div class="alert alert-info text-center">
            {{ __('messages.users.no_users') }}
        </div>
    @else
        <div class="row">
            @foreach($users as $user)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if($user->avatar)
                                    <div class="ratio ratio-1x1 d-inline-block" style="width: 180px; height: 180px;">
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                    </div>
                                @else
                                    <div class="ratio ratio-1x1 d-inline-block" style="width: 180px; height: 180px;">
                                        <img src="{{ asset('images/default-avatar.svg') }}" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                    </div>
                                @endif
                                <h3 class="mt-3">{{ $user->name }}</h3>
                            </div>
                            
                            <div class="mb-2">
                                <strong>{{ __('messages.profile.gender') }}:</strong> {{ $user->gender == 'male' ? __('messages.profile.male') : __('messages.profile.female') }}
                            </div>
                            <div class="mb-2">
                                <strong>{{ __('messages.profile.age') }}:</strong> {{ $user->age }}
                            </div>
                            <div class="mb-2">
                                <strong>{{ __('messages.profile.height') }}:</strong> {{ $user->height }} cm
                            </div>
                            <div class="mb-2">
                                <strong>{{ __('messages.profile.weight') }}:</strong> {{ $user->weight }} kg
                            </div>
                            
                            <div class="mt-4 text-center">
                                <a href="{{ route('profile', $user->id) }}" class="btn btn-user-action">
                                    {{ __('messages.users.view_profile') }}
                                </a>
                                @if(auth()->id() != $user->id)
                                    <a href="{{ route('chat.show', $user->id) }}" class="btn btn-user-action">
                                        <i class="fas fa-comments"></i> 立即聊天
                                    </a>
                                    @if(isset($user->friendship_status) && $user->friendship_status === 'accepted')
                                        <button type="button" class="btn btn-user-action" disabled>
                                            <i class="fas fa-check"></i> 已是好友
                                        </button>
                                    @elseif(isset($user->friendship_status) && $user->friendship_status === 'pending' && $user->requester_id === $user->id)
                                        <button type="button" class="btn btn-user-action" disabled>
                                            <i class="fas fa-clock"></i> 申请已发送
                                        </button>
                                    @else
                                        <form action="{{ route('friends.request', $user->id) }}" method="POST" class="d-inline friend-request-form">
                                            @csrf
                                            <button type="submit" class="btn btn-user-action friend-request-btn">
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
    .btn-user-action {
        min-width: 120px;
        padding: 10px 20px;
        margin: 4px;
        font-size: 16px;
        font-weight: 500;
        text-align: center;
        display: inline-block;
        white-space: nowrap;
        background-color: #007bff;
        border-color: #007bff;
        color: #fff !important;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-user-action:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }
    
    .btn-user-action:active {
        transform: translateY(0);
    }
    
    .btn-user-action[disabled] {
        background-color: #6c757d;
        border-color: #6c757d;
        cursor: not-allowed;
        opacity: 0.65;
    }
    
    .btn-user-action[disabled]:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        transform: none;
        box-shadow: none;
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
            button.innerHTML = '<i class="fas fa-clock"></i> 申请已发送';
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
                    staticButton.innerHTML = '<i class="fas fa-clock"></i> 申请已发送';
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