<template>
  <view class="album-detail-container">
    <view class="status-bar"></view>
    
    <view class="nav-bar">
      <view class="nav-left" @click="goBack">
        <FontAwesome name="arrow-left" size="24px" color="#fff" />
      </view>
      <view class="nav-center">
        <text class="nav-title">{{ albumDetail?.name || '相册详情' }}</text>
      </view>
      <view class="nav-right"></view>
    </view>
    
    <scroll-view class="content" scroll-y>
      <view class="album-info" v-if="albumDetail">
        <view class="owner-section">
          <image v-if="albumDetail.owner.avatar" class="owner-avatar" :src="albumDetail.owner.avatar" mode="aspectFill" />
          <view class="owner-placeholder" v-else>
            <FontAwesome name="user" size="32px" color="#ccc" />
          </view>
          <view class="owner-info">
            <text class="owner-name">{{ albumDetail.owner.name }}</text>
            <view class="album-meta">
              <text class="meta-item"><FontAwesome name="eye" size="16px" color="#999" /> {{ albumDetail.view_count }}</text>
              <text class="meta-item"><FontAwesome name="shopping-cart" size="16px" color="#999" /> {{ albumDetail.purchase_count }}</text>
            </view>
          </view>
        </view>
        
        <text class="album-description" v-if="albumDetail.description">{{ albumDetail.description }}</text>
        
        <view class="purchase-section" v-if="!albumDetail.is_owner && !albumDetail.privacy && !albumDetail.can_view">
          <view class="price-badge">
            <FontAwesome name="coins" size="24px" color="#ffd700" />
            <text class="price-value">{{ albumDetail.price }}</text>
            <text class="price-label">金币</text>
          </view>
          <view class="purchase-btn" @click="handlePurchase">
            <FontAwesome name="lock" size="24px" color="#fff" />
            <text>解锁查看</text>
          </view>
        </view>
        
        <view class="purchased-badge" v-if="albumDetail.can_view && !albumDetail.is_owner && !albumDetail.privacy">
          <FontAwesome name="check-circle" size="20px" color="#27ae60" />
          <text>已购买</text>
        </view>
        
        <view class="public-badge" v-if="albumDetail.privacy">
          <FontAwesome name="globe" size="20px" color="#3498db" />
          <text>公开相册</text>
        </view>
      </view>
      
      <view class="photos-section" v-if="albumDetail && albumDetail.photos.length > 0">
        <view class="photos-grid">
          <view class="photo-item" v-for="(photo, index) in albumDetail.photos" :key="photo.id" @click="previewPhoto(index)">
            <image class="photo-img" :src="photo.image_url || photo.thumbnail_url" mode="aspectFill" />
            <view class="photo-overlay" v-if="!photo.can_view_full">
              <FontAwesome name="lock" size="48px" color="#fff" />
            </view>
          </view>
        </view>
      </view>
      
      <view class="empty-state" v-if="albumDetail && albumDetail.photos.length === 0">
        <FontAwesome name="image" size="80px" color="#ddd" />
        <text class="empty-text">相册暂无照片</text>
      </view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { getAlbum, purchaseAlbum, type AlbumDetail } from '../../api/album'

const albumDetail = ref<AlbumDetail | null>(null)
const albumId = ref(0)

onMounted(() => {
  const options = (uni as any).getLaunchOptionsSync?.() || {}
  const url = options.query?.url || ''
  const match = url.match(/albumId=(\d+)/)
  
  if (match) {
    albumId.value = parseInt(match[1])
  } else {
    const pages = getCurrentPages()
    const currentPage = pages[pages.length - 1]
    albumId.value = (currentPage as any).options?.albumId || 0
  }
  
  if (albumId.value) {
    loadAlbum()
  }
})

async function loadAlbum() {
  try {
    albumDetail.value = await getAlbum(albumId.value)
  } catch (error) {
    console.error('加载相册失败:', error)
    uni.showToast({ title: '加载失败', icon: 'none' })
  }
}

async function handlePurchase() {
  if (!albumDetail.value) return
  
  uni.showModal({
    title: '确认购买',
    content: `需要支付 ${albumDetail.value.price} 金币解锁此相册，有效期30天`,
    success: async (res) => {
      if (res.confirm) {
        try {
          await purchaseAlbum(albumId.value)
          uni.showToast({ title: '购买成功', icon: 'success' })
          loadAlbum()
        } catch (error) {
          console.error('购买失败:', error)
          const errMsg = (error as any)?.message || ''
          uni.showToast({ title: errMsg || '购买失败', icon: 'none' })
        }
      }
    }
  })
}

function previewPhoto(index: number) {
  if (!albumDetail.value) return
  
  const urls = albumDetail.value.photos.map(p => p.image_url || p.thumbnail_url)
  uni.previewImage({
    current: urls[index],
    urls: urls
  })
}

function goBack() {
  uni.navigateBack({
    fail: () => {
      uni.switchTab({ url: '/pages/profile/index' })
    }
  })
}
</script>

<style lang="scss">
page {
  height: 100%;
  margin: 0;
  padding: 0;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
}

.album-detail-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.status-bar {
  height: var(--status-bar-height, 44px);
}

.nav-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 16rpx 24rpx;
  position: relative;
  z-index: 100;
}

.nav-left, .nav-right {
  width: 80rpx;
  height: 80rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-title {
  font-size: 34rpx;
  color: #fff;
  font-weight: 600;
}

.content {
  flex: 1;
  padding: 24rpx;
}

.album-info {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
  margin-bottom: 24rpx;
}

.owner-section {
  display: flex;
  align-items: center;
  margin-bottom: 20rpx;
}

.owner-avatar {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
  background: #f0f0f0;
}

.owner-placeholder {
  width: 80rpx;
  height: 80rpx;
  background: #f0f0f0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.owner-info {
  flex: 1;
  margin-left: 16rpx;
}

.owner-name {
  display: block;
  font-size: 30rpx;
  color: #333;
  font-weight: 500;
  margin-bottom: 8rpx;
}

.album-meta {
  display: flex;
  gap: 24rpx;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8rpx;
  font-size: 24rpx;
  color: #999;
}

.album-description {
  display: block;
  font-size: 28rpx;
  color: #666;
  line-height: 1.6;
  margin-bottom: 20rpx;
}

.purchase-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20rpx;
  background: linear-gradient(135deg, #fef9c3 0%, #fde047 100%);
  border-radius: 12rpx;
}

.price-badge {
  display: flex;
  align-items: center;
  gap: 8rpx;
}

.price-value {
  font-size: 36rpx;
  color: #d97706;
  font-weight: bold;
}

.price-label {
  font-size: 24rpx;
  color: #d97706;
}

.purchase-btn {
  display: flex;
  align-items: center;
  gap: 8rpx;
  background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
  padding: 16rpx 32rpx;
  border-radius: 24rpx;
  
  text {
    font-size: 28rpx;
    color: #fff;
    font-weight: 500;
  }
}

.purchased-badge, .public-badge {
  display: flex;
  align-items: center;
  gap: 8rpx;
  padding: 12rpx 20rpx;
  background: rgba(39, 174, 96, 0.1);
  border-radius: 20rpx;
  width: fit-content;
  
  text {
    font-size: 24rpx;
    color: #27ae60;
  }
}

.public-badge {
  background: rgba(52, 152, 219, 0.1);
  
  text {
    color: #3498db;
  }
}

.photos-section {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
}

.photos-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12rpx;
}

.photo-item {
  aspect-ratio: 1;
  border-radius: 12rpx;
  overflow: hidden;
  position: relative;
}

.photo-img {
  width: 100%;
  height: 100%;
}

.photo-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-state {
  background: #fff;
  border-radius: 16rpx;
  padding: 100rpx 0;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.empty-text {
  font-size: 28rpx;
  color: #999;
  margin-top: 24rpx;
}
</style>
