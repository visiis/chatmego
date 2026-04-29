<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <h2 class="mb-4">🎁 我的礼物</h2>
    
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
    
    <!-- 实体礼物 -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📦 实体礼物</h5>
            <div>
                <button type="button" class="btn btn-light btn-sm me-2" onclick="checkRedemptionInfo()">
                    📝 填写兑换信息
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="redeemSelected()" id="redeemBtn" disabled>
                    ✅ 兑换选中
                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasRedemptionInfo): ?>
                    <span class="badge bg-success ms-2">✓ 已填写兑换信息</span>
                <?php else: ?>
                    <span class="badge bg-warning ms-2">⚠ 未填写兑换信息</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($physicalGifts->isEmpty()): ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-box fa-3x mb-3"></i>
                    <p class="mb-0">暂无实体礼物</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="physicalGiftsTable">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-4" style="width: 50px;">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th scope="col">礼物</th>
                                <th scope="col">价格类型</th>
                                <th scope="col">价格</th>
                                <th scope="col">获得时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $physicalGifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userGift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="ps-4">
                                        <input type="checkbox" 
                                               class="gift-checkbox" 
                                               data-user-gift-id="<?php echo e($userGift->id); ?>"
                                               data-gift-name="<?php echo e($userGift->gift->name); ?>"
                                               onchange="updateRedeemButton()">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($userGift->gift->image): ?>
                                                <img src="<?php echo e(image_url($userGift->gift->image)); ?>" 
                                                     alt="<?php echo e($userGift->gift->name); ?>" 
                                                     class="rounded me-3"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded bg-light d-flex align-items-center justify-content-center me-3"
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-gift text-muted"></i>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <span class="fw-medium"><?php echo e($userGift->gift->name); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo e($userGift->gift->getTranslatedPriceTypeAttribute()); ?></td>
                                    <td><?php echo e($userGift->gift->price); ?></td>
                                    <td class="text-muted small">
                                        <?php echo e($userGift->created_at->format('Y-m-d H:i')); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    
    <!-- 虚拟礼物 -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">💎 虚拟礼物</h5>
        </div>
        <div class="card-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($virtualGifts->isEmpty()): ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-gift fa-3x mb-3"></i>
                    <p class="mb-0">暂无虚拟礼物</p>
                </div>
            <?php else: ?>
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $virtualGifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userGift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <div class="gift-image-wrapper" style="position: relative; height: 150px; overflow: hidden;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($userGift->gift->image): ?>
                                        <img src="<?php echo e(image_url($userGift->gift->image)); ?>" 
                                             alt="<?php echo e($userGift->gift->name); ?>" 
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                            <i class="fas fa-gift fa-2x text-muted"></i>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($userGift->quantity > 1): ?>
                                        <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                                            ×<?php echo e($userGift->quantity); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="card-body p-3 text-center">
                                    <h6 class="card-title text-truncate mb-2"><?php echo e($userGift->gift->name); ?></h6>
                                    <p class="card-text small text-primary mb-2">
                                        <?php echo e($userGift->gift->getTranslatedPriceTypeAttribute()); ?>: <?php echo e($userGift->gift->price); ?>

                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    
    <!-- 兑换记录链接 -->
    <div class="text-center mt-4">
        <a href="<?php echo e(route('user.gifts.history')); ?>" class="btn btn-outline-primary">
            📋 查看兑换记录
        </a>
    </div>
</div>

<!-- 兑换信息模态框 -->
<div class="modal fade" id="redemptionInfoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="redemptionInfoForm">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">📝 填写兑换信息</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">收件人姓名 <span class="text-danger">*</span></label>
                        <input type="text" name="recipient_name" class="form-control" value="<?php echo e($hasRedemptionInfo ? auth()->user()->recipient_name : ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">手机号 <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" placeholder="请输入手机号" value="<?php echo e($hasRedemptionInfo ? auth()->user()->phone : ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">详细地址 <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" rows="3" placeholder="请输入详细地址" required><?php echo e($hasRedemptionInfo ? auth()->user()->address : ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">收件人手机号（可选）</label>
                        <input type="tel" name="recipient_phone" class="form-control" placeholder="如与上面不同请填写" value="<?php echo e($hasRedemptionInfo ? auth()->user()->recipient_phone : ''); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">保存信息</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.gift-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
}
#selectAll {
    width: 18px;
    height: 18px;
    cursor: pointer;
}
</style>

<script>
// 全选/取消全选
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.gift-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateRedeemButton();
}

// 更新兑换按钮状态
function updateRedeemButton() {
    const selected = document.querySelectorAll('.gift-checkbox:checked');
    const btn = document.getElementById('redeemBtn');
    if (selected.length > 0) {
        btn.disabled = false;
        btn.innerHTML = '✅ 兑换选中 (' + selected.length + ')';
    } else {
        btn.disabled = true;
        btn.innerHTML = '✅ 兑换选中';
    }
}

// 检查兑换信息
function checkRedemptionInfo() {
    // 不需要检查是否选择产品，直接打开填写信息模态框
    const modalElement = document.getElementById('redemptionInfoModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// 兑换选中的礼物
function redeemSelected() {
    const selected = document.querySelectorAll('.gift-checkbox:checked');
    if (selected.length === 0) {
        alert('请先选择要兑换的礼物');
        return;
    }
    
    // 收集选中的礼物信息
    const gifts = [];
    selected.forEach(cb => {
        gifts.push({
            user_gift_id: cb.dataset.userGiftId,
            name: cb.dataset.giftName
        });
    });
    
    // 提交表单
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo e(route("user.gifts.redeem-multiple")); ?>';
    
    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = '<?php echo e(csrf_token()); ?>';
    form.appendChild(token);
    
    gifts.forEach((gift, index) => {
        const userGiftIdInput = document.createElement('input');
        userGiftIdInput.type = 'hidden';
        userGiftIdInput.name = 'user_gift_ids[' + index + ']';
        userGiftIdInput.value = gift.user_gift_id;
        form.appendChild(userGiftIdInput);
    });
    
    document.body.appendChild(form);
    form.submit();
}

// 初始化
document.addEventListener('DOMContentLoaded', function() {
    updateRedeemButton();
    
    // 确保 Bootstrap Dropdown 正常工作
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(element) {
        new bootstrap.Dropdown(element);
    });
    
    // 处理兑换信息表单提交
    document.getElementById('redemptionInfoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        
        fetch('<?php echo e(route("user.gifts.save-redemption-info")); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // 关闭模态框
            const modalElement = document.getElementById('redemptionInfoModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
            
            // 刷新页面
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('保存失败，请重试');
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/MyWork/APP/ChatMeGo/resources/views/user/gifts/index.blade.php ENDPATH**/ ?>