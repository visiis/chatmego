<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['user' => null, 'level' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['user' => null, 'level' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    if (!$level && $user) {
        $level = $user->current_level;
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($level): ?>
    <span class="badge badge-level badge-level-<?php echo e(strtolower($level->name)); ?>" title="<?php echo e($level->name); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($level->icon): ?>
            <span class="level-icon"><?php echo e($level->icon); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <span class="level-name"><?php echo e($level->name); ?></span>
    </span>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<style>
    .badge-level {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
        color: #666;
    }
    
    .badge-level-青铜会员 {
        background: linear-gradient(135deg, #cd7f32 0%, #8b4513 100%);
        color: white;
    }
    
    .badge-level-白银会员 {
        background: linear-gradient(135deg, #c0c0c0 0%, #808080 100%);
        color: white;
    }
    
    .badge-level-黄金会员 {
        background: linear-gradient(135deg, #ffd700 0%, #daa520 100%);
        color: white;
    }
    
    .badge-level-钻石会员 {
        background: linear-gradient(135deg, #b9f2ff 0%, #00bfff 100%);
        color: white;
    }
    
    .badge-level-王者会员 {
        background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(255, 107, 107, 0.4);
    }
    
    .level-icon {
        font-size: 14px;
    }
    
    .level-name {
        white-space: nowrap;
    }
</style>
<?php /**PATH /Volumes/MyWork/APP/ChatMeGo/resources/views/components/member-level-badge.blade.php ENDPATH**/ ?>