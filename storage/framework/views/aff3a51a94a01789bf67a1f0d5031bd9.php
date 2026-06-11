<?php $__env->startSection('content'); ?>
<script>
    window.csrfToken = '<?php echo e(csrf_token()); ?>';
    window.currentUserId = <?php echo e(Auth::id() ?? 'null'); ?>;
    window.targetUserId = <?php echo e($user->id); ?>;
    window.isProfileOwner = <?php echo e(Auth::id() == $user->id ? 'true' : 'false'); ?>;
</script>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-container mx-auto mb-3">
                        <img src="<?php echo e(avatar_url($user->avatar)); ?>" 
                             alt="<?php echo e($user->name); ?>" 
                             class="rounded-circle img-fluid lazy-image" 
                             style="width: 120px; height: 120px; object-fit: cover;">
                    </div>
                    <h5 class="card-title"><?php echo e($user->name); ?></h5>
                    <p class="text-muted"><?php echo e($user->signature); ?></p>
                    <div class="row mt-3">
                        <div class="col-4">
                            <div class="text-center">
                                <div class="font-weight-bold"><?php echo e($albums->count()); ?></div>
                                <div class="text-xs text-muted">相册</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <div class="font-weight-bold"><?php echo e($statuses->count()); ?></div>
                                <div class="text-xs text-muted">说说</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center">
                                <div class="font-weight-bold"><?php echo e($user->coins); ?></div>
                                <div class="text-xs text-muted">金币</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">基本资料</h6>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">性别</span>
                            <span><?php echo e($user->gender == 1 ? '男' : ($user->gender == 2 ? '女' : '保密')); ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">年龄</span>
                            <span><?php echo e($user->age); ?> 岁</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">身高</span>
                            <span><?php echo e($user->height); ?> cm</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">体重</span>
                            <span><?php echo e($user->weight); ?> kg</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">会员等级</span>
                            <span><?php echo e($user->membershipLevel?->name ?? '普通会员'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>

        <div class="col-md-9">
            <div class="nav nav-tabs mb-3" id="profile-tabs" role="tablist">
                <button class="nav-link active" id="status-tab" data-bs-toggle="tab" data-bs-target="#status-panel" type="button">说说</button>
                <button class="nav-link" id="album-tab" data-bs-toggle="tab" data-bs-target="#album-panel" type="button">相册</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::id() != $user->id): ?>
                <button class="nav-link" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-panel" type="button">个人资料</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::id() == $user->id): ?>
                <button class="nav-link" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit-panel" type="button">编辑资料</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="tab-content" id="profile-tab-content">
                <div class="tab-pane fade show active" id="status-panel">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::id() == $user->id): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <form action="<?php echo e(route('status.store')); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <textarea name="content" class="form-control mb-3" rows="3" placeholder="分享你的心情..."></textarea>
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <input type="file" name="images[]" class="custom-file-input" id="status-images" multiple accept="image/*">
                                        <label class="custom-file-label" for="status-images"></label>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_private" class="form-check-input" id="is-private">
                                        <label class="form-check-label" for="is-private">仅自己可见</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">发布</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <img src="<?php echo e(avatar_url($status->user->avatar)); ?>" 
                                     alt="<?php echo e($status->user->name); ?>" 
                                     class="rounded-circle mr-3" 
                                     style="width: 48px; height: 48px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="font-weight-bold"><?php echo e($status->user->name); ?></h6>
                                        <span class="text-muted text-sm"><?php echo e($status->created_at->diffForHumans()); ?></span>
                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status->content): ?>
                                    <p class="mt-2"><?php echo e($status->content); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status->images && count($status->images) > 0): ?>
                                    <div class="row mt-3 status-images" data-status-id="<?php echo e($status->id); ?>">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $status->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-4 mb-2">
                                            <img src="<?php echo e($image); ?>" 
                                                 class="img-fluid rounded status-image-item" 
                                                 style="max-height: 150px; object-fit: cover; cursor: pointer;"
                                                 data-status-id="<?php echo e($status->id); ?>"
                                                 data-image-index="<?php echo e($index); ?>"
                                                 data-image-url="<?php echo e($image); ?>"
                                                 onclick="openStatusLightbox(<?php echo e($status->id); ?>, <?php echo e($index); ?>)">
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="d-flex mt-3">
                                        <button class="btn btn-link text-muted" data-status-id="<?php echo e($status->id); ?>" onclick="handleLikeBtn(this)">
                                            <i class="<?php echo e($status->liked ? 'fas fa-heart text-danger' : 'far fa-heart'); ?>"></i>
                                            <span class="ml-1 like-count"><?php echo e($status->likes_count); ?></span>
                                            <span class="ml-1">点赞</span>
                                        </button>
                                        <button class="btn btn-link text-muted ml-4" data-status-id="<?php echo e($status->id); ?>" onclick="handleCommentBtn(this)">
                                            <i class="far fa-comment"></i>
                                            <span class="ml-1"><?php echo e($status->comments_count); ?></span>
                                            <span class="ml-1">评论</span>
                                        </button>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::id() == $status->user_id): ?>
                                        <button class="btn btn-link text-muted ml-4" data-status-id="<?php echo e($status->id); ?>" onclick="handleDeleteBtn(this)">
                                            <i class="far fa-trash-alt"></i>
                                            <span class="ml-1">删除</span>
                                        </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div class="comment-section mt-3" id="comments-<?php echo e($status->id); ?>" style="display: none;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status->comments->count() > 0): ?>
                                        <div class="mb-3">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $status->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="media mb-2">
                                                <img src="<?php echo e(avatar_url($comment->user->avatar)); ?>" 
                                                     alt="<?php echo e($comment->user->name); ?>" 
                                                     class="rounded-circle mr-2" 
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                                <div class="media-body bg-light rounded p-2">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="font-weight-bold text-sm"><?php echo e($comment->user->name); ?></span>
                                                        <span class="text-xs text-muted"><?php echo e($comment->created_at->diffForHumans()); ?></span>
                                                    </div>
                                                    <p class="text-sm mt-1"><?php echo e($comment->content); ?></p>
                                                </div>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <div class="input-group">
                                            <input type="text" class="form-control comment-input" placeholder="写下评论..." data-status-id="<?php echo e($status->id); ?>">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" data-status-id="<?php echo e($status->id); ?>" onclick="handleSendCommentBtn(this)">发送</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statuses->count() == 0): ?>
                    <div class="card">
                        <div class="card-body text-center py-8">
                            <i class="far fa-newspaper text-muted mb-3" style="font-size: 48px;"></i>
                            <p class="text-muted">暂无说说</p>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="tab-pane fade" id="album-panel">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::id() == $user->id): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <button class="btn btn-primary btn-sm" onclick="showCreateAlbumModal()">
                                <i class="fas fa-plus"></i> 创建相册
                            </button>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($albums->count() == 0): ?>
                    <div class="card">
                        <div class="card-body text-center py-8">
                            <i class="far fa-images text-muted mb-3" style="font-size: 48px;"></i>
                            <p class="text-muted">暂无相册</p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::id() == $user->id): ?>
                            <button class="btn btn-primary mt-3" onclick="showCreateAlbumModal()">
                                <i class="fas fa-plus"></i> 创建相册
                            </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="row">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $albums; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $album): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <a href="<?php echo e(route('album.show', ['userId' => $user->id, 'albumId' => $album->id])); ?>">
                                    <img src="<?php echo e($album->photos->count() > 0 ? ($album->photos->first()->thumbnail_url ?? $album->photos->first()->image_url) : '/images/default-album.svg'); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo e($album->name); ?>"
                                         style="height: 200px; object-fit: cover;">
                                </a>
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo e($album->name); ?></h6>
                                    <p class="card-text text-muted text-sm"><?php echo e($album->photos->count()); ?> 张图片</p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($album->privacy): ?>
                                    <span class="badge badge-success">公开</span>
                                    <?php else: ?>
                                    <span class="badge badge-warning">付费 <?php echo e($album->price); ?> 金币</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex gap-2">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::id() == $user->id): ?>
                                        <button class="btn btn-xs btn-light flex-1" 
                                                data-album-id="<?php echo e($album->id); ?>" 
                                                data-album-name="<?php echo e($album->name); ?>" 
                                                data-album-description="<?php echo e($album->description); ?>"
                                                data-album-privacy="<?php echo e($album->privacy); ?>"
                                                data-album-price="<?php echo e($album->price); ?>"
                                                onclick="showEditAlbumModal(this)">
                                            <i class="fas fa-edit"></i> 编辑
                                        </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <a href="<?php echo e(route('album.show', ['userId' => $user->id, 'albumId' => $album->id])); ?>" class="btn btn-xs btn-primary flex-1">
                                            <i class="fas fa-eye"></i> 查看
                                        </a>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::id() == $user->id): ?>
                                        <button class="btn btn-xs btn-danger flex-1" 
                                                data-album-id="<?php echo e($album->id); ?>"
                                                onclick="handleDeleteAlbum(this)">
                                            <i class="fas fa-trash"></i> 删除
                                        </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="tab-pane fade" id="info-panel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">个人资料</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4 text-center">
                                    <img src="<?php echo e(avatar_url($user->avatar)); ?>" 
                                         alt="<?php echo e($user->name); ?>" 
                                         class="rounded-circle img-fluid" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                    <h5 class="mt-3"><?php echo e($user->name); ?></h5>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->signature): ?>
                                    <p class="text-muted text-sm"><?php echo e($user->signature); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">性别</span>
                                                <span><?php echo e($user->gender == 1 ? '男' : ($user->gender == 2 ? '女' : '保密')); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">年龄</span>
                                                <span><?php echo e($user->age); ?> 岁</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">身高</span>
                                                <span><?php echo e($user->height); ?> cm</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">体重</span>
                                                <span><?php echo e($user->weight); ?> kg</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">会员等级</span>
                                                <span><?php echo e($user->membershipLevel?->name ?? '普通会员'); ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">金币</span>
                                                <span><?php echo e($user->coins); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->hobbies || $user->specialty || $user->love_declaration): ?>
                            <div class="border-top pt-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->hobbies): ?>
                                <div class="mb-3">
                                    <h6 class="text-muted">兴趣爱好</h6>
                                    <p><?php echo e($user->hobbies); ?></p>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->specialty): ?>
                                <div class="mb-3">
                                    <h6 class="text-muted">特长技能</h6>
                                    <p><?php echo e($user->specialty); ?></p>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->love_declaration): ?>
                                <div class="mb-3">
                                    <h6 class="text-muted">爱情宣言</h6>
                                    <p><?php echo e($user->love_declaration); ?></p>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            
                            <div class="border-top pt-4 mt-4">
                                <h6 class="text-muted">统计信息</h6>
                                <div class="row mt-3">
                                    <div class="col-md-4 text-center">
                                        <div class="font-weight-bold"><?php echo e($albums->count()); ?></div>
                                        <div class="text-xs text-muted">相册</div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="font-weight-bold"><?php echo e($statuses->count()); ?></div>
                                        <div class="text-xs text-muted">说说</div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="font-weight-bold"><?php echo e($user->fans_count ?? 0); ?></div>
                                        <div class="text-xs text-muted">粉丝</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::id() == $user->id): ?>
                <div class="tab-pane fade" id="edit-panel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">编辑资料</h5>
                        </div>
                        <div class="card-body">
                            <form id="edit-profile-form" method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                
                                <input type="hidden" name="email" value="<?php echo e($user->email); ?>">
                                
                                <div class="text-center mb-6">
                                    <img src="<?php echo e(avatar_url($user->avatar)); ?>" 
                                         alt="<?php echo e($user->name); ?>" 
                                         class="rounded-circle img-fluid mb-3" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                    <div class="mt-3">
                                        <label for="avatar-input" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-upload"></i> 更换头像
                                        </label>
                                        <input type="file" name="avatar" id="avatar-input" accept="image/*" class="d-none" onchange="previewAvatar(this)">
                                        <p class="text-sm text-muted mt-2">支持 JPG、PNG 格式，建议尺寸 150x150</p>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="name">昵称</label>
                                    <input type="text" name="name" class="form-control" id="name" value="<?php echo e($user->name); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">手机号码</label>
                                    <input type="text" name="phone" class="form-control" id="phone" value="<?php echo e($user->phone); ?>" placeholder="请输入手机号码">
                                </div>
                                
                                <div class="form-group">
                                    <label for="signature">个性签名</label>
                                    <input type="text" name="signature" class="form-control" id="signature" value="<?php echo e($user->signature); ?>" placeholder="说说你的个性...">
                                </div>
                                
                                <div class="form-group">
                                    <label for="gender">性别</label>
                                    <select name="gender" class="form-control" id="gender">
                                        <option value="0" <?php echo e($user->gender == 0 ? 'selected' : ''); ?>>保密</option>
                                        <option value="1" <?php echo e($user->gender == 1 ? 'selected' : ''); ?>>男</option>
                                        <option value="2" <?php echo e($user->gender == 2 ? 'selected' : ''); ?>>女</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="age">年龄</label>
                                    <input type="number" name="age" class="form-control" id="age" value="<?php echo e($user->age); ?>" min="18" max="120">
                                </div>
                                
                                <div class="form-group">
                                    <label for="height">身高 (cm)</label>
                                    <input type="number" name="height" class="form-control" id="height" value="<?php echo e($user->height); ?>" min="100" max="250">
                                </div>
                                
                                <div class="form-group">
                                    <label for="weight">体重 (kg)</label>
                                    <input type="number" name="weight" class="form-control" id="weight" value="<?php echo e($user->weight); ?>" min="30" max="300">
                                </div>
                                
                                <div class="form-group">
                                    <label for="hobbies">兴趣爱好</label>
                                    <input type="text" name="hobbies" class="form-control" id="hobbies" value="<?php echo e($user->hobbies); ?>" placeholder="例如：音乐、阅读、运动...">
                                </div>
                                
                                <div class="form-group">
                                    <label for="specialty">特长技能</label>
                                    <input type="text" name="specialty" class="form-control" id="specialty" value="<?php echo e($user->specialty); ?>" placeholder="你的特长或技能...">
                                </div>
                                
                                <div class="form-group">
                                    <label for="love_declaration">爱情宣言</label>
                                    <textarea name="love_declaration" class="form-control" id="love_declaration" rows="3" placeholder="写下你的爱情宣言..."><?php echo e($user->love_declaration); ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-block mt-4">保存更改</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<div class="modal fade" id="status-lightbox-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <button type="button" class="close-btn text-white position-absolute top-4 right-4 z-10" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times" style="font-size: 24px; opacity: 0.8; transition: opacity 0.2s;"></i>
                </button>
            </div>
            <div class="modal-body p-0">
                <button type="button" class="btn btn-light btn-prev position-absolute top-50 left-4 transform -translate-y-50" style="z-index: 100;" onclick="prevStatusImage()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <img id="status-lightbox-image" src="" class="img-fluid mx-auto d-block max-h-[70vh]">
                <button type="button" class="btn btn-light btn-next position-absolute top-50 right-4 transform -translate-y-50" style="z-index: 100;" onclick="nextStatusImage()">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="modal-footer border-0 bg-dark">
                <div class="text-white">
                    <span id="status-lightbox-counter"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="album-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="album-modal-title">创建相册</h5>
                <button type="button" class="close-btn" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times" style="font-size: 18px; opacity: 0.7;"></i>
                </button>
            </div>
            <form id="album-form" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="album-id" name="album_id">
                <input type="hidden" name="_method" id="album-method" value="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="album-name">相册名称</label>
                        <input type="text" class="form-control" id="album-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="album-description">相册描述</label>
                        <textarea class="form-control" id="album-description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="album-privacy" name="privacy" value="1" checked>
                            <label class="form-check-label" for="album-privacy">公开相册</label>
                        </div>
                    </div>
                    <div class="form-group" id="album-price-group">
                        <label for="album-price">观看价格（金币）</label>
                        <input type="number" class="form-control" id="album-price" name="price" min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="lightbox-overlay" id="album-lightbox-overlay" style="display: none;">
    <div class="lightbox-container">
        <button class="lightbox-close" onclick="closeLightbox()">
            <i class="fas fa-times"></i>
        </button>
        
        <button class="lightbox-nav lightbox-prev" onclick="prevAlbumImage()" id="lightbox-prev">
            <i class="fas fa-chevron-left"></i>
        </button>
        
        <img id="album-lightbox-image" src="" class="lightbox-image">
        
        <button class="lightbox-nav lightbox-next" onclick="nextAlbumImage()" id="lightbox-next">
            <i class="fas fa-chevron-right"></i>
        </button>
        
        <div class="lightbox-counter" id="album-lightbox-counter"></div>
    </div>
</div>

<style>
.lightbox-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
}

.lightbox-container {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    cursor: default;
}

.lightbox-image {
    max-width: 100%;
    max-height: 85vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
}

.lightbox-close {
    position: absolute;
    top: -45px;
    right: 0;
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    z-index: 10;
}

.lightbox-close:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(1.1);
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    z-index: 10;
}

.lightbox-nav:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-50%) scale(1.1);
}

.lightbox-prev {
    left: -60px;
}

.lightbox-next {
    right: -60px;
}

.lightbox-counter {
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%);
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    font-weight: 500;
}

@media (max-width: 768px) {
    .lightbox-prev {
        left: 10px;
    }
    .lightbox-next {
        right: 10px;
    }
    .lightbox-close {
        top: -40px;
        right: 10px;
    }
}
</style>

<?php $__env->startPush('scripts'); ?>
<script>
function showEditProfile() {
    document.getElementById('edit-tab').click();
}

function previewAvatar(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.querySelector('#edit-panel img.rounded-circle');
            if (img) {
                img.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
}

function handleLikeBtn(btn) {
    console.log('Like button clicked:', btn.dataset.statusId);
    
    const statusId = btn.dataset.statusId;
    const countSpan = btn.querySelector('.like-count');
    const icon = btn.querySelector('i');
    
    fetch('/status/' + statusId + '/like', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        console.log('Like response:', data);
        if (data.success !== false) {
            countSpan.textContent = data.count;
            if (data.liked) {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-danger');
            } else {
                icon.classList.remove('fas', 'text-danger');
                icon.classList.add('far');
            }
        } else {
            alert(data.message || '点赞失败');
        }
    })
    .catch(err => {
        console.error('Like error:', err);
        alert('点赞失败');
    });
}

function handleCommentBtn(btn) {
    console.log('Comment button clicked:', btn.dataset.statusId);
    
    const statusId = btn.dataset.statusId;
    const section = document.getElementById('comments-' + statusId);
    if (section.style.display === 'none' || section.style.display === '') {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}

function handleSendCommentBtn(btn) {
    console.log('Send comment button clicked:', btn.dataset.statusId);
    
    const statusId = btn.dataset.statusId;
    const input = document.querySelector('.comment-input[data-status-id="' + statusId + '"]');
    const content = input.value.trim();
    if (!content) return;
    
    fetch('/status/' + statusId + '/comment', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ content })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Comment response:', data);
        if (data.success) {
            input.value = '';
            location.reload();
        } else {
            alert(data.message || '评论失败');
        }
    })
    .catch(err => {
        console.error('Comment error:', err);
        alert('评论失败');
    });
}

function handleDeleteBtn(btn) {
    console.log('Delete button clicked:', btn.dataset.statusId);
    
    if (!confirm('确定要删除这条说说吗？')) return;
    
    const statusId = btn.dataset.statusId;
    const card = btn.closest('.card');
    
    fetch('/status/' + statusId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        console.log('Delete response:', data);
        if (data.success) {
            card.remove();
        } else {
            alert(data.message || '删除失败');
        }
    })
    .catch(err => {
        console.error('Delete error:', err);
        alert('删除失败');
    });
}

function showCreateAlbumModal() {
    document.getElementById('album-modal-title').textContent = '创建相册';
    document.getElementById('album-id').value = '';
    document.getElementById('album-method').value = 'POST';
    document.getElementById('album-name').value = '';
    document.getElementById('album-description').value = '';
    document.getElementById('album-privacy').checked = true;
    document.getElementById('album-price').value = '0';
    document.getElementById('album-price-group').style.display = 'none';
    document.getElementById('album-form').action = '/album';
    $('#album-modal').modal('show');
}

function showEditAlbumModal(btn) {
    const albumId = btn.dataset.albumId;
    const name = btn.dataset.albumName;
    const description = btn.dataset.albumDescription || '';
    const privacy = parseInt(btn.dataset.albumPrivacy) === 1;
    const price = btn.dataset.albumPrice || '0';
    
    document.getElementById('album-modal-title').textContent = '编辑相册';
    document.getElementById('album-id').value = albumId;
    document.getElementById('album-method').value = 'PUT';
    document.getElementById('album-name').value = name;
    document.getElementById('album-description').value = description;
    document.getElementById('album-privacy').checked = privacy;
    document.getElementById('album-price').value = price;
    document.getElementById('album-price-group').style.display = privacy ? 'none' : 'block';
    document.getElementById('album-form').action = '/album/' + albumId;
    $('#album-modal').modal('show');
}

function handleDeleteAlbum(btn) {
    if (!confirm('确定要删除这个相册吗？删除后相册内的所有照片也会被删除。')) return;
    
    const albumId = btn.dataset.albumId;
    const card = btn.closest('.card');
    
    fetch('/album/' + albumId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        console.log('Delete album response:', data);
        if (data.success) {
            card.remove();
        } else {
            alert(data.message || '删除失败');
        }
    })
    .catch(err => {
        console.error('Delete album error:', err);
        alert('删除失败');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('album-privacy').addEventListener('change', function() {
        const priceGroup = document.getElementById('album-price-group');
        priceGroup.style.display = this.checked ? 'none' : 'block';
        if (this.checked) {
            document.getElementById('album-price').value = '0';
        }
    });
    
    document.getElementById('album-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const action = form.action;
        const method = form.querySelector('#album-method').value;
        
        const formData = new FormData(form);
        
        fetch(action, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            console.log('Album save response:', data);
            if (data.success) {
                $('#album-modal').modal('hide');
                location.reload();
            } else {
                alert(data.message || '保存失败');
            }
        })
        .catch(err => {
            console.error('Album save error:', err);
            alert('保存失败');
        });
    });
});

function selectAlbum(card) {
    const albumId = card.dataset.albumId;
    const albumName = card.dataset.albumName;
    const albumDescription = card.dataset.albumDescription;
    const albumPrivacy = parseInt(card.dataset.albumPrivacy) === 1;
    const albumPrice = card.dataset.albumPrice;
    const viewCount = card.dataset.albumViewCount;
    const purchaseCount = card.dataset.albumPurchaseCount;
    
    document.querySelectorAll('.album-card').forEach(c => c.classList.remove('border-primary'));
    card.classList.add('border-primary');
    
    fetch('/album/' + albumId + '/photos')
    .then(res => res.json())
    .then(data => {
        console.log('Album photos:', data);
        renderAlbumDetail(albumId, albumName, albumDescription, albumPrivacy, albumPrice, viewCount, purchaseCount, data.photos || []);
    })
    .catch(err => {
        console.error('Load album photos error:', err);
        alert('加载相册失败');
    });
}

function renderAlbumDetail(albumId, name, description, isPublic, price, viewCount, purchaseCount, photos) {
    const isOwner = window.isProfileOwner;
    const currentUserId = window.currentUserId;
    const targetUserId = window.targetUserId;
    const canView = isPublic || isOwner || currentUserId == targetUserId;
    
    let html = '<div class="card-header d-flex justify-content-between align-items-center"><div><h5>' + name + '</h5>';
    if (description) {
        html += '<p class="text-muted text-sm">' + description + '</p>';
    }
    html += '<div class="d-flex gap-3 mt-1 text-sm"><span class="text-muted">浏览 ' + viewCount + ' 次</span><span class="text-muted">购买 ' + purchaseCount + ' 次</span>';
    html += isPublic ? '<span class="badge badge-success">公开</span>' : '<span class="badge badge-warning">付费 ' + price + ' 金币</span>';
    html += '</div></div></div><div class="card-body">';
    
    if (!canView) {
        html += '<div class="text-center py-8"><i class="fas fa-lock fa-5x text-muted mb-4"></i><h5 class="card-title">该相册需要付费解锁</h5>';
        html += '<p class="text-muted mt-2">支付 ' + price + ' 金币即可查看相册内容</p>';
        html += '<button id="purchase-btn-' + albumId + '" class="btn btn-primary mt-4" onclick="purchaseAlbum(' + albumId + ')">立即支付 ' + price + ' 金币</button>';
        html += '<p class="text-sm text-danger mt-2" id="purchase-error-' + albumId + '"></p></div>';
    } else {
        html += '<div><div class="d-flex justify-content-between align-items-center mb-3"><h6>相册图片</h6>';
        if (isOwner) {
            html += '<button class="btn btn-primary btn-sm" onclick="showUploadPanel(' + albumId + ')">';
            html += '<i class="fas fa-upload"></i> 上传图片</button>';
        }
        html += '</div>';
        
        if (photos.length === 0) {
            html += '<p class="text-muted text-center py-4">暂无图片</p>';
        } else {
            html += '<div class="grid grid-cols-3 gap-2" id="photos-grid-' + albumId + '">';
            for (let i = 0; i < photos.length; i++) {
                const photo = photos[i];
                html += '<div class="relative group"><img src="' + (photo.thumbnail_url || photo.image_url) + '" ';
                html += 'alt="' + (photo.title || '图片') + '" class="w-full h-32 object-cover cursor-pointer album-photo-item" ';
                html += 'data-url="' + photo.image_url + '" onclick="openAlbumLightbox(' + albumId + ', ' + i + ')">';
                if (isOwner) {
                    html += '<button class="absolute top-1 right-1 btn btn-xs btn-danger opacity-0 group-hover:opacity-100 transition-opacity" ';
                    html += 'onclick="deletePhoto(' + photo.id + ', ' + albumId + ')">';
                    html += '<i class="fas fa-trash"></i></button>';
                }
                html += '</div>';
            }
            html += '</div>';
        }
        
        if (isOwner) {
            html += '<div id="upload-panel-' + albumId + '" class="mt-4" style="display: none;"><hr><h6 class="mt-3 mb-3">上传图片</h6>';
            html += '<ul class="nav nav-tabs mb-3"><li class="nav-item"><button class="nav-link active upload-tab-btn" ';
            html += 'data-album-id="' + albumId + '" data-tab="single" onclick="switchUploadTab(' + albumId + ', \'single\')">单张上传</button></li>';
            html += '<li class="nav-item"><button class="nav-link upload-tab-btn" data-album-id="' + albumId + '" ';
            html += 'data-tab="batch" onclick="switchUploadTab(' + albumId + ', \'batch\')">批量上传</button></li></ul>';
            
            html += '<div id="single-upload-' + albumId + '"><form id="upload-form-' + albumId + '" enctype="multipart/form-data">';
            html += '<input type="hidden" name="_token" value="' + window.csrfToken + '">';
            html += '<div class="form-group"><input type="file" name="image" id="image-upload-' + albumId + '" class="form-control" accept="image/*"></div>';
            html += '<div class="form-group"><input type="text" name="title" class="form-control" placeholder="图片标题（可选）"></div>';
            html += '<button type="submit" class="btn btn-primary" id="upload-btn-' + albumId + '">上传图片</button>';
            html += '<div id="upload-progress-' + albumId + '" class="mt-3" style="display: none;"><div class="progress">';
            html += '<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">';
            html += '<span id="progress-text-' + albumId + '">0%</span></div></div></div>';
            html += '<p class="text-sm text-danger mt-2" id="upload-error-' + albumId + '"></p></form></div>';
            
            html += '<div id="batch-upload-' + albumId + '" style="display: none;"><form id="batch-upload-form-' + albumId + '" enctype="multipart/form-data">';
            html += '<input type="hidden" name="_token" value="' + window.csrfToken + '">';
            html += '<div class="form-group"><input type="file" name="images[]" id="batch-image-upload-' + albumId + '" class="form-control" accept="image/*" multiple>';
            html += '<small class="form-text text-muted">按住 Ctrl 或 Command 键选择多张图片</small></div>';
            html += '<button type="submit" class="btn btn-primary" id="batch-upload-btn-' + albumId + '">批量上传</button>';
            html += '<div id="batch-upload-progress-' + albumId + '" class="mt-3" style="display: none;"><div class="progress">';
            html += '<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">';
            html += '<span id="batch-progress-text-' + albumId + '">0%</span></div></div>';
            html += '<p id="upload-status-' + albumId + '" class="text-sm mt-2"></p></div>';
            html += '<p class="text-sm text-danger mt-2" id="batch-upload-error-' + albumId + '"></p>';
            html += '<div id="batch-upload-results-' + albumId + '" class="mt-3" style="display: none;">';
            html += '<h6>上传结果</h6><div id="success-files-' + albumId + '" class="text-success"></div>';
            html += '<div id="failed-files-' + albumId + '" class="text-danger"></div></div></form></div></div>';
        }
        
        html += '</div>';
    }
    
    html += '</div>';
    document.getElementById('album-detail').innerHTML = html;
    
    initUploadForm(albumId);
}

function showUploadPanel(albumId) {
    const panel = document.getElementById('upload-panel-' + albumId);
    panel.style.display = panel.style.display === 'none' || panel.style.display === '' ? 'block' : 'none';
}

function switchUploadTab(albumId, tab) {
    document.querySelectorAll('.upload-tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    document.getElementById('single-upload-' + albumId).style.display = tab === 'single' ? 'block' : 'none';
    document.getElementById('batch-upload-' + albumId).style.display = tab === 'batch' ? 'block' : 'none';
}

function initUploadForm(albumId) {
    const form = document.getElementById('upload-form-' + albumId);
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            uploadPhoto(albumId);
        });
    }
    
    const batchForm = document.getElementById('batch-upload-form-' + albumId);
    if (batchForm) {
        batchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            batchUploadPhotos(albumId);
        });
    }
}

function uploadPhoto(albumId) {
    const form = document.getElementById('upload-form-' + albumId);
    const formData = new FormData(form);
    
    const progressDiv = document.getElementById('upload-progress-' + albumId);
    const progressBar = progressDiv.querySelector('.progress-bar');
    const progressText = document.getElementById('progress-text-' + albumId);
    const errorDiv = document.getElementById('upload-error-' + albumId);
    
    progressDiv.style.display = 'block';
    errorDiv.textContent = '';
    
    fetch('/album/' + albumId + '/upload', {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        progressDiv.style.display = 'none';
        if (data.success) {
            form.reset();
            const card = document.querySelector(`.album-card[data-album-id="${albumId}"]`);
            if (card) {
                selectAlbum(card);
            }
        } else {
            errorDiv.textContent = data.message || '上传失败';
        }
    })
    .catch(err => {
        progressDiv.style.display = 'none';
        errorDiv.textContent = '上传失败';
        console.error('Upload error:', err);
    });
}

function batchUploadPhotos(albumId) {
    const form = document.getElementById('batch-upload-form-' + albumId);
    const formData = new FormData(form);
    
    const progressDiv = document.getElementById('batch-upload-progress-' + albumId);
    const progressBar = progressDiv.querySelector('.progress-bar');
    const progressText = document.getElementById('batch-progress-text-' + albumId);
    const statusDiv = document.getElementById('upload-status-' + albumId);
    const errorDiv = document.getElementById('batch-upload-error-' + albumId);
    const resultsDiv = document.getElementById('batch-upload-results-' + albumId);
    const successDiv = document.getElementById('success-files-' + albumId);
    const failedDiv = document.getElementById('failed-files-' + albumId);
    
    progressDiv.style.display = 'block';
    resultsDiv.style.display = 'none';
    errorDiv.textContent = '';
    successDiv.innerHTML = '';
    failedDiv.innerHTML = '';
    
    fetch('/album/' + albumId + '/upload-multiple', {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        progressDiv.style.display = 'none';
        if (data.success) {
            form.reset();
            resultsDiv.style.display = 'block';
            if (data.successCount > 0) {
                successDiv.innerHTML = `<p>成功上传 ${data.successCount} 张图片</p>`;
            }
            if (data.failedCount > 0) {
                failedDiv.innerHTML = `<p>失败 ${data.failedCount} 张图片</p>`;
                if (data.failedFiles) {
                    failedDiv.innerHTML += `<p class="text-sm">失败文件: ${data.failedFiles.join(', ')}</p>`;
                }
            }
            const card = document.querySelector(`.album-card[data-album-id="${albumId}"]`);
            if (card) {
                selectAlbum(card);
            }
        } else {
            errorDiv.textContent = data.message || '上传失败';
        }
    })
    .catch(err => {
        progressDiv.style.display = 'none';
        errorDiv.textContent = '上传失败';
        console.error('Batch upload error:', err);
    });
}

function deletePhoto(photoId, albumId) {
    if (!confirm('确定要删除这张图片吗？')) return;
    
    fetch('/album/photo/' + photoId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const card = document.querySelector(`.album-card[data-album-id="${albumId}"]`);
            if (card) {
                selectAlbum(card);
            }
        } else {
            alert(data.message || '删除失败');
        }
    })
    .catch(err => {
        console.error('Delete photo error:', err);
        alert('删除失败');
    });
}

function purchaseAlbum(albumId) {
    const errorDiv = document.getElementById('purchase-error-' + albumId);
    const btn = document.getElementById('purchase-btn-' + albumId);
    
    btn.disabled = true;
    errorDiv.textContent = '';
    
    fetch('/album/' + albumId + '/purchase', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        if (data.success) {
            const card = document.querySelector(`.album-card[data-album-id="${albumId}"]`);
            if (card) {
                selectAlbum(card);
            }
        } else {
            errorDiv.textContent = data.message || '购买失败';
        }
    })
    .catch(err => {
        btn.disabled = false;
        errorDiv.textContent = '购买失败';
        console.error('Purchase error:', err);
    });
}

let currentAlbumPhotos = [];
let currentPhotoIndex = 0;

function openAlbumLightbox(albumId, index) {
    fetch('/album/' + albumId + '/photos')
    .then(res => res.json())
    .then(data => {
        currentAlbumPhotos = data.photos || [];
        currentPhotoIndex = index;
        showLightbox();
    })
    .catch(err => console.error('Load photos error:', err));
}

function showLightbox() {
    console.log('showLightbox called, photos:', currentAlbumPhotos.length, 'index:', currentPhotoIndex);
    
    if (currentAlbumPhotos.length === 0) return;
    
    const overlay = document.getElementById('album-lightbox-overlay');
    const image = document.getElementById('album-lightbox-image');
    const counter = document.getElementById('album-lightbox-counter');
    const prevBtn = document.getElementById('lightbox-prev');
    const nextBtn = document.getElementById('lightbox-next');
    
    console.log('Overlay element:', overlay);
    
    image.src = currentAlbumPhotos[currentPhotoIndex].image_url;
    counter.textContent = `${currentPhotoIndex + 1} / ${currentAlbumPhotos.length}`;
    
    prevBtn.style.display = currentPhotoIndex === 0 ? 'none' : 'flex';
    nextBtn.style.display = currentPhotoIndex === currentAlbumPhotos.length - 1 ? 'none' : 'flex';
    
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const overlay = document.getElementById('album-lightbox-overlay');
    overlay.style.display = 'none';
    document.body.style.overflow = '';
}

function prevAlbumImage() {
    if (currentPhotoIndex > 0) {
        currentPhotoIndex--;
        showLightbox();
    }
}

function nextAlbumImage() {
    if (currentPhotoIndex < currentAlbumPhotos.length - 1) {
        currentPhotoIndex++;
        showLightbox();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('album-lightbox-overlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeLightbox();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (overlay.style.display === 'flex') {
                if (e.key === 'Escape') {
                    closeLightbox();
                } else if (e.key === 'ArrowLeft') {
                    prevAlbumImage();
                } else if (e.key === 'ArrowRight') {
                    nextAlbumImage();
                }
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('edit-profile-form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            console.log('Action:', this.action);
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: formData
            })
            .then(res => {
                console.log('Response status:', res.status);
                return res.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || '保存失败');
                }
            })
            .catch(err => {
                console.error('Profile update error:', err);
                alert('保存失败: ' + err.message);
            });
        });
    } else {
        console.log('Edit profile form not found');
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Volumes/MyWork/APP/ChatMeGo/resources/views/profile.blade.php ENDPATH**/ ?>