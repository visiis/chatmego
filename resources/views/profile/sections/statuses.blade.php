<div class="p-6">
    @if(Auth::id() == $user->id)
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('status.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <textarea name="content" class="form-control mb-3" rows="3" placeholder="分享你的心情..."></textarea>
                <div class="input-group mb-3">
                    <div class="custom-file">
                        <input type="file" name="images[]" class="custom-file-input" id="status-images" multiple accept="image/*">
                        <label class="custom-file-label" for="status-images">选择图片</label>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="form-check">
                        <input type="checkbox" name="is_private" class="form-check-input" id="is-private">
                        <label class="form-check-label" for="is-private">仅自己可见</label>
                    </div>
                    <button type="submit" class="btn btn-primary">发布说说</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($statuses->count() == 0)
    <div class="card">
        <div class="card-body text-center py-8">
            <i class="far fa-message-circle text-muted mb-3" style="font-size: 48px;"></i>
            <p class="text-muted">暂无说说</p>
        </div>
    </div>
    @else
    <div class="space-y-4">
        @foreach($statuses as $status)
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center">
                        <img src="{{ avatar_url($status->user->avatar) }}" 
                             alt="{{ $status->user->name }}" 
                             class="rounded-circle mr-3" 
                             style="width: 40px; height: 40px; object-fit: cover;">
                        <div>
                            <h6 class="font-weight-bold">{{ $status->user->name }}</h6>
                            <p class="text-xs text-muted">{{ $status->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if(Auth::id() == $user->id)
                    <button class="btn btn-xs btn-danger delete-status-btn" data-status-id="{{ $status->id }}">
                        <i class="fas fa-trash"></i> 删除
                    </button>
                    @endif
                </div>
                
                <p class="card-text">{{ $status->content }}</p>
                
                @if($status->images->count() > 0)
                <div class="row mt-3">
                    @foreach($status->images as $image)
                    <div class="col-md-3 mb-2">
                        <img src="{{ $image->image_url }}" 
                             class="img-fluid rounded" 
                             style="height: 100px; object-fit: cover; cursor: pointer;"
                             onclick="openImageLightbox('{{ $image->image_url }}')">
                    </div>
                    @endforeach
                </div>
                @endif
                
                <div class="d-flex gap-4 mt-3">
                    <button class="btn btn-link text-muted like-btn" data-status-id="{{ $status->id }}">
                        <i class="far fa-heart"></i>
                        <span>{{ $status->likes_count }}</span>
                        <span>点赞</span>
                    </button>
                    <button class="btn btn-link text-muted comment-btn" data-status-id="{{ $status->id }}">
                        <i class="far fa-comment"></i>
                        <span>{{ $status->comments_count }}</span>
                        <span>评论</span>
                    </button>
                </div>
                
                <div id="comments-{{ $status->id }}" style="display: none;" class="mt-3 pt-3 border-top">
                    @foreach($status->comments as $comment)
                    <div class="d-flex mb-2">
                        <img src="{{ avatar_url($comment->user->avatar) }}" 
                             alt="{{ $comment->user->name }}" 
                             class="rounded-circle mr-2" 
                             style="width: 30px; height: 30px; object-fit: cover;">
                        <div class="flex-1">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-bold text-sm">{{ $comment->user->name }}</span>
                                <span class="text-xs text-muted">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm">{{ $comment->content }}</p>
                        </div>
                    </div>
                    @endforeach
                    
                    @if(Auth::check())
                    <div class="input-group mt-2">
                        <input type="text" class="form-control form-control-sm comment-input" 
                               data-status-id="{{ $status->id }}" 
                               placeholder="写下你的评论...">
                        <button class="btn btn-primary btn-sm send-comment-btn" data-status-id="{{ $status->id }}">发送</button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>