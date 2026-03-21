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
    
    // 存储最后一条消息的 ID（确保是正数）
    let lastMessageId = {{ $messages->isNotEmpty() ? $messages->last()->id : 0 }};
    if (lastMessageId < 0) lastMessageId = 0;
    
    // 存储最早的消息 ID，用于加载历史
    let oldestMessageId = {{ $messages->isNotEmpty() ? $messages->first()->id : 0 }};
    let isLoadingHistory = false;
    let hasMoreHistory = true;
    
    let isSendingMessage = false;
    
    // 头像 URL
    const myAvatar = "{{ asset('storage/' . auth()->user()->avatar) }}";
    const userAvatar = "{{ asset('storage/' . $user->avatar) }}";
    const defaultAvatar = "{{ asset('images/default-avatar.svg') }}";

    // 滚动到底部
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    // 监听滚动事件，向上滚动时加载历史消息
    chatMessages.addEventListener('scroll', function() {
        // 如果滚动到顶部附近（50px 以内）
        if (chatMessages.scrollTop < 50 && !isLoadingHistory && hasMoreHistory) {
            loadHistory();
        }
    });
    
    // 加载历史消息
    function loadHistory() {
        if (isLoadingHistory || !hasMoreHistory) return;
        
        isLoadingHistory = true;
        
        // 记录当前滚动高度
        const oldScrollHeight = chatMessages.scrollHeight;
        
        fetch('{{ route("chat.history", $user->id) }}?before_id=' + oldestMessageId + '&limit=50')
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success && data.messages && data.messages.length > 0) {
                    // 在顶部添加历史消息
                    for (var i = 0; i < data.messages.length; i++) {
                        var message = data.messages[i];
                        
                        // 跳过自己发送的消息
                        if (message.from_user_id == {{ auth()->id() }}) {
                            oldestMessageId = Math.min(oldestMessageId, message.id);
                            continue;
                        }
                        
                        // 添加对方的消息到顶部
                        var messageHtml = createMessageElement(message, false);
                        chatMessages.insertBefore(messageHtml, chatMessages.firstChild);
                        oldestMessageId = Math.min(oldestMessageId, message.id);
                    }
                    
                    // 保持滚动位置
                    const newScrollHeight = chatMessages.scrollHeight;
                    chatMessages.scrollTop = newScrollHeight - oldScrollHeight;
                    
                    hasMoreHistory = data.has_more;
                } else {
                    hasMoreHistory = false;
                }
                
                isLoadingHistory = false;
            })
            .catch(function(error) {
                console.error('Error loading history:', error);
                isLoadingHistory = false;
            });
    }

    // 定时检查新消息（每 1 秒）
    setInterval(function() {
        if (isSendingMessage) return;
        
        // 如果 lastMessageId 是 0 或负数，设置为 0
        if (lastMessageId <= 0) {
            lastMessageId = 0;
        }
        
        fetch('{{ route("chat.fetch", $user->id) }}?last_message_id=' + lastMessageId)
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success && data.messages && data.messages.length > 0) {
                    // 有新消息，逐个添加
                    for (var i = 0; i < data.messages.length; i++) {
                        var message = data.messages[i];
                        
                        // 跳过自己发送的消息
                        if (message.from_user_id == {{ auth()->id() }}) {
                            lastMessageId = Math.max(lastMessageId, message.id);
                            continue;
                        }
                        
                        // 添加对方的消息
                        addMessage(message);
                        lastMessageId = Math.max(lastMessageId, message.id);
                    }
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
            });
    }, 1000);

    // 发送消息
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const messageText = messageInput.value.trim();
        
        if (!messageText) return;
        
        isSendingMessage = true;
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                messageInput.value = '';
                // 添加自己发送的消息
                addMessage(data.message, true);
                lastMessageId = Math.max(lastMessageId, data.message.id);
                isSendingMessage = false;
            } else {
                alert(data.message || '发送失败');
                isSendingMessage = false;
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            alert('发送失败，请重试');
            isSendingMessage = false;
        });
    });
    
    // 添加消息到聊天窗口
    function addMessage(message, isMe = false) {
        const div = createMessageElement(message, isMe);
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // 创建消息元素
    function createMessageElement(message, isMe = false) {
        const div = document.createElement('div');
        const now = new Date();
        const timeString = now.toLocaleTimeString('zh-TW', { hour: '2-digit', minute: '2-digit' });
        
        if (isMe || message.from_user_id == {{ auth()->id() }}) {
            // 我的消息 - 显示在右侧
            div.className = 'd-flex justify-content-end mb-3';
            div.innerHTML = `
                <div class="bg-primary text-white rounded p-3 shadow-sm" style="max-width: 70%;">
                    ${escapeHtml(message.message)}
                    <div class="text-end mt-2">
                        <small class="text-light">${timeString}</small>
                    </div>
                </div>
                <div class="ms-2">
                    <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                        <img src="${myAvatar}" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover" onerror="this.src='${defaultAvatar}'">
                    </div>
                </div>
            `;
        } else {
            // 对方的消息 - 显示在左侧
            div.className = 'd-flex justify-content-start mb-3';
            div.innerHTML = `
                <div class="me-2">
                    <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                        <img src="${userAvatar}" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover" onerror="this.src='${defaultAvatar}'">
                    </div>
                </div>
                <div class="bg-light rounded p-3 shadow-sm" style="max-width: 70%;">
                    ${escapeHtml(message.message)}
                    <div class="text-end mt-2">
                        <small class="text-muted">${timeString}</small>
                    </div>
                </div>
            `;
        }
        
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
