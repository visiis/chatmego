<?php $__env->startSection('content'); ?>
<div class="container">
    <h1 class="text-center mb-4"><?php echo e(__('messages.users.title')); ?></h1>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->isEmpty()): ?>
        <div class="alert alert-info text-center">
            <?php echo e(__('messages.users.no_users')); ?>

        </div>
    <?php else: ?>
        <div class="row">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->avatar): ?>
                                    <div class="ratio ratio-1x1 d-inline-block" style="width: 180px; height: 180px;">
                                        <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                    </div>
                                <?php else: ?>
                                    <div class="ratio ratio-1x1 d-inline-block" style="width: 180px; height: 180px;">
                                        <img src="<?php echo e(asset('images/default-avatar.svg')); ?>" alt="Default Avatar" class="rounded-circle img-thumbnail w-100 h-100 object-fit-cover">
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <h3 class="mt-3"><?php echo e($user->name); ?></h3>
                            </div>
                            
                            <div class="mb-2">
                                <strong><?php echo e(__('messages.profile.gender')); ?>:</strong> <?php echo e($user->gender == 'male' ? __('messages.profile.male') : __('messages.profile.female')); ?>

                            </div>
                            <div class="mb-2">
                                <strong><?php echo e(__('messages.profile.age')); ?>:</strong> <?php echo e($user->age); ?>

                            </div>
                            <div class="mb-2">
                                <strong><?php echo e(__('messages.profile.height')); ?>:</strong> <?php echo e($user->height); ?> cm
                            </div>
                            <div class="mb-2">
                                <strong><?php echo e(__('messages.profile.weight')); ?>:</strong> <?php echo e($user->weight); ?> kg
                            </div>
                            
                            <div class="mt-4 text-center">
                                <a href="<?php echo e(route('profile', $user->id)); ?>" class="btn btn-primary">
                                    <?php echo e(__('messages.users.view_profile')); ?>

                                </a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->id() != $user->id): ?>
                                    <form action="<?php echo e(route('friends.request', $user->id)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-success mt-2">
                                            <i class="fas fa-user-plus"></i> 加入好友
                                        </button>
                                    </form>
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/MyWork/APP/ChatMeGo/resources/views/users.blade.php ENDPATH**/ ?>