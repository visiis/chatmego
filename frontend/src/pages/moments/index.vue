<template>
  <view class="page-container">
    <view class="status-bar"></view>
    
    <view class="nav-bar">
      <view class="nav-left" @click="goBack">
        <text class="nav-icon">‹</text>
      </view>
      <view class="nav-center">
        <text class="nav-title">{{ userName }}的说说</text>
      </view>
      <view class="nav-right"></view>
    </view>
    
    <scroll-view scroll-y class="content" :show-scrollbar="false">
      <view v-if="loading" class="loading-state">
        <text class="fa fas fa-spinner fa-spin" style="font-size: 48px; color: #ff6b9d;"></text>
        <text>加载中...</text>
      </view>
      
      <view v-else-if="statuses.length === 0" class="empty-state">
        <text class="fa fas fa-comment" style="font-size: 80px; color: #ccc;"></text>
        <text class="empty-title">暂无说说</text>
        <text class="empty-desc">{{ userName }}还没有发表任何说说</text>
      </view>
      
      <view v-else class="status-list">
        <view class="status-item" v-for="status in statuses" :key="status.id">
          <image class="status-avatar" :src="userAvatar" mode="aspectFill" />
          <view class="status-content">
            <view class="status-header">
              <text class="status-name">{{ userName }}</text>
              <text class="status-time">{{ formatTime(status.created_at) }}</text>
            </view>
            <text class="status-text">{{ status.content }}</text>
            <view class="status-images" v-if="status.images && status.images.length > 0">
              <image 
                class="status-image" 
                v-for="(img, idx) in status.images" 
                :key="idx" 
                :src="img" 
                mode="widthFix"
                @click="previewImage(img, status.images)"
              />
            </view>
            <view class="status-footer">
              <view class="status-action">
                <text class="fa fas fa-heart" style="font-size: 24px; color: #999;"></text>
                <text class="action-text">{{ status.likes_count || 0 }}</text>
              </view>
              <view class="status-action">
                <text class="fa fas fa-comment" style="font-size: 24px; color: #999;"></text>
                <text class="action-text">{{ status.comments_count || 0 }}</text>
              </view>
            </view>
          </view>
        </view>
      </view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const userId = ref(0)
const userName = ref('')
const userAvatar = ref('')
const statuses = ref<any[]>([])
const loading = ref(true)

const DEFAULT_AVATAR = 'https://chatmego.com/images/default-avatar.png'

onMounted(() => {
  initPage()
})

function initPage() {
  const pages = getCurrentPages()
  const currentPage = pages[pages.length - 1]
  const options = (currentPage as any).$page?.options || (currentPage as any).options || {}
  
  userId.value = parseInt(options.id || '0')
  userName.value = decodeURIComponent(options.name || options.nickname || '用户')
  userAvatar.value = decodeURIComponent(options.avatar || '') || DEFAULT_AVATAR
  
  loadStatuses()
}

async function loadStatuses() {
  try {
    const token = uni.getStorageSync('token')
    if (!token) {
      loading.value = false
      return
    }
    
    const response = await fetch(`https://chatmego.com/api/status/user/${userId.value}`, {
      headers: {
        'Authorization': 'Bearer ' + token
      }
    })
    
    const data = await response.json()
    statuses.value = Array.isArray(data) ? data : (data?.data || data?.statuses || [])
  } catch (error) {
    console.error('加载说说失败:', error)
    statuses.value = []
  } finally {
    loading.value = false
  }
}

function formatTime(dateStr: string): string {
  const date = new Date(dateStr)
  const now = new Date()
  const diff = now.getTime() - date.getTime()
  
  if (diff < 60000) return '刚刚'
  if (diff < 3600000) return Math.floor(diff / 60000) + '分钟前'
  if (diff < 86400000) return Math.floor(diff / 3600000) + '小时前'
  if (diff < 604800000) return Math.floor(diff / 86400000) + '天前'
  
  const month = date.getMonth() + 1
  const day = date.getDate()
  return `${month}月${day}日`
}

function previewImage(current: string, urls: string[]) {
  uni.previewImage({
    urls,
    current
  })
}

function goBack() {
  uni.navigateBack({
    fail: () => {
      uni.switchTab({ url: '/pages/statuses/index' })
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

.status-list {
  display: flex;
  flex-direction: column;
  gap: 24rpx;
}

.status-item {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
  display: flex;
  gap: 20rpx;
}

.status-avatar {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
  flex-shrink: 0;
}

.status-content {
  flex: 1;
}

.status-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16rpx;
}

.status-name {
  font-size: 28rpx;
  color: #333;
  font-weight: 500;
}

.status-time {
  font-size: 22rpx;
  color: #999;
}

.status-text {
  font-size: 28rpx;
  color: #333;
  line-height: 1.6;
  word-break: break-all;
}

.status-images {
  display: flex;
  flex-wrap: wrap;
  gap: 12rpx;
  margin-top: 16rpx;
}

.status-image {
  width: calc(33.33% - 8rpx);
  border-radius: 12rpx;
}

.status-footer {
  display: flex;
  gap: 48rpx;
  margin-top: 20rpx;
  padding-top: 16rpx;
  border-top: 1rpx solid #f0f0f0;
}

.status-action {
  display: flex;
  align-items: center;
  gap: 8rpx;
}

.action-text {
  font-size: 24rpx;
  color: #999;
}
</style>