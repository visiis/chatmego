@extends('layouts.app')

@section('content')
<div class="cards-page">
    <div class="cards-header">
        <h2 class="cards-title">💕 发现有趣的人</h2>
        <p class="cards-subtitle">左右滑动切换用户 · 上下滑动浏览相册</p>
    </div>

    <div class="cards-container" id="cards-container">
        <div class="cards-stack" id="cards-stack">
            @foreach($users as $index => $user)
            <div class="card-item" data-user-id="{{ $user->id }}" style="z-index: {{ count($users) - $index }};">
                <div class="card-content">
                    <div class="card-photo-container">
                        <img 
                            class="card-photo" 
                            src="" 
                            data-photos='{{ json_encode($user->albums->flatMap(function($album) {
                                return $album->photos->map(function($photo) use ($album) {
                                    return [
                                        'url' => $photo->image_url,
                                        'thumbnail' => $photo->thumbnail_url,
                                        'title' => $photo->title,
                                        'album_privacy' => $album->privacy,
                                        'album_price' => $album->price,
                                        'album_id' => $album->id
                                    ];
                                });
                            })) }}'
                            alt="用户相册">
                        
                        <div class="card-overlay">
                            <div class="user-info">
                                <img src="{{ $user->avatar_url }}" class="user-avatar">
                                <div class="user-details">
                                    <span class="user-name">{{ $user->nickname }}</span>
                                    <span class="user-meta">{{ $user->age }}岁 · {{ $user->location }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="photo-counter">
                            <span id="photo-counter-{{ $user->id }}">1 / 1</span>
                        </div>

                        <div class="locked-badge" id="locked-badge-{{ $user->id }}" style="display: none;">
                            <i class="fas fa-lock"></i>
                            <span>付费解锁</span>
                        </div>

                        <div class="blur-overlay" id="blur-overlay-{{ $user->id }}" style="display: none;"></div>
                    </div>

                    <div class="card-actions">
                        <button class="action-btn btn-dislike" onclick="dislikeUser()">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="action-btn btn-favorite" onclick="likeUser()">
                            <i class="fas fa-heart"></i>
                        </button>
                        <button class="action-btn btn-message" onclick="openMessage()">
                            <i class="fas fa-comment"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($users->isEmpty())
        <div class="empty-state">
            <i class="fas fa-inbox fa-5x text-muted"></i>
            <p class="mt-4">暂无用户</p>
        </div>
        @endif
    </div>

    <div class="cards-hint">
        <span>← 左滑跳过</span>
        <span>↑↓ 浏览相册</span>
        <span>右滑喜欢 →</span>
    </div>
</div>

<!-- 付费解锁弹窗 -->
<div class="purchase-modal" id="purchase-modal" style="display: none;">
    <div class="purchase-content">
        <button class="purchase-close" onclick="closePurchaseModal()">
            <i class="fas fa-times"></i>
        </button>
        <h3>🔒 该相册为付费内容</h3>
        <p class="purchase-desc">解锁后可查看完整相册内容</p>
        <div class="purchase-price">
            <span class="price-amount">0</span>
            <span class="price-unit">金币</span>
        </div>
        <p class="purchase-note">* 有效期30天，50%金币将返还给相册所有者</p>
        <button class="purchase-btn" onclick="purchaseAlbum()">立即解锁</button>
        <p class="purchase-error" id="purchase-error"></p>
    </div>
</div>

<style>
.cards-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    position: relative;
}

.cards-header {
    text-align: center;
    color: white;
    margin-bottom: 20px;
}

.cards-title {
    font-size: 28px;
    font-weight: 600;
    margin: 0;
}

.cards-subtitle {
    font-size: 14px;
    opacity: 0.8;
    margin-top: 5px;
}

.cards-container {
    max-width: 420px;
    margin: 0 auto;
    position: relative;
    min-height: 500px;
}

.cards-stack {
    position: relative;
    height: 600px;
}

.card-item {
    position: absolute;
    width: 100%;
    height: 550px;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, opacity 0.3s ease, box-shadow 0.3s ease;
    cursor: grab;
}

.card-item:active {
    cursor: grabbing;
}

.card-item.swipe-left {
    transform: translateX(-120%) rotate(-15deg);
    opacity: 0;
}

.card-item.swipe-right {
    transform: translateX(120%) rotate(15deg);
    opacity: 0;
}

.card-item.swipe-up {
    transform: translateY(-30px);
}

.card-item.swipe-down {
    transform: translateY(30px);
}

.card-content {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.card-photo-container {
    flex: 1;
    position: relative;
    overflow: hidden;
}

.card-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px;
    background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 3px solid white;
    object-fit: cover;
}

.user-details {
    color: white;
}

.user-name {
    display: block;
    font-size: 20px;
    font-weight: 600;
}

.user-meta {
    display: block;
    font-size: 14px;
    opacity: 0.8;
}

.photo-counter {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(0,0,0,0.5);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
}

.locked-badge {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 15px 25px;
    border-radius: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    z-index: 10;
}

.blur-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    backdrop-filter: blur(15px);
    background: rgba(0,0,0,0.3);
    z-index: 5;
}

.card-actions {
    display: flex;
    justify-content: center;
    gap: 30px;
    padding: 15px;
    background: white;
}

.action-btn {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.action-btn:hover {
    transform: scale(1.1);
}

.btn-dislike {
    background: #ff6b6b;
    color: white;
}

.btn-favorite {
    background: linear-gradient(135deg, #ff6b6b, #ee5a9b);
    color: white;
}

.btn-message {
    background: #4ecdc4;
    color: white;
}

.cards-hint {
    position: fixed;
    bottom: 30px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    gap: 40px;
    color: rgba(255,255,255,0.7);
    font-size: 13px;
}

.empty-state {
    text-align: center;
    padding-top: 100px;
}

.purchase-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.purchase-content {
    background: white;
    border-radius: 20px;
    padding: 40px;
    max-width: 350px;
    width: 90%;
    text-align: center;
    position: relative;
}

.purchase-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #999;
}

.purchase-content h3 {
    margin: 0 0 10px 0;
    font-size: 20px;
}

.purchase-desc {
    color: #666;
    margin-bottom: 20px;
}

.purchase-price {
    display: flex;
    justify-content: center;
    align-items: baseline;
    gap: 5px;
    margin-bottom: 10px;
}

.price-amount {
    font-size: 48px;
    font-weight: bold;
    color: #ff6b6b;
}

.price-unit {
    font-size: 18px;
    color: #666;
}

.purchase-note {
    font-size: 12px;
    color: #999;
    margin-bottom: 20px;
}

.purchase-btn {
    background: linear-gradient(135deg, #ff6b6b, #ee5a9b);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 30px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
}

.purchase-btn:hover {
    transform: scale(1.02);
}

.purchase-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.purchase-error {
    color: #ff6b6b;
    font-size: 12px;
    margin-top: 15px;
    height: 18px;
}

@media (max-width: 768px) {
    .cards-stack {
        height: 550px;
    }
    
    .card-item {
        height: 500px;
    }
    
    .cards-hint {
        gap: 20px;
        font-size: 11px;
    }
}
</style>

<script>
let currentCardIndex = 0;
let currentPhotoIndex = 0;
let photosArray = [];
let currentAlbumId = null;
let currentAlbumPrice = null;
let isDragging = false;
let startX = 0;
let startY = 0;
let currentX = 0;
let currentY = 0;
let cards = [];

document.addEventListener('DOMContentLoaded', function() {
    cards = document.querySelectorAll('.card-item');
    if (cards.length > 0) {
        initCurrentCard();
        setupDragEvents();
        setupKeyboardEvents();
    }
});

function initCurrentCard() {
    if (cards.length === 0) return;
    
    const currentCard = cards[currentCardIndex];
    const photoData = currentCard.querySelector('.card-photo').dataset.photos;
    photosArray = JSON.parse(photoData);
    
    if (photosArray.length > 0) {
        loadCurrentPhoto();
    }
    
    updateCounter();
}

function loadCurrentPhoto() {
    if (photosArray.length === 0) return;
    
    const currentCard = cards[currentCardIndex];
    const photo = photosArray[currentPhotoIndex];
    const img = currentCard.querySelector('.card-photo');
    const blurOverlay = currentCard.querySelector('.blur-overlay');
    const lockedBadge = currentCard.querySelector('.locked-badge');
    
    img.src = photo.url;
    
    if (photo.album_privacy === false) {
        blurOverlay.style.display = 'block';
        lockedBadge.style.display = 'flex';
        currentAlbumId = photo.album_id;
        currentAlbumPrice = photo.album_price;
    } else {
        blurOverlay.style.display = 'none';
        lockedBadge.style.display = 'none';
        currentAlbumId = null;
        currentAlbumPrice = null;
    }
}

function updateCounter() {
    if (cards.length === 0) return;
    
    const currentCard = cards[currentCardIndex];
    const counter = currentCard.querySelector('.photo-counter');
    counter.textContent = `${currentPhotoIndex + 1} / ${photosArray.length}`;
}

function nextPhoto() {
    if (photosArray.length === 0) return;
    
    if (photosArray[currentPhotoIndex].album_privacy === false) {
        openPurchaseModal();
        return;
    }
    
    if (currentPhotoIndex < photosArray.length - 1) {
        currentPhotoIndex++;
        loadCurrentPhoto();
        updateCounter();
    }
}

function prevPhoto() {
    if (photosArray.length === 0) return;
    
    if (photosArray[currentPhotoIndex].album_privacy === false) {
        openPurchaseModal();
        return;
    }
    
    if (currentPhotoIndex > 0) {
        currentPhotoIndex--;
        loadCurrentPhoto();
        updateCounter();
    }
}

function nextUser() {
    if (currentCardIndex < cards.length - 1) {
        cards[currentCardIndex].classList.add('swipe-right');
        setTimeout(() => {
            cards[currentCardIndex].style.display = 'none';
            currentCardIndex++;
            currentPhotoIndex = 0;
            initCurrentCard();
        }, 300);
    } else {
        showNoMoreCards();
    }
}

function prevUser() {
    if (currentCardIndex > 0) {
        cards[currentCardIndex].classList.add('swipe-left');
        setTimeout(() => {
            cards[currentCardIndex].style.display = 'none';
            currentCardIndex--;
            currentPhotoIndex = 0;
            initCurrentCard();
        }, 300);
    }
}

function showNoMoreCards() {
    const container = document.getElementById('cards-container');
    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-heart fa-5x text-white"></i>
            <p class="mt-4 text-white">已经看完所有卡片啦</p>
            <button class="btn btn-light mt-3" onclick="location.reload()">再看一遍</button>
        </div>
    `;
}

function setupDragEvents() {
    const container = document.getElementById('cards-stack');
    
    container.addEventListener('mousedown', handleMouseDown);
    container.addEventListener('mousemove', handleMouseMove);
    container.addEventListener('mouseup', handleMouseUp);
    container.addEventListener('mouseleave', handleMouseUp);
    
    container.addEventListener('touchstart', handleTouchStart);
    container.addEventListener('touchmove', handleTouchMove);
    container.addEventListener('touchend', handleTouchEnd);
}

function handleMouseDown(e) {
    if (currentCardIndex >= cards.length) return;
    isDragging = true;
    startX = e.clientX;
    startY = e.clientY;
}

function handleMouseMove(e) {
    if (!isDragging || currentCardIndex >= cards.length) return;
    
    currentX = e.clientX - startX;
    currentY = e.clientY - startY;
    
    const card = cards[currentCardIndex];
    const rotate = currentX * 0.05;
    card.style.transform = `translate(${currentX}px, ${currentY}px) rotate(${rotate}deg)`;
    
    if (currentX > 50) {
        card.style.borderColor = '#4ecdc4';
    } else if (currentX < -50) {
        card.style.borderColor = '#ff6b6b';
    } else {
        card.style.borderColor = 'transparent';
    }
}

function handleMouseUp() {
    if (!isDragging || currentCardIndex >= cards.length) {
        isDragging = false;
        return;
    }
    
    isDragging = false;
    const card = cards[currentCardIndex];
    card.style.transform = 'translate(0, 0) rotate(0deg)';
    card.style.borderColor = 'transparent';
    
    const threshold = 100;
    const photoThreshold = 60;
    
    if (Math.abs(currentX) > threshold) {
        if (currentX > 0) {
            nextUser();
        } else {
            prevUser();
        }
    } else if (Math.abs(currentY) > photoThreshold) {
        if (currentY < 0) {
            nextPhoto();
        } else {
            prevPhoto();
        }
    }
    
    currentX = 0;
    currentY = 0;
}

function handleTouchStart(e) {
    if (currentCardIndex >= cards.length) return;
    isDragging = true;
    const touch = e.touches[0];
    startX = touch.clientX;
    startY = touch.clientY;
}

function handleTouchMove(e) {
    if (!isDragging || currentCardIndex >= cards.length) return;
    
    const touch = e.touches[0];
    currentX = touch.clientX - startX;
    currentY = touch.clientY - startY;
    
    const card = cards[currentCardIndex];
    const rotate = currentX * 0.05;
    card.style.transform = `translate(${currentX}px, ${currentY}px) rotate(${rotate}deg)`;
}

function handleTouchEnd() {
    handleMouseUp();
}

function setupKeyboardEvents() {
    document.addEventListener('keydown', function(e) {
        switch(e.key) {
            case 'ArrowLeft':
                e.preventDefault();
                prevUser();
                break;
            case 'ArrowRight':
                e.preventDefault();
                nextUser();
                break;
            case 'ArrowUp':
                e.preventDefault();
                nextPhoto();
                break;
            case 'ArrowDown':
                e.preventDefault();
                prevPhoto();
                break;
            case 'Escape':
                closePurchaseModal();
                break;
        }
    });
}

function likeUser() {
    nextUser();
}

function dislikeUser() {
    prevUser();
}

function openMessage() {
    if (cards.length === 0) return;
    const userId = cards[currentCardIndex].dataset.userId;
    window.location.href = `/chat/${userId}`;
}

function openPurchaseModal() {
    const modal = document.getElementById('purchase-modal');
    const priceDisplay = modal.querySelector('.price-amount');
    
    if (currentAlbumPrice) {
        priceDisplay.textContent = currentAlbumPrice;
    }
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePurchaseModal() {
    const modal = document.getElementById('purchase-modal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
    document.getElementById('purchase-error').textContent = '';
}

function purchaseAlbum() {
    if (!currentAlbumId) return;
    
    const btn = document.querySelector('.purchase-btn');
    btn.disabled = true;
    btn.textContent = '处理中...';
    document.getElementById('purchase-error').textContent = '';
    
    fetch(`/album/${currentAlbumId}/purchase`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closePurchaseModal();
            loadCurrentPhoto();
        } else {
            document.getElementById('purchase-error').textContent = data.message;
        }
        btn.disabled = false;
        btn.textContent = '立即解锁';
    })
    .catch(() => {
        document.getElementById('purchase-error').textContent = '购买失败，请重试';
        btn.disabled = false;
        btn.textContent = '立即解锁';
    });
}
</script>
@endsection
