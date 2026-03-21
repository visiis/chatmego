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
                            <button type="button" class="btn btn-outline-secondary" id="emoji-btn" style="font-size: 20px; padding: 6px 12px;">
                                😀
                            </button>
                            <input type="text" name="message" id="message-input" class="form-control" placeholder="输入消息..." autocomplete="off" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> 发送
                            </button>
                        </div>
                    </form>
                    <!-- 桌面端 Emoji Mart 容器 -->
                    <div id="emoji-picker-container" style="display: none; position: absolute; bottom: 60px; left: 20px; z-index: 1000; max-width: 350px; max-height: 450px; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"></div>
                    <!-- 移动端简化表情容器 -->
                    <div id="simple-emoji-picker" style="display: none;">
                        <div class="emoji-grid" id="emoji-grid"></div>
                    </div>
                </div>

<style>
/* 移动端简化表情面板样式 */
@media (max-width: 768px) {
    #simple-emoji-picker {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-radius: 16px 16px 0 0;
        padding: 12px;
        max-height: 50vh;
        overflow-y: auto;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
    }
    
    .emoji-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 8px;
        font-size: 24px;
    }
    
    .emoji-item {
        cursor: pointer;
        text-align: center;
        padding: 4px;
        border-radius: 4px;
        transition: background 0.2s;
        user-select: none;
    }
    
    .emoji-item:hover {
        background: #f0f0f0;
    }
    
    .emoji-item:active {
        background: #e0e0e0;
    }
    
    .emoji-category {
        grid-column: 1 / -1;
        font-size: 13px;
        color: #666;
        margin: 8px 0 4px;
        font-weight: 600;
    }
    
    /* 移动端优化桌面端容器 */
    #emoji-picker-container {
        left: 10px;
        right: 10px;
        max-width: calc(100% - 20px);
        max-height: 40vh;
        bottom: 70px;
    }
}
</style>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- 引入 Emoji Mart -->
<script src="https://cdn.jsdelivr.net/npm/@emoji-mart/react"></script>
<script src="https://cdn.jsdelivr.net/npm/emoji-mart@latest/dist/browser.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const emojiBtn = document.getElementById('emoji-btn');
    const emojiPickerContainer = document.getElementById('emoji-picker-container');
    
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
    
    // 检测是否为移动设备
    function isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) 
            || window.innerWidth <= 768;
    }
    
    // 常用表情列表（移动端使用）- 使用 iOS 兼容的基础表情
    const commonEmojis = [
        { category: '最近使用', emojis: ['😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇', '', '😍', '🤩'] },
        { category: '情感', emojis: ['😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '😎', ''] },
        { category: '爱意', emojis: ['🥰', '😍', '🤩', '😘', '😗', '😚', '😙', '😋', '😛', '😝', '😜', '🤪'] },
        { category: '开心', emojis: ['😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇', '', '😝'] },
        { category: '手势', emojis: ['👍', '👎', '👊', '🤜', '🤞', '️', '🤟', '🤘', '👌', '', '👉', '👆'] },
        { category: '动物', emojis: ['🐶', '🐱', '', '🐹', '🐰', '🦊', '', '🐼', '🐨', '🐯', '', '🐮'] },
        { category: '自然', emojis: ['🌞', '🌙', '⭐', '🌈', '️', '🔥', '💧', '🌸', '🌺', '🌻', '', '🌷'] },
        { category: '食物', emojis: ['🍎', '🍊', '🍋', '', '🍉', '🍇', '🍓', '', '🍑', '🍍', '🥝', ''] },
        { category: '庆祝', emojis: ['🎉', '🎊', '🎈', '🎁', '', '', '🍰', '', '🍭', '🍬', '', ''] }
    ];
    
    // 初始化移动端表情面板
    function initMobileEmojiPicker() {
        const grid = document.getElementById('emoji-grid');
        if (!grid) return;
        
        grid.innerHTML = '';
        
        commonEmojis.forEach(category => {
            // 添加分类标题
            const categoryDiv = document.createElement('div');
            categoryDiv.className = 'emoji-category';
            categoryDiv.textContent = category.category;
            grid.appendChild(categoryDiv);
            
            // 添加表情
            category.emojis.forEach(emoji => {
                const emojiDiv = document.createElement('div');
                emojiDiv.className = 'emoji-item';
                emojiDiv.textContent = emoji;
                emojiDiv.addEventListener('click', function() {
                    insertEmoji(emoji);
                });
                grid.appendChild(emojiDiv);
            });
        });
    }
    
    // 插入表情到输入框
    function insertEmoji(emoji) {
        const messageInput = document.getElementById('message-input');
        const startPos = messageInput.selectionStart;
        const endPos = messageInput.selectionEnd;
        const text = messageInput.value;
        messageInput.value = text.substring(0, startPos) + emoji + text.substring(endPos);
        messageInput.focus();
        messageInput.selectionStart = messageInput.selectionEnd = startPos + emoji.length;
        
        // 隐藏表情面板
        if (isMobile()) {
            document.getElementById('simple-emoji-picker').style.display = 'none';
        } else {
            document.getElementById('emoji-picker-container').style.display = 'none';
        }
    }
    
    // 初始化 Emoji Mart 表情选择器（桌面端）
    let emojiPicker = null;
    const initEmojiPicker = () => {
        if (!emojiPicker) {
            emojiPicker = new EmojiMart.Picker({
                theme: 'light',
                locale: 'zh',
                emojiSize: 24,
                maxFrequentRows: 2,
                onEmojiSelect: (emoji) => {
                    insertEmoji(emoji.native);
                }
            });
            
            // 将表情选择器添加到容器中
            emojiPickerContainer.innerHTML = '';
            emojiPickerContainer.appendChild(emojiPicker);
        }
    };
    
    // 点击表情按钮显示/隐藏表情选择器
    emojiBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (isMobile()) {
            // 移动端使用简化版
            initMobileEmojiPicker();
            const picker = document.getElementById('simple-emoji-picker');
            picker.style.display = picker.style.display === 'block' ? 'none' : 'block';
            
            // 隐藏桌面版
            emojiPickerContainer.style.display = 'none';
        } else {
            // 桌面端使用 Emoji Mart
            initEmojiPicker();
            emojiPickerContainer.style.display = emojiPickerContainer.style.display === 'block' ? 'none' : 'block';
            
            // 隐藏移动版
            document.getElementById('simple-emoji-picker').style.display = 'none';
        }
    });
    
    // 点击其他地方关闭表情选择器
    document.addEventListener('click', function(e) {
        if (emojiPickerContainer.style.display === 'block' && 
            !emojiPickerContainer.contains(e.target) && 
            e.target !== emojiBtn) {
            emojiPickerContainer.style.display = 'none';
        }
        
        if (document.getElementById('simple-emoji-picker').style.display === 'block' &&
            !document.getElementById('simple-emoji-picker').contains(e.target) &&
            e.target !== emojiBtn) {
            document.getElementById('simple-emoji-picker').style.display = 'none';
        }
    });

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
