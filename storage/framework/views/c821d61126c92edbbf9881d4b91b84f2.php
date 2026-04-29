<?php $__env->startSection('content'); ?>
<div class="container">
    <h1 class="text-center mb-4">
        <i class="fas fa-comments"></i> 我的聊天
    </h1>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($chatList) || count($chatList) === 0): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-comment-slash fa-3x mb-3"></i>
            <h4>还没有聊天</h4>
            <p>添加好友后就可以开始聊天了！</p>
            <a href="<?php echo e(route('friends')); ?>" class="btn btn-primary mt-2">
                <i class="fas fa-users"></i> 我的好友
            </a>
        </div>
    <?php else: ?>
        <div class="list-group">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $chatList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $friend = $chat['friend'];
                    $lastMessage = $chat['last_message'];
                    $unreadCount = $chat['unread_count'];
                ?>
                <a href="<?php echo e(route('chat.show', $friend->id)); ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="position-relative me-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($friend->avatar): ?>
                                    <div class="ratio ratio-1x1 d-inline-block avatar-container" style="width: 60px; height: 60px;">
                                        <img src="<?php echo e(avatar_url($friend->avatar)); ?>" loading="lazy" class="lazy-image rounded-circle img-thumbnail w-100 h-100 object-fit-cover" alt="Avatar">
                                    </div>
                                <?php else: ?>
                                    <div class="ratio ratio-1x1 d-inline-block" style="width: 60px; height: 60px;">
                                        <img src="<?php echo e(asset('images/default-avatar.svg')); ?>" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo e($unreadCount); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div>
                                <h5 class="mb-1"><?php echo e($friend->name); ?></h5>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastMessage): ?>
                                    <p class="mb-1 text-muted">
                                        <small>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastMessage->type === 'image'): ?>
                                                <i class="fas fa-image"></i> [图片]
                                            <?php elseif($lastMessage->type === 'voice'): ?>
                                                <i class="fas fa-microphone"></i> [语音]
                                            <?php elseif($lastMessage->type === 'video'): ?>
                                                <i class="fas fa-video"></i> [视频]
                                            <?php elseif($lastMessage->type === 'gift'): ?>
                                                <i class="fas fa-gift"></i> [礼物]
                                            <?php elseif($lastMessage->type === 'emoji'): ?>
                                                <i class="fas fa-smile"></i> [表情]
                                            <?php else: ?>
                                                <?php echo e(Str::limit($lastMessage->message, 30)); ?>

                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </small>
                                    </p>
                                <?php else: ?>
                                    <p class="mb-1 text-muted">
                                        <small>还没有消息</small>
                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                        <div class="text-end">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastMessage): ?>
                                <small class="text-muted">
                                    <?php echo e($lastMessage->created_at->diffForHumans()); ?>

                                </small>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-2"><?php echo e($unreadCount); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="text-center mt-4">
        <a href="<?php echo e(route('friends')); ?>" class="btn btn-primary">
            <i class="fas fa-users"></i> 我的好友
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/MyWork/APP/ChatMeGo/resources/views/chats.blade.php ENDPATH**/ ?>