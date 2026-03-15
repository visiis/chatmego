<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <?php echo e($user->id == auth()->id() ? '个人资料' : $user->name . '的资料'); ?>

                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->id == auth()->id()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                            <div class="alert alert-success">
                                <?php echo e(session('success')); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="alert alert-info mb-4">
                            为了更加匹配到心意的那个TA，请真实填写自己的交友情况
                        </div>

                        <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            <div class="form-group row mb-4">
                                <label class="col-md-4 col-form-label text-md-right">当前头像</label>
                                <div class="col-md-6">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->avatar): ?>
                                        <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="头像" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?php echo e(asset('images/default-avatar.svg')); ?>" alt="默认头像" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="avatar" class="col-md-4 col-form-label text-md-right">上传头像</label>
                                <div class="col-md-6">
                                    <input id="avatar" type="file" class="form-control-file" name="avatar" accept="image/*">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['avatar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback" role="alert">
                                            <?php echo e($message); ?>

                                        </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="name" class="col-md-4 col-form-label text-md-right">姓名</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name" value="<?php echo e(old('name', $user->name)); ?>" required autocomplete="name" autofocus>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback" role="alert">
                                            <?php echo e($message); ?>

                                        </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="email" class="col-md-4 col-form-label text-md-right">邮箱</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email', $user->email)); ?>" required autocomplete="email">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback" role="alert">
                                            <?php echo e($message); ?>

                                        </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="phone" class="col-md-4 col-form-label text-md-right">电话</label>
                                <div class="col-md-6">
                                    <input id="phone" type="tel" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" autocomplete="tel">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback" role="alert">
                                            <?php echo e($message); ?>

                                        </span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="form-group row mb-4">
                                <label for="gender" class="col-md-4 col-form-label text-md-right">性别</label>
                                <div class="col-md-6">
                                    <select id="gender" class="form-control" name="gender">
                                        <option value="">请选择</option>
                                        <option value="male" <?php echo e(old('gender', $user->gender) == 'male' ? 'selected' : ''); ?>>男</option>
                                        <option value="female" <?php echo e(old('gender', $user->gender) == 'female' ? 'selected' : ''); ?>>女</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="age" class="col-md-4 col-form-label text-md-right">年龄</label>
                                <div class="col-md-6">
                                    <input id="age" type="number" class="form-control" name="age" value="<?php echo e(old('age', $user->age)); ?>">
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="height" class="col-md-4 col-form-label text-md-right">身高</label>
                                <div class="col-md-6">
                                    <input id="height" type="text" class="form-control" name="height" value="<?php echo e(old('height', $user->height)); ?>">
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="weight" class="col-md-4 col-form-label text-md-right">体重</label>
                                <div class="col-md-6">
                                    <input id="weight" type="text" class="form-control" name="weight" value="<?php echo e(old('weight', $user->weight)); ?>">
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="form-group row mb-4">
                                <label for="hobbies" class="col-md-4 col-form-label text-md-right">爱好</label>
                                <div class="col-md-6">
                                    <textarea id="hobbies" class="form-control" name="hobbies" rows="3"><?php echo e(old('hobbies', $user->hobbies)); ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="specialty" class="col-md-4 col-form-label text-md-right">特长</label>
                                <div class="col-md-6">
                                    <textarea id="specialty" class="form-control" name="specialty" rows="3"><?php echo e(old('specialty', $user->specialty)); ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="love_declaration" class="col-md-4 col-form-label text-md-right">爱情宣言</label>
                                <div class="col-md-6">
                                    <textarea id="love_declaration" class="form-control" name="love_declaration" rows="3"><?php echo e(old('love_declaration', $user->love_declaration)); ?></textarea>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        更新资料
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <!-- 查看其他用户资料 -->
                        <div class="text-center mb-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->avatar): ?>
                                <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" 
                                     class="rounded-circle" 
                                     style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #f8f9fa;">
                            <?php else: ?>
                                <img src="<?php echo e(asset('images/default-avatar.svg')); ?>" 
                                     class="rounded-circle" 
                                     style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #f8f9fa;">
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <h3 class="mt-3"><?php echo e($user->name); ?></h3>
                        </div>

                        <div class="mt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>性别：</strong><?php echo e($user->gender == 'male' ? '男' : '女'); ?></p>
                                    <p class="mb-2"><strong>年龄：</strong><?php echo e($user->age); ?>岁</p>
                                    <p class="mb-2"><strong>身高：</strong><?php echo e($user->height); ?>cm</p>
                                    <p class="mb-2"><strong>体重：</strong><?php echo e($user->weight); ?>kg</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>邮箱：</strong><?php echo e($user->email); ?></p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->phone): ?>
                                        <p class="mb-2"><strong>电话：</strong><?php echo e($user->phone); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->hobbies): ?>
                            <div class="mt-4">
                                <h5>爱好</h5>
                                <p><?php echo e($user->hobbies); ?></p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->specialty): ?>
                            <div class="mt-4">
                                <h5>特长</h5>
                                <p><?php echo e($user->specialty); ?></p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->love_declaration): ?>
                            <div class="mt-4">
                                <h5>爱情宣言</h5>
                                <p><?php echo e($user->love_declaration); ?></p>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="mt-4 text-center">
                            <button class="btn btn-primary">
                                发起聊天
                            </button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/MyWork/APP/ChatMeGo/resources/views/profile.blade.php ENDPATH**/ ?>