<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <h2 class="text-center mb-4"><?php echo e(__('messages.users.title')); ?></h2>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->isEmpty()): ?>
        <div class="alert alert-info text-center">
            <?php echo e(__('messages.users.no_users')); ?>

        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card text-center shadow-sm h-100">
                        <div class="card-body">
                            <!-- 头像 -->
                            <div class="mb-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->avatar): ?>
                                    <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" class="rounded-circle mx-auto d-block" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e9ecef;" alt="<?php echo e($user->name); ?>">
                                <?php else: ?>
                                    <img src="<?php echo e(asset('images/default-avatar.svg')); ?>" class="rounded-circle mx-auto d-block" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #e9ecef;" alt="<?php echo e($user->name); ?>">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            
                            <h5 class="card-title"><?php echo e($user->name); ?></h5>
                            <p class="text-muted small mb-1">
                                <?php echo e($user->gender == 'male' ? __('messages.profile.male') : __('messages.profile.female')); ?> · <?php echo e($user->age); ?> <?php echo e(__('messages.profile.age')); ?>

                            </p>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-ruler-vertical"></i> <?php echo e($user->height); ?> cm · 
                                <i class="fas fa-weight"></i> <?php echo e($user->weight); ?> kg
                            </p>
                            
                            <!-- 操作按钮 -->
                            <div class="mt-4 text-center">
                                <a href="<?php echo e(route('profile', $user->id)); ?>" class="btn btn-primary">
                                    <?php echo e(__('messages.users.view_profile')); ?>

                                </a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->id() != $user->id): ?>
                                    <a href="<?php echo e(route('chat.show', $user->id)); ?>" class="btn btn-success">
                                        <i class="fas fa-comments"></i> 立即聊天
                                    </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($user->friendship_status) && $user->friendship_status === 'accepted'): ?>
                                        <button type="button" class="btn btn-secondary" disabled>
                                            <i class="fas fa-check"></i> 已是好友
                                        </button>
                                    <?php elseif(isset($user->friendship_status) && $user->friendship_status === 'pending' && $user->requester_id === auth()->id()): ?>
                                        <button type="button" class="btn btn-secondary" disabled>
                                            <i class="fas fa-clock"></i> 已发送
                                        </button>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('friends.request', $user->id)); ?>" method="POST" class="d-inline friend-request-form">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-success friend-request-btn">
                                                <i class="fas fa-user-plus"></i> 加入好友
                                            </button>
                                        </form>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/MyWork/APP/ChatMeGo/resources/views/users.blade.php ENDPATH**/ ?>