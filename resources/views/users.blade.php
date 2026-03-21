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
                                <a href="{{ route('profile', $user->id) }}" class="btn btn-primary">
                                    {{ __('messages.users.view_profile') }}
                                </a>
                                @if(auth()->id() != $user->id)
                                    <form action="{{ route('friends.request', $user->id) }}" method="POST" class="d-inline friend-request-form">
                                        @csrf
                                        <button type="submit" class="btn btn-success mt-2 friend-request-btn">
                                            <i class="fas fa-user-plus"></i> 加入好友
                                        </button>
                                    </form>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 处理好友申请表单提交
    const forms = document.querySelectorAll('.friend-request-form');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = form.querySelector('.friend-request-btn');
            const originalText = button.innerHTML;
            
            // 禁用按钮
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-check"></i> 申请已发送';
            button.classList.remove('btn-success');
            button.classList.add('btn-secondary');
            
            // 发送请求
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    // 显示成功提示
                    alert('好友申请已经提交！');
                } else {
                    // 恢复按钮
                    button.disabled = false;
                    button.innerHTML = originalText;
                    button.classList.remove('btn-secondary');
                    button.classList.add('btn-success');
                    alert(data.message || '发送失败');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                // 恢复按钮
                button.disabled = false;
                button.innerHTML = originalText;
                button.classList.remove('btn-secondary');
                button.classList.add('btn-success');
                alert('发送失败，请重试');
            });
        });
    });
});
</script>
@endpush