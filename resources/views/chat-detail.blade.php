@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            @if($user->avatar)
                                <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                </div>
                            @else
                                <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                                    <img src="{{ asset('images/default-avatar.svg') }}" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                </div>
                            @endif
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $user->name }}</h5>
                            <small class="text-light">
                                @if($user->gender == 'male')
                                    <i class="fas fa-male"></i> 男
                                @else
                                    <i class="fas fa-female"></i> 女
                                @endif
                                @if($user->age)
                                    | {{ $user->age }}岁
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card-body" id="chat-messages" style="height: 500px; overflow-y: auto; background-color: #f8f9fa;">
                    @if($messages->isEmpty())
                        <div class="text-center text-muted mt-5">
                            <i class="fas fa-comment-slash fa-3x mb-3"></i>
                            <p>还没有消息，发送第一条消息开始聊天吧！</p>
                        </div>
                    @else
                        @foreach($messages as $message)
                            @php
                                $isMe = $message->from_user_id == auth()->id();
                            @endphp
                            <div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                                @if(!$isMe)
                                    <div class="me-2">
                                        @if($user->avatar)
                                            <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                            </div>
                                        @else
                                            <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                                                <img src="{{ asset('images/default-avatar.svg') }}" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="{{ $isMe ? 'bg-primary text-white' : 'bg-white' }} rounded p-3 shadow-sm" style="max-width: 70%;">
                                    @if($message->type === 'image')
                                        @if($message->attachment_url)
                                            <img src="{{ $message->attachment_url }}" alt="图片" class="img-fluid rounded">
                                        @endif
                                    @elseif($message->type === 'voice')
                                        <i class="fas fa-microphone"></i> [语音]
                                    @elseif($message->type === 'video')
                                        <i class="fas fa-video"></i> [视频]
                                    @elseif($message->type === 'gift')
                                        <i class="fas fa-gift"></i> [礼物]
                                    @elseif($message->type === 'emoji')
                                        <i class="fas fa-smile"></i> [表情]
                                    @else
                                        {{ $message->message }}
                                    @endif
                                    <div class="text-end mt-2">
                                        <small class="{{ $isMe ? 'text-light' : 'text-muted' }}">
                                            {{ $message->created_at->format('H:i') }}
                                            @if($isMe && $message->is_read)
                                                <i class="fas fa-check-double text-light"></i>
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                @if($isMe)
                                    <div class="ms-2">
                                        @if(auth()->user()->avatar)
                                            <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                            </div>
                                        @else
                                            <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                                                <img src="{{ asset('images/default-avatar.svg') }}" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="card-footer">
                    <form id="message-form" action="{{ route('chat.send', $user->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="text">
                        <div class="input-group">
                            <input type="text" name="message" id="message-input" class="form-control" placeholder="输入消息..." autocomplete="off" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> 发送
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');

    // 滚动到底部
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // 发送消息
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const messageText = messageInput.value.trim();
        
        // 如果消息为空，不发送
        if (!messageText) return;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 清空输入框
                messageInput.value = '';
                
                // 动态添加新消息到聊天窗口
                const message = data.message;
                const messageHtml = createMessageElement(message, true);
                chatMessages.appendChild(messageHtml);
                
                // 滚动到底部
                chatMessages.scrollTop = chatMessages.scrollHeight;
            } else {
                alert(data.message || '发送失败');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('发送失败，请重试');
        });
    });
    
    // 创建消息元素
    function createMessageElement(message, isMe) {
        const div = document.createElement('div');
        div.className = 'd-flex justify-content-end mb-3';
        
        const now = new Date();
        const timeString = now.toLocaleTimeString('zh-TW', { hour: '2-digit', minute: '2-digit' });
        
        div.innerHTML = `
            <div class="bg-primary text-white rounded p-3 shadow-sm" style="max-width: 70%;">
                ${escapeHtml(message.message)}
                <div class="text-end mt-2">
                    <small class="text-light">
                        ${timeString}
                        <i class="fas fa-check-double text-light"></i>
                    </small>
                </div>
            </div>
            <div class="ms-2">
                <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
                </div>
            </div>
        `;
        
        return div;
    }
    
    // HTML 转义防止 XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endpush
@endsection
