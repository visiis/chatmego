@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('album.index', $user->id) }}" class="btn btn-secondary btn-sm">返回相册列表</a>
                    <h2 class="mt-2">{{ $album->name }}</h2>
                </div>
                @if($isOwner)
                <div>
                    <a href="{{ route('album.edit', $album->id) }}" class="btn btn-secondary">编辑相册</a>
                    <button class="btn btn-danger ml-2" onclick="deleteAlbum()">删除相册</button>
                </div>
                @endif
            </div>
            @if($album->description)
            <p class="text-muted mt-2">{{ $album->description }}</p>
            @endif
            <div class="d-flex gap-4 mt-2">
                <span class="text-muted">浏览 {{ $album->view_count }} 次</span>
                <span class="text-muted">购买 {{ $album->purchase_count }} 次</span>
                @if($album->privacy)
                <span class="badge badge-success">公开</span>
                @else
                <span class="badge badge-warning">付费观看</span>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(!$canView)
    <div class="card text-center py-12">
        <div class="card-body">
            <i class="fas fa-lock fa-5x text-muted mb-4"></i>
            <h3 class="card-title">该相册需要付费解锁</h3>
            <p class="card-text text-muted mt-2">支付 {{ $album->price }} 金币即可查看相册内容</p>
            <p class="text-sm text-muted">* 有效期30天，50%金币将返还给相册所有者</p>
            <button id="purchase-btn" class="btn btn-primary mt-4" onclick="purchaseAlbum()">
                立即支付 {{ $album->price }} 金币
            </button>
            <p class="text-sm text-danger mt-2" id="purchase-error"></p>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">相册图片</h5>
                    @if($photos->isEmpty())
                    <p class="text-muted">暂无图片</p>
                    @endif
                    <div class="row">
                        @foreach($photos as $photo)
                        <div class="col-md-3 mb-3">
                            <div class="card h-100">
                                <img src="{{ $photo->thumbnail_url }}" 
                                     alt="{{ $photo->title }}" 
                                     class="card-img-top album-photo-item" 
                                     data-url="{{ $photo->image_url }}"
                                     data-title="{{ $photo->title }}"
                                     style="height: 200px; object-fit: cover; cursor: pointer;"
                                     onclick="openLightbox('{{ $photo->image_url }}')">
                                <div class="card-body p-2">
                                    @if($photo->title)
                                    <p class="card-text text-sm">{{ $photo->title }}</p>
                                    @endif
                                    @if($isOwner)
                                    <button class="btn btn-danger btn-sm w-100" onclick="deletePhoto({{ $photo->id }})">删除</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if($isOwner)
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">上传图片</h5>
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" id="single-upload-tab" data-bs-toggle="tab" href="#single-upload-panel">单张上传</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="batch-upload-tab" data-bs-toggle="tab" href="#batch-upload-panel">批量上传</a>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="single-upload-panel">
                            <form id="upload-form" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="form-group">
                                    <input type="file" name="image" id="image-upload" class="form-control" accept="image/*">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="title" class="form-control" placeholder="图片标题（可选）">
                                </div>
                                <div class="form-group">
                                    <textarea name="description" class="form-control" rows="2" placeholder="图片描述（可选）"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" id="upload-btn">上传图片</button>
                                <div id="upload-progress" class="mt-3" style="display: none;">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                            <span id="progress-text">0%</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-danger mt-2" id="upload-error"></p>
                            </form>
                        </div>
                        
                        <div class="tab-pane fade" id="batch-upload-panel">
                            <form id="batch-upload-form" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="form-group">
                                    <input type="file" name="images[]" id="batch-image-upload" class="form-control" accept="image/*" multiple>
                                    <small class="form-text text-muted">按住 Ctrl 或 Command 键选择多张图片</small>
                                </div>
                                <button type="submit" class="btn btn-primary" id="batch-upload-btn">批量上传</button>
                                <div id="batch-upload-progress" class="mt-3" style="display: none;">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                            <span id="batch-progress-text">0%</span>
                                        </div>
                                    </div>
                                    <p id="upload-status" class="text-sm mt-2"></p>
                                </div>
                                <p class="text-sm text-danger mt-2" id="batch-upload-error"></p>
                                <div id="batch-upload-results" class="mt-3" style="display: none;">
                                    <h6>上传结果</h6>
                                    <div id="success-files" class="text-success"></div>
                                    <div id="failed-files" class="text-danger"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>

<div class="lightbox-overlay" id="album-lightbox-overlay" style="display: none;">
    <div class="lightbox-container">
        <button class="lightbox-close" onclick="closeLightbox()">
            <i class="fas fa-times"></i>
        </button>
        
        <button class="lightbox-nav lightbox-prev" onclick="prevImage()" id="lightbox-prev">
            <i class="fas fa-chevron-left"></i>
        </button>
        
        <img id="lightbox-image" src="" class="lightbox-image">
        
        <button class="lightbox-nav lightbox-next" onclick="nextImage()" id="lightbox-next">
            <i class="fas fa-chevron-right"></i>
        </button>
        
        <div class="lightbox-caption" id="lightbox-caption"></div>
        <div class="lightbox-counter" id="lightbox-counter"></div>
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

.lightbox-caption {
    position: absolute;
    bottom: -35px;
    left: 0;
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
}

.lightbox-counter {
    position: absolute;
    bottom: -35px;
    right: 0;
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

<form id="delete-album-form" action="{{ route('album.destroy', $album->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
let currentImageIndex = 0;
let imagesArray = [];

function initLightbox() {
    imagesArray = [];
    document.querySelectorAll('.album-photo-item').forEach((item, index) => {
        imagesArray.push({
            url: item.dataset.url,
            title: item.dataset.title
        });
    });
}

function openLightbox(url) {
    initLightbox();
    currentImageIndex = imagesArray.findIndex(img => img.url === url);
    updateLightbox();
    
    const overlay = document.getElementById('album-lightbox-overlay');
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const overlay = document.getElementById('album-lightbox-overlay');
    overlay.style.display = 'none';
    document.body.style.overflow = '';
}

function updateLightbox() {
    const image = imagesArray[currentImageIndex];
    const prevBtn = document.getElementById('lightbox-prev');
    const nextBtn = document.getElementById('lightbox-next');
    
    document.getElementById('lightbox-image').src = image.url;
    document.getElementById('lightbox-caption').textContent = image.title || '';
    document.getElementById('lightbox-counter').textContent = `${currentImageIndex + 1} / ${imagesArray.length}`;
    
    prevBtn.style.display = currentImageIndex === 0 ? 'none' : 'flex';
    nextBtn.style.display = currentImageIndex === imagesArray.length - 1 ? 'none' : 'flex';
}

function prevImage() {
    if (currentImageIndex > 0) {
        currentImageIndex--;
        updateLightbox();
    }
}

function nextImage() {
    if (currentImageIndex < imagesArray.length - 1) {
        currentImageIndex++;
        updateLightbox();
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
                    prevImage();
                } else if (e.key === 'ArrowRight') {
                    nextImage();
                }
            }
        });
    }
});

function purchaseAlbum() {
    const btn = document.getElementById('purchase-btn');
    btn.disabled = true;
    btn.innerHTML = '处理中...';
    
    fetch('{{ route('album.purchase', $album->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            document.getElementById('purchase-error').textContent = data.message;
            btn.disabled = false;
            btn.innerHTML = `立即支付 ${data.price || {{ $album->price }}} 金币`;
        }
    })
    .catch(() => {
        document.getElementById('purchase-error').textContent = '购买失败，请重试';
        btn.disabled = false;
        btn.innerHTML = `立即支付 {{ $album->price }} 金币`;
    });
}

function deletePhoto(photoId) {
    if (!confirm('确定要删除这张图片吗？')) return;
    
    fetch('/album/photo/' + photoId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('删除失败');
        }
    });
}

function deleteAlbum() {
    if (!confirm('确定要删除这个相册吗？所有图片将被删除。')) return;
    document.getElementById('delete-album-form').submit();
}

document.getElementById('upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const errorElement = document.getElementById('upload-error');
    const progressContainer = document.getElementById('upload-progress');
    const progressBar = progressContainer.querySelector('.progress-bar');
    const progressText = document.getElementById('progress-text');
    const uploadBtn = document.getElementById('upload-btn');
    
    errorElement.textContent = '';
    progressContainer.style.display = 'block';
    progressBar.style.width = '0%';
    progressText.textContent = '0%';
    uploadBtn.disabled = true;
    uploadBtn.textContent = '上传中...';
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route('album.upload', $album->id) }}');
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = percent + '%';
        }
    });
    
    xhr.addEventListener('load', function() {
        try {
            const data = JSON.parse(xhr.responseText);
            if (data.success) {
                location.reload();
            } else {
                errorElement.textContent = data.message;
            }
        } catch {
            errorElement.textContent = '上传失败，请重试';
        }
        uploadBtn.disabled = false;
        uploadBtn.textContent = '上传图片';
    });
    
    xhr.addEventListener('error', function() {
        errorElement.textContent = '上传失败，请重试';
        uploadBtn.disabled = false;
        uploadBtn.textContent = '上传图片';
    });
    
    xhr.send(formData);
});

document.getElementById('batch-upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const errorElement = document.getElementById('batch-upload-error');
    const progressContainer = document.getElementById('batch-upload-progress');
    const progressBar = progressContainer.querySelector('.progress-bar');
    const progressText = document.getElementById('batch-progress-text');
    const statusText = document.getElementById('upload-status');
    const uploadBtn = document.getElementById('batch-upload-btn');
    const resultsContainer = document.getElementById('batch-upload-results');
    const successFiles = document.getElementById('success-files');
    const failedFiles = document.getElementById('failed-files');
    
    errorElement.textContent = '';
    resultsContainer.style.display = 'none';
    successFiles.innerHTML = '';
    failedFiles.innerHTML = '';
    progressContainer.style.display = 'block';
    progressBar.style.width = '0%';
    progressText.textContent = '0%';
    uploadBtn.disabled = true;
    uploadBtn.textContent = '上传中...';
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route('album.upload.multiple', $album->id) }}');
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = percent + '%';
            statusText.textContent = `正在上传... ${percent}%`;
        }
    });
    
    xhr.addEventListener('load', function() {
        try {
            const data = JSON.parse(xhr.responseText);
            if (data.success) {
                resultsContainer.style.display = 'block';
                if (data.results.success.length > 0) {
                    successFiles.innerHTML = `<p>成功: ${data.results.success.join(', ')}</p>`;
                }
                if (data.results.failed.length > 0) {
                    const failedList = data.results.failed.map(item => `${item.name}: ${item.error}`);
                    failedFiles.innerHTML = `<p>失败: ${failedList.join(', ')}</p>`;
                }
                if (data.results.success.length > 0) {
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
            } else {
                errorElement.textContent = data.message || '上传失败';
            }
        } catch {
            errorElement.textContent = '上传失败，请重试';
        }
        uploadBtn.disabled = false;
        uploadBtn.textContent = '批量上传';
        progressContainer.style.display = 'none';
    });
    
    xhr.addEventListener('error', function() {
        errorElement.textContent = '上传失败，请重试';
        uploadBtn.disabled = false;
        uploadBtn.textContent = '批量上传';
        progressContainer.style.display = 'none';
    });
    
    xhr.send(formData);
});
</script>
@endsection
