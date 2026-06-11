<div class="p-6">
    @if(Auth::id() == $user->id)
    <div class="card mb-4">
        <div class="card-body">
            <button class="btn btn-primary" onclick="showCreateAlbumModal()">
                <i class="fas fa-plus"></i> 创建相册
            </button>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            @if($albums->count() == 0)
            <div class="card">
                <div class="card-body text-center py-8">
                    <i class="far fa-images text-muted mb-3" style="font-size: 48px;"></i>
                    <p class="text-muted">暂无相册</p>
                </div>
            </div>
            @else
            <div class="space-y-3">
                @foreach($albums as $album)
                <div class="card album-card" data-album-id="{{ $album->id }}">
                    <img src="{{ $album->photos->first()?->thumbnail_url ?? $album->photos->first()?->image_url ?? '/images/default-album.png' }}" 
                         class="card-img-top" 
                         alt="{{ $album->name }}"
                         style="height: 120px; object-fit: cover;">
                    <div class="card-body p-2">
                        <h6 class="card-title text-sm">{{ $album->name }}</h6>
                        <p class="card-text text-muted text-xs">{{ $album->photos->count() }} 张图片</p>
                        @if($album->privacy)
                        <span class="badge badge-success badge-xs">公开</span>
                        @else
                        <span class="badge badge-warning badge-xs">付费 {{ $album->price }} 金币</span>
                        @endif
                    </div>
                    <div class="card-footer p-2 d-flex gap-1">
                        @if(Auth::id() == $user->id)
                        <button class="btn btn-xs btn-light flex-1 edit-album-btn" data-album-id="{{ $album->id }}">
                            <i class="fas fa-edit"></i> 编辑
                        </button>
                        @endif
                        <button class="btn btn-xs btn-primary flex-1 view-album-btn" data-album-id="{{ $album->id }}">
                            <i class="fas fa-eye"></i> 查看
                        </button>
                        @if(Auth::id() == $user->id)
                        <button class="btn btn-xs btn-danger flex-1 delete-album-btn" data-album-id="{{ $album->id }}">
                            <i class="fas fa-trash"></i> 删除
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="col-md-8">
            <div id="album-detail">
                <div class="card">
                    <div class="card-body text-center py-8">
                        <i class="far fa-images text-muted mb-3" style="font-size: 48px;"></i>
                        <p class="text-muted">请选择一个相册查看详情</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>