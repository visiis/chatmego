<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><?php echo e(__('messages.settings.title')); ?></div>

                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <form method="POST" action="<?php echo e(route('settings.update')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="form-group row mb-4">
                            <label for="language" class="col-md-4 col-form-label text-md-right"><?php echo e(__('messages.settings.language')); ?></label>

                            <div class="col-md-6">
                                <select id="language" class="form-control" name="language" required>
                                    <option value="zh_TW" <?php echo e(session('locale', config('app.locale')) == 'zh_TW' ? 'selected' : ''); ?>>中文（繁體）</option>
                                    <option value="zh_CN" <?php echo e(session('locale', config('app.locale')) == 'zh_CN' ? 'selected' : ''); ?>>中文（简体）</option>
                                    <option value="en" <?php echo e(session('locale', config('app.locale')) == 'en' ? 'selected' : ''); ?>>English</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo e(__('messages.settings.save')); ?>

                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/MyWork/APP/ChatMeGo/resources/views/settings.blade.php ENDPATH**/ ?>