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
    
    // 存储最后一条消息的 ID 和时间
    let lastMessageId = <?php echo e($messages->isNotEmpty() ? $messages->last()->id : 0); ?>;
    let lastMessageTime = '<?php echo e($messages->isNotEmpty() ? $messages->last()->created_at->timestamp : 0); ?>';

    // 滚动到底部
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // 定时轮询获取新消息（每 2 秒）
    function fetchNewMessages() {
        fetch('<?php echo e(route("chat.fetch", $user->id)); ?>?last_message_id=' + lastMessageId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.messages.length > 0) {
                    // 添加新消息
                    data.messages.forEach(message => {
                        const messageHtml = createMessageElement(message, message.from_user_id === <?php echo e(auth()->id()); ?>);
                        chatMessages.appendChild(messageHtml);
                        lastMessageId = message.id;
                        lastMessageTime = new Date(message.created_at).getTime() / 1000;
                    });
                    
                    // 滚动到底部
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    
                    // 标记消息为已读
                    markMessagesAsRead();
                }
            })
            .catch(error => console.error('Error fetching messages:', error));
    }
    
    // 启动轮询（每 2 秒检查一次新消息）
    const pollInterval = setInterval(fetchNewMessages, 2000);
    
    // 标记消息为已读
    function markMessagesAsRead() {
        fetch('<?php echo e(route("chat.read", $user->id)); ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        }).catch(error => console.error('Error marking messages as read:', error));
    }
    
    // 页面可见时立即检查一次
    setTimeout(fetchNewMessages, 1000);
    
    // 页面隐藏时停止轮询，可见时恢复
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(pollInterval);
        } else {
            fetchNewMessages();
            // 重新启动轮询
            setInterval(fetchNewMessages, 2000);
        }
    });

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
                
                // 更新最后消息 ID
                lastMessageId = message.id;
                
                // 滚动到底部
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                // 立即标记为已读
                markMessagesAsRead();
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
                    <img src="<?php echo e(asset('storage/' . auth()->user()->avatar)); ?>" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover" onerror="this.src='<?php echo e(asset('images/default-avatar.svg')); ?>'">
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/MyWork/APP/ChatMeGo/resources/views/chat-detail.blade.php ENDPATH**/ ?>