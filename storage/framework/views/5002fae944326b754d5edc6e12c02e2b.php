<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <h2 class="text-center mb-4"><?php echo e(__('messages.membership.title')); ?></h2>
    
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
    
    <div class="row">
        <!-- 左侧：当前会员信息 -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h4 class="card-title mb-3"><?php echo e(__('messages.membership.current_membership')); ?></h4>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($membershipInfo['has_membership']): ?>
                        <div class="mb-3">
                            <span class="badge" style="background-color: <?php echo e($membershipInfo['plan']->badge_color); ?>; font-size: 1.2rem; padding: 10px 20px;">
                                <?php echo e($membershipInfo['plan']->icon); ?> <?php echo e(__('messages.membership.plans.' . $membershipInfo['plan']->code)); ?>

                            </span>
                        </div>
                        <p class="text-muted mb-2">
                            <?php echo e(__('messages.membership.expires_at')); ?>：<?php echo e($membershipInfo['ends_at']->format('Y-m-d H:i')); ?>

                        </p>
                        <p class="text-primary">
                            <?php echo e(__('messages.membership.days_remaining', ['days' => $membershipInfo['days_remaining']])); ?>

                        </p>
                        <hr>
                        <form id="cancel-form" action="<?php echo e(route('membership.cancel')); ?>" method="POST" class="d-none">
                            <?php echo csrf_field(); ?>
                        </form>
                        <button class="btn btn-outline-danger btn-sm" onclick="confirmCancel()">
                            <?php echo e(__('messages.membership.cancel_auto_renewal')); ?>

                        </button>
                    <?php else: ?>
                        <div class="mb-3">
                            <span class="badge bg-secondary" style="font-size: 1.2rem; padding: 10px 20px;">
                                <?php echo e(__('messages.membership.basic_member')); ?>

                            </span>
                        </div>
                        <p class="text-muted">
                            <?php echo e(__('messages.membership.no_membership')); ?>

                        </p>
                        <a href="#plans" class="btn btn-primary">
                            <?php echo e(__('messages.membership.available_plans')); ?>

                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <small class="text-muted"><?php echo e(__('messages.nav.points')); ?></small>
                            <h4 class="mb-0 text-warning">💰 <?php echo e($user->coins); ?></h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted"><?php echo e(__('messages.nav.activity_points')); ?></small>
                            <h4 class="mb-0 text-info">💎 <?php echo e($user->points); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 活跃度兑换 -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">💱 <?php echo e(__('messages.membership.convert_points_to_coins')); ?></h5>
                    <p class="small text-muted mb-3">
                        100 <?php echo e(__('messages.nav.activity_points')); ?> = 1 💰
                    </p>
                    <form action="<?php echo e(route('membership.convertPoints')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('messages.membership.convert_amount')); ?></label>
                            <input type="number" name="points" class="form-control" 
                                   min="100" step="100" 
                                   max="<?php echo e($user->points); ?>" 
                                   placeholder="<?php echo e(__('messages.membership.enter_activity_points')); ?>" required>
                            <small class="text-muted">
                                <?php echo e(__('messages.nav.activity_points')); ?>: <?php echo e($user->points); ?>

                            </small>
                        </div>
                        <div class="mb-3">
                            <small class="text-success">
                                <i class="fas fa-gift"></i> <?php echo e(__('messages.membership.will_get_coins')); ?>: <span id="coins-preview">0</span> 💰
                            </small>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <?php echo e(__('messages.membership.convert_now')); ?>

                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- 右侧：会员计划 -->
        <div class="col-lg-8">
            <h4 class="mb-3"><?php echo e(__('messages.membership.available_plans')); ?></h4>
            <div class="row" id="plans">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $availablePlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100" style="border-top: 4px solid <?php echo e($plan['badge_color']); ?>;">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <span class="badge" style="background-color: <?php echo e($plan['badge_color']); ?>; font-size: 1.5rem; padding: 12px 24px;">
                                        <?php echo e($plan['icon']); ?> <?php echo e(__('messages.membership.plans.' . $plan['code'])); ?>

                                    </span>
                                </div>
                                
                                <div class="text-center mb-3">
                                    <h2 class="text-primary mb-0">
                                        💰 <?php echo e($plan['price']); ?> <?php echo e(__('messages.nav.points')); ?>

                                    </h2>
                                    <small class="text-muted">
                                        ≈ ¥<?php echo e($plan['price_yuan']); ?>

                                    </small>
                                </div>
                                
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                                        <?php echo e(__('messages.membership.days')); ?>: <?php echo e($plan['duration_days']); ?>

                                    </li>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plan['privileges']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $privilege): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <?php echo e($privilege); ?>

                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </ul>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($membershipInfo['has_membership'] && $membershipInfo['plan']->code === $plan['code']): ?>
                                    <div class="alert alert-info mb-3">
                                        <small><i class="fas fa-info-circle"></i> <?php echo e(__('messages.membership.currently_using')); ?></small>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <form action="<?php echo e(route('membership.purchase')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="plan_id" value="<?php echo e($plan['id']); ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label small"><?php echo e(__('messages.membership.purchase_duration')); ?></label>
                                        <select name="months" class="form-select form-select-sm" onchange="updatePrice(this, <?php echo e($plan['price']); ?>, <?php echo e($plan['duration_days']); ?>)">
                                            <option value="1">1 <?php echo e(__('messages.membership.month')); ?> (<?php echo e($plan['duration_days']); ?> <?php echo e(__('messages.membership.days')); ?>)</option>
                                            <option value="3">3 <?php echo e(__('messages.membership.months')); ?> (<?php echo e($plan['duration_days'] * 3); ?> <?php echo e(__('messages.membership.days')); ?>)</option>
                                            <option value="6">6 <?php echo e(__('messages.membership.months')); ?> (<?php echo e($plan['duration_days'] * 6); ?> <?php echo e(__('messages.membership.days')); ?>)</option>
                                            <option value="12">12 <?php echo e(__('messages.membership.months')); ?> (<?php echo e($plan['duration_days'] * 12); ?> <?php echo e(__('messages.membership.days')); ?>)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small"><?php echo e(__('messages.membership.required_coins')); ?>:</span>
                                            <span class="text-primary fw-bold" id="price-<?php echo e($plan['id']); ?>"><?php echo e($plan['price']); ?> 💰</span>
                                        </div>
                                        <input type="hidden" name="total_price" id="hidden-price-<?php echo e($plan['id']); ?>" value="<?php echo e($plan['price']); ?>">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100" 
                                            <?php echo e($user->coins < $plan['price'] ? 'disabled' : ''); ?>

                                            id="btn-<?php echo e($plan['id']); ?>">
                                        <?php echo e($user->coins < $plan['price'] ? '💰 ' . __('messages.membership.insufficient_coins') : '🛒 ' . __('messages.membership.buy_now')); ?>

                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- 订阅历史队列 -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">📋 <?php echo e(__('messages.membership.subscription_queue')); ?></h5>
                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscriptionHistory->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('messages.membership.plan')); ?></th>
                                    <th><?php echo e(__('messages.membership.start_time')); ?></th>
                                    <th><?php echo e(__('messages.membership.end_time')); ?></th>
                                    <th><?php echo e(__('messages.membership.status')); ?></th>
                                    <th><?php echo e(__('messages.membership.price')); ?></th>
                                    <th><?php echo e(__('messages.membership.notes')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $subscriptionHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span class="badge" style="background-color: <?php echo e($subscription->plan->badge_color); ?>;">
                                            <?php echo e($subscription->plan->icon); ?> <?php echo e(__('messages.membership.plans.' . $subscription->plan->code)); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($subscription->starts_at->format('Y-m-d H:i')); ?></td>
                                    <td><?php echo e($subscription->ends_at->format('Y-m-d H:i')); ?></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($subscription->status === 'active'): ?>
                                            <span class="badge bg-success"><?php echo e(__('messages.membership.active')); ?></span>
                                        <?php elseif($subscription->status === 'expired'): ?>
                                            <span class="badge bg-secondary"><?php echo e(__('messages.membership.expired')); ?></span>
                                        <?php elseif($subscription->status === 'cancelled'): ?>
                                            <span class="badge bg-warning"><?php echo e(__('messages.membership.cancelled')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td>💰 <?php echo e($subscription->price_paid); ?></td>
                                    <td class="text-muted small"><?php echo e($subscription->translatedNotes); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center mb-0"><?php echo e(__('messages.membership.no_subscription_records')); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="alert alert-info mb-0 mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong><?php echo e(__('messages.membership.stacking_info')); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmCancel() {
    if (confirm('<?php echo e(__('messages.membership.confirm_cancel')); ?>')) {
        document.getElementById('cancel-form').submit();
    }
}

// 活跃度兑换金币预览
const pointsInput = document.querySelector('input[name="points"]');
if (pointsInput) {
    pointsInput.addEventListener('input', function() {
        const points = parseInt(this.value) || 0;
        const coins = Math.floor(points / 100);
        document.getElementById('coins-preview').textContent = coins;
    });
}

// 更新价格和按钮状态
function updatePrice(selectElement, basePrice, baseDays) {
    const months = parseInt(selectElement.value);
    const planId = selectElement.closest('form').querySelector('input[name="plan_id"]').value;
    
    // 计算总价和总天数
    const totalPrice = basePrice * months;
    const totalDays = baseDays * months;
    
    // 更新价格显示
    document.getElementById(`price-${planId}`).textContent = `${totalPrice} 💰`;
    document.getElementById(`hidden-price-${planId}`).value = totalPrice;
    
    // 更新按钮状态
    const userCoins = <?php echo e($user->coins); ?>;
    const btn = document.getElementById(`btn-${planId}`);
    
    if (userCoins < totalPrice) {
        btn.disabled = true;
        btn.innerHTML = '💰 <?php echo e(__('messages.membership.insufficient_coins')); ?>';
    } else {
        btn.disabled = false;
        btn.innerHTML = '🛒 <?php echo e(__('messages.membership.buy_now')); ?>';
    }
}
</script>

<form id="cancel-form" action="<?php echo e(route('membership.cancel')); ?>" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/MyWork/APP/ChatMeGo/resources/views/membership/index.blade.php ENDPATH**/ ?>