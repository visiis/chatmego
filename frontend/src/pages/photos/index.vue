<template>
  <view class="page-container">
    <view class="status-bar"></view>
    
    <view class="nav-bar">
      <view class="nav-left" @click="goBack">
        <text class="nav-icon">‹</text>
      </view>
      <view class="nav-center">
        <text class="nav-title">{{ userName }}的照片</text>
      </view>
      <view class="nav-right"></view>
    </view>
    
    <scroll-view scroll-y class="content" :show-scrollbar="false">
      <view v-if="loading" class="loading-state">
        <text class="fa fas fa-spinner fa-spin" style="font-size: 48px; color: #ff6b9d;"></text>
        <text>加载中...</text>
      </view>
      
      <view v-else-if="photos.length === 0" class="empty-state">
        <text class="fa fas fa-images" style="font-size: 80px; color: #ccc;"></text>
        <text class="empty-title">暂无照片</text>
        <text class="empty-desc">{{ userName }}还没有上传任何照片</text>
      </view>
      
      <view v-else class="photos-grid">
        <image 
          class="photo-item" 
          v-for="(photo, idx) in photos" 
          :key="idx" 
          :src="photo.url || photo" 
          mode="aspectFill"
          @click="previewImage(photo.url || photo)"
        />
      </view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const userId = ref(0)
const userName = ref('')
const userAvatar = ref('')
const photos = ref<any[]>([])
const loading = ref(true)

onMounted(() => {
  initPage()
})

function initPage() {
  const pages = getCurrentPages()
  const currentPage = pages[pages.length - 1]
  const options = (currentPage as any).$page?.options || (currentPage as any).options || {}
  
  userId.value = parseInt(options.id || '0')
  userName.value = decodeURIComponent(options.name || options.nickname || '用户')
  userAvatar.value = decodeURIComponent(options.avatar || '') || ''
  
  loadPhotos()
}

async function loadPhotos() {
  try {
    const token = uni.getStorageSync('token')
    if (!token) {
      loading.value = false
      return
    }
    
    const response = await fetch(`https://chatmego.com/api/album/user/${userId.value}/photos`, {
      headers: {
        'Authorization': 'Bearer ' + token
      }
    })
    
    const data = await response.json()
    const photoList = Array.isArray(data) ? data : (data?.data || data?.photos || [])
    
    photos.value = photoList.map((item: any) => ({
      url: item.image_url || item.url || item.photo_url || item
    })).filter((item: any) => item.url)
  } catch (error) {
    console.error('加载照片失败:', error)
    photos.value = []
  } finally {
    loading.value = false
  }
}

function previewImage(url: string) {
  uni.previewImage({
    urls: photos.value.map((p: any) => p.url),
    current: url
  })
}

function goBack() {
  uni.navigateBack({
    fail: () => {
      uni.switchTab({ url: '/pages/discover/cards' })
    }
  })
}
</script>

<style lang="scss">
page {
  height: 100%;
  margin: 0;
  padding: 0;
  background: #f5f5f5;
}

.page-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  height: 100dvh;
}

.status-bar {
  height: var(--status-bar-height, 44px);
  flex-shrink: 0;
}

.nav-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 16rpx 24rpx;
  flex-shrink: 0;
}

.nav-left, .nav-right {
  width: 80rpx;
  height: 80rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-icon {
  font-size: 48rpx;
  color: #fff;
  font-weight: bold;
}

.nav-center {
  flex: 1;
  text-align: center;
}

.nav-title {
  font-size: 32rpx;
  color: #fff;
  font-weight: 500;
}

.content {
  flex: 1;
  padding: 24rpx;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 200rpx 0;
  gap: 20rpx;
  color: #999;
  font-size: 28rpx;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 200rpx 0;
}

.empty-title {
  font-size: 32rpx;
  color: #666;
  margin-top: 32rpx;
  font-weight: 500;
}

.empty-desc {
  font-size: 26rpx;
  color: #999;
  margin-top: 16rpx;
}

.photos-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 12rpx;
}

.photo-item {
  width: calc(33.33% - 8rpx);
  padding-bottom: calc(33.33% - 8rpx);
  border-radius: 12rpx;
  background: #eee;
  position: relative;
  
  image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
}
</style>