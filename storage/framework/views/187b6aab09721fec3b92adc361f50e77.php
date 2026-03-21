<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->avatar): ?>
                                <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                                    <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                </div>
                            <?php else: ?>
                                <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                                    <img src="<?php echo e(asset('images/default-avatar.svg')); ?>" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div>
                            <h5 class="mb-0"><?php echo e($user->name); ?></h5>
                            <small class="text-light">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->gender == 'male'): ?>
                                    <i class="fas fa-male"></i> 男
                                <?php else: ?>
                                    <i class="fas fa-female"></i> 女
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->age): ?>
                                    | <?php echo e($user->age); ?>岁
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card-body" id="chat-messages" style="height: 500px; overflow-y: auto; background-color: #f8f9fa;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($messages->isEmpty()): ?>
                        <div class="text-center text-muted mt-5">
                            <i class="fas fa-comment-slash fa-3x mb-3"></i>
                            <p>还没有消息，发送第一条消息开始聊天吧！</p>
                        </div>
                    <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $isMe = $message->from_user_id == auth()->id();
                            ?>
                            <div class="d-flex <?php echo e($isMe ? 'justify-content-end' : 'justify-content-start'); ?> mb-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isMe): ?>
                                    <div class="me-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->avatar): ?>
                                            <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                                                <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                            </div>
                                        <?php else: ?>
                                            <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                                                <img src="<?php echo e(asset('images/default-avatar.svg')); ?>" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <div class="<?php echo e($isMe ? 'bg-primary text-white' : 'bg-white'); ?> rounded p-3 shadow-sm" style="max-width: 70%;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message->type === 'image'): ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($message->attachment_url): ?>
                                            <img src="<?php echo e($message->attachment_url); ?>" alt="图片" class="img-fluid rounded">
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php elseif($message->type === 'voice'): ?>
                                        <i class="fas fa-microphone"></i> [语音]
                                    <?php elseif($message->type === 'video'): ?>
                                        <i class="fas fa-video"></i> [视频]
                                    <?php elseif($message->type === 'gift'): ?>
                                        <i class="fas fa-gift"></i> [礼物]
                                    <?php elseif($message->type === 'emoji'): ?>
                                        <i class="fas fa-smile"></i> [表情]
                                    <?php else: ?>
                                        <?php echo e($message->message); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="text-end mt-2">
                                        <small class="<?php echo e($isMe ? 'text-light' : 'text-muted'); ?>">
                                            <?php echo e($message->created_at->format('H:i')); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isMe && $message->is_read): ?>
                                                <i class="fas fa-check-double text-light"></i>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </small>
                                    </div>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isMe): ?>
                                    <div class="ms-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->avatar): ?>
                                            <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                                                <img src="<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                            </div>
                                        <?php else: ?>
                                            <div class="ratio ratio-1x1 d-inline-block" style="width: 40px; height: 40px;">
                                                <img src="<?php echo e(asset('images/default-avatar.svg')); ?>" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="card-footer">
                    <form id="message-form" action="<?php echo e(route('chat.send', $user->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
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

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    
    // 存储最后一条消息的 ID（确保是正数）
    let lastMessageId = <?php echo e($messages->isNotEmpty() ? $messages->last()->id : 0); ?>;
    if (lastMessageId < 0) lastMessageId = 0;
    
    // 存储最早的消息 ID，用于加载历史
    let oldestMessageId = <?php echo e($messages->isNotEmpty() ? $messages->first()->id : 0); ?>;
    let isLoadingHistory = false;
    let hasMoreHistory = true;
    
    let isSendingMessage = false;
    
    // 头像 URL
    const myAvatar = "<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>";
    const userAvatar = "<?php echo e(asset('storage/' . $user->avatar)); ?>";
    const defaultAvatar = "<?php echo e(asset('images/default-avatar.svg')); ?>";

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
        
        fetch('<?php echo e(route("chat.history", $user->id)); ?>?before_id=' + oldestMessageId + '&limit=50')
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success && data.messages && data.messages.length > 0) {
                    // 在顶部添加历史消息
                    for (var i = 0; i < data.messages.length; i++) {
                        var message = data.messages[i];
                        
                        // 跳过自己发送的消息
                        if (message.from_user_id == <?php echo e(auth()->id()); ?>) {
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
        
        fetch('<?php echo e(route("chat.fetch", $user->id)); ?>?last_message_id=' + lastMessageId)
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success && data.messages && data.messages.length > 0) {
                    // 有新消息，逐个添加
                    for (var i = 0; i < data.messages.length; i++) {
                        var message = data.messages[i];
                        
                        // 跳过自己发送的消息
                        if (message.from_user_id == <?php echo e(auth()->id()); ?>) {
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
        
        if (isMe || message.from_user_id == <?php echo e(auth()->id()); ?>) {
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/MyWork/APP/ChatMeGo/resources/views/chat-detail.blade.php ENDPATH**/ ?>