@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 bg-light border-right">
            <div class="p-4">
                <div class="text-center mb-4">
                    <img src="{{ avatar_url($user->avatar) }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle img-fluid mb-3" 
                         style="width: 120px; height: 120px; object-fit: cover;">
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted text-sm">{{ $user->signature }}</p>
                </div>
                
                <div class="row mb-4">
                    <div class="col-4 text-center">
                        <div class="font-weight-bold">{{ $albums->count() }}</div>
                        <div class="text-xs text-muted">相册</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="font-weight-bold">{{ $statuses->count() }}</div>
                        <div class="text-xs text-muted">说说</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="font-weight-bold">{{ $user->coins }}</div>
                        <div class="text-xs text-muted">金币</div>
                    </div>
                </div>

                <nav class="nav flex-column">
                    <a href="#" class="nav-link active profile-nav-item" data-section="profile">
                        <i class="fas fa-user"></i> 个人资料
                    </a>
                    <a href="#" class="nav-link profile-nav-item" data-section="statuses">
                        <i class="fas fa-message-circle"></i> 我的说说
                    </a>
                    <a href="#" class="nav-link profile-nav-item" data-section="albums">
                        <i class="fas fa-images"></i> 我的相册
                    </a>
                    <a href="#" class="nav-link profile-nav-item" data-section="edit">
                        <i class="fas fa-edit"></i> 编辑资料
                    </a>
                </nav>
            </div>
        </div>

        <div class="col-md-9">
            <div id="profile-content">
                @include('profile.sections.profile')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.profile-nav-item');
    const contentArea = document.getElementById('profile-content');
    
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            navItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            
            const section = this.dataset.section;
            loadSection(section);
        });
    });
    
    function loadSection(section) {
        contentArea.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin"></i> 加载中...</div>';
        
        fetch('/profile/{{ $user->id }}/section/' + section)
        .then(res => res.text())
        .then(html => {
            contentArea.innerHTML = html;
            initSectionScripts(section);
        })
        .catch(err => {
            console.error('Load section error:', err);
            contentArea.innerHTML = '<div class="text-center py-8"><i class="fas fa-exclamation-triangle text-danger"></i> 加载失败</div>';
        });
    }
    
    function initSectionScripts(section) {
        if (section === 'albums') {
            initAlbumSection();
        } else if (section === 'statuses') {
            initStatusSection();
        } else if (section === 'edit') {
            initEditSection();
        }
    }
    
    function initAlbumSection() {
        document.querySelectorAll('.album-card').forEach(card => {
            card.addEventListener('click', function() {
                const albumId = this.dataset.albumId;
                showAlbumDetail(albumId);
            });
        });
        
        document.querySelectorAll('.edit-album-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const albumId = this.dataset.albumId;
                showEditAlbumModal(albumId);
            });
        });
        
        document.querySelectorAll('.delete-album-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const albumId = this.dataset.albumId;
                deleteAlbum(albumId);
            });
        });
    }
    
    function initStatusSection() {
        document.querySelectorAll('.like-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const statusId = this.dataset.statusId;
                likeStatus(statusId);
            });
        });
        
        document.querySelectorAll('.comment-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const statusId = this.dataset.statusId;
                toggleComments(statusId);
            });
        });
        
        document.querySelectorAll('.delete-status-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const statusId = this.dataset.statusId;
                deleteStatus(statusId);
            });
        });
    }
    
    function initEditSection() {
        const form = document.getElementById('edit-profile-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                submitProfileEdit();
            });
        }
    }
});
</script>
@endpush