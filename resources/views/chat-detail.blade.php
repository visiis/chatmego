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
                            <button type="button" class="btn btn-outline-danger" id="gift-btn" style="font-size: 20px; padding: 6px 12px;">
                                🎁
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
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .emoji-item:empty {
        visibility: hidden;
        pointer-events: none;
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

<!-- 礼物选择模态框 -->
<div class="modal fade" id="giftModal" tabindex="-1" role="dialog" aria-labelledby="giftModalLabel">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="giftModalLabel">🎁 选择礼物</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- 礼物分类标签页 -->
                <ul class="nav nav-tabs mb-3" id="giftTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="virtual-tab" data-bs-toggle="tab" data-bs-target="#virtual" type="button" role="tab">
                            💎 虚拟礼物
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="physical-tab" data-bs-toggle="tab" data-bs-target="#physical" type="button" role="tab">
                            🎁 实体礼物
                        </button>
                    </li>
                </ul>
                
                <!-- 礼物内容区域 -->
                <div class="tab-content" id="giftTabsContent">
                    <div class="tab-pane fade show active" id="virtual" role="tabpanel">
                        <div id="virtual-gift-list" class="row g-3">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">加载中...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="physical" role="tabpanel">
                        <div id="physical-gift-list" class="row g-3">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">加载中...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- 引入 Emoji Mart -->
<script src="https://cdn.jsdelivr.net/npm/emoji-mart@latest/dist/browser.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const emojiBtn = document.getElementById('emoji-btn');
    const emojiPickerContainer = document.getElementById('emoji-picker-container');
    const giftBtn = document.getElementById('gift-btn');
    const giftModal = document.getElementById('giftModal');
    
    // 礼物列表
    let userGifts = [];
    
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
    
    // 常用表情列表（移动端使用）- 纯 Unicode 方案，直接显示字符
    const commonEmojis = [
        { category: '最近使用', emojis: ['😀', '😃', '😄', '😁', '😆', '😅', '😂', '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩'] },
        { category: '情感', emojis: ['😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '😎', '🤓'] },
        { category: '爱意', emojis: ['🥰', '😍', '🤩', '😘', '😗', '😚', '😙', '😋', '😛', '😝', '😜', '🤪'] },
        { category: '开心', emojis: ['😀', '😃', '😄', '😁', '😆', '😅', '😂', '🙂', '🙃', '😉', '😊', '😇', '🤗', '🤭', '😛'] },
        { category: '手势', emojis: ['👍', '👎', '👊', '✊', '🤛', '✌️', '🤟', '🤘', '👌', '🤏', '👉', '👆'] },
        { category: '动物', emojis: ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮'] },
        { category: '自然', emojis: ['🌞', '🌙', '⭐', '🌈', '☀️', '🔥', '💧', '🌸', '🌺', '🌻', '🌹', '🌷'] },
        { category: '食物', emojis: ['🍎', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🫐', '🍑', '🍍', '🥝', '🍒'] },
        { category: '庆祝', emojis: ['🎉', '🎊', '🎈', '🎁', '🎂', '🎄', '🎃', '🧁', '🍭', '🍬', '🎃', '🎆'] }
    ];
    
    // 初始化移动端表情面板 - 直接显示 Unicode 字符
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
            
            // 添加表情 - 直接显示 Unicode 字符
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
            
            let messageContent = '';
            if (message.type === 'gift') {
                const giftData = JSON.parse(message.message);
                messageContent = `
                    <div class="text-center">
                        ${giftData.gift_image ? `<img src="/storage/${giftData.gift_image}" alt="${giftData.gift_name}" class="img-fluid rounded mb-2" style="max-height: 150px;">` : ''}
                        <div class="bg-white bg-opacity-25 rounded p-2">
                            <i class="fas fa-gift"></i> ${giftData.gift_name}
                        </div>
                    </div>
                `;
            } else if (message.type === 'image') {
                messageContent = message.attachment_url 
                    ? `<img src="${message.attachment_url}" alt="图片" class="img-fluid rounded">`
                    : escapeHtml(message.message);
            } else if (message.type === 'emoji') {
                messageContent = `<span style="font-size: 48px;">${escapeHtml(message.message)}</span>`;
            } else {
                messageContent = escapeHtml(message.message);
            }
            
            div.innerHTML = `
                <div class="bg-primary text-white rounded p-3 shadow-sm" style="max-width: 70%;">
                    ${messageContent}
                    <div class="text-end mt-2">
                        <small class="text-light">${timeString}
                            ${message.is_read ? '<i class="fas fa-check-double ms-1"></i>' : '<i class="fas fa-check ms-1"></i>'}
                        </small>
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
            
            let messageContent = '';
            if (message.type === 'gift') {
                const giftData = JSON.parse(message.message);
                messageContent = `
                    <div class="text-center">
                        ${giftData.gift_image ? `<img src="/storage/${giftData.gift_image}" alt="${giftData.gift_name}" class="img-fluid rounded mb-2" style="max-height: 150px;">` : ''}
                        <div class="bg-light rounded p-2">
                            <i class="fas fa-gift text-danger"></i> ${giftData.gift_name}
                        </div>
                    </div>
                `;
            } else if (message.type === 'image') {
                messageContent = message.attachment_url 
                    ? `<img src="${message.attachment_url}" alt="图片" class="img-fluid rounded">`
                    : escapeHtml(message.message);
            } else if (message.type === 'emoji') {
                messageContent = `<span style="font-size: 48px;">${escapeHtml(message.message)}</span>`;
            } else {
                messageContent = escapeHtml(message.message);
            }
            
            div.innerHTML = `
                <div class="me-2">
                    <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                        <img src="${userAvatar}" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover" onerror="this.src='${defaultAvatar}'">
                    </div>
                </div>
                <div class="bg-white rounded p-3 shadow-sm" style="max-width: 70%;">
                    ${messageContent}
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
    
    // 礼物相关功能
    // 打开礼物模态框
    if (giftBtn) {
        giftBtn.addEventListener('click', function() {
            loadUserGifts();
        });
    }
    
    // 加载用户礼物
    function loadUserGifts() {
        fetch('{{ route("user.gifts.api") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    userGifts = data.gifts;
                    renderGiftLists(userGifts);
                    new bootstrap.Modal(giftModal).show();
                } else {
                    alert('加载礼物失败');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('加载失败');
            });
    }
    
    // 渲染礼物列表（分虚拟和实体）
    function renderGiftLists(gifts) {
        const storageBaseUrl = '{{ asset('storage') }}';
        
        // 分离虚拟礼物和实体礼物
        const virtualGifts = gifts.filter(g => g.type === 'virtual');
        const physicalGifts = gifts.filter(g => g.type === 'physical');
        
        // 渲染虚拟礼物
        renderGiftCategory('virtual-gift-list', virtualGifts, storageBaseUrl);
        
        // 渲染实体礼物
        renderGiftCategory('physical-gift-list', physicalGifts, storageBaseUrl);
    }
    
    // 渲染单个礼物分类
    function renderGiftCategory(elementId, gifts, storageBaseUrl) {
        const giftList = document.getElementById(elementId);
        
        if (!gifts || gifts.length === 0) {
            giftList.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                    <p class="text-muted">暂无此类礼物</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        gifts.forEach(gift => {
            const giftImage = gift.image 
                ? `<img src="${storageBaseUrl}/${gift.image}" alt="${gift.name}" class="img-fluid rounded mb-2" style="height: 80px; object-fit: cover;">`
                : `<div class="bg-light rounded mb-2" style="height: 80px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-gift fa-2x text-muted"></i></div>`;
            
            const priceText = gift.price_type === 'activity_points' 
                ? `💎 ${gift.price} 活跃度` 
                : `💰 ${gift.price} 金币`;
            
            html += `
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 position-relative" style="cursor: pointer;" onclick="sendGift(${gift.id})">
                        <div class="card-body text-center p-2">
                            ${giftImage}
                            <h6 class="card-title small mb-1">${gift.name}</h6>
                            <p class="card-text small text-primary mb-0">${priceText}</p>
                        </div>
                    </div>
                </div>
            `;
        });
        
        giftList.innerHTML = html;
    }
    
    // 发送礼物（购买并发送）- 暴露到全局作用域
    window.sendGift = function(giftId) {
        if (!confirm('确定要购买并发送这个礼物吗？')) return;
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('gift_id', giftId);
        
        fetch('{{ route("chat.send.gift", $user->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 关闭模态框
                const modal = bootstrap.Modal.getInstance(giftModal);
                modal.hide();
                
                // 添加礼物消息到聊天
                addMessage(data.message, true);
                lastMessageId = Math.max(lastMessageId, data.message.id);
                
                // 重新加载礼物列表（更新余额）
                loadUserGifts();
            } else {
                alert(data.message || '发送失败');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('发送失败，请重试');
        });
    }
});
</script>
@endpush
@endsection
