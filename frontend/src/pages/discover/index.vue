<template>
  <view class="discover-page">
    <view class="header">
      <view class="header-left">
        <text class="page-title">发现</text>
      </view>
      <view class="header-right">
        <view class="likes-info">
          <text class="likes-icon">❤️</text>
          <text class="likes-count">{{ discoverStore.dailyLikesLeft }}</text>
        </view>
      </view>
    </view>
    
    <view class="cards-container" v-if="discoverStore.hasMoreUsers()">
      <swiper 
        class="cards-swiper"
        :current="currentCardIndex"
        @change="onSwiperChange"
        circular
      >
        <swiper-item v-for="(user, index) in visibleUsers" :key="user.id">
          <view 
            class="user-card"
            @touchstart="onTouchStart"
            @touchmove="onTouchMove"
            @touchend="onTouchEnd"
            :style="cardStyle"
          >
            <image 
              class="card-bg" 
              :src="user.photos[0]?.url || 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=beautiful%20woman%20portrait%20dating%20app&image_size=portrait_16_9'" 
              mode="aspectFill" 
            />
            <view class="card-overlay"></view>
            
            <view class="card-info">
              <view class="user-header">
                <view class="avatar-wrapper">
                  <image 
                    class="user-avatar" 
                    :src="user.avatar || 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20woman%20cute&image_size=square'" 
                    mode="aspectFill" 
                  />
                  <view class="vip-badge" v-if="user.is_vip">👑</view>
                </view>
                <view class="user-basic">
                  <text class="user-name">{{ user.nickname }}</text>
                  <text class="user-age">{{ calculateAge(user.birthday) }}</text>
                </view>
              </view>
              
              <view class="user-details" v-if="user.location">
                <text class="detail-icon">📍</text>
                <text class="detail-text">{{ user.location }}</text>
              </view>
              
              <view class="user-details" v-if="user.height">
                <text class="detail-icon">📏</text>
                <text class="detail-text">{{ user.height }}cm</text>
              </view>
              
              <view class="user-details" v-if="user.love_declaration">
                <text class="detail-text">{{ user.love_declaration }}</text>
              </view>
              
              <view class="user-hobbies" v-if="user.hobbies">
                <text 
                  class="hobby-tag" 
                  v-for="(hobby, idx) in user.hobbies.split(',')" 
                  :key="idx"
                >
                  {{ hobby }}
                </text>
              </view>
            </view>
            
            <view class="action-indicators">
              <view class="indicator dislike" :class="{ active: swipeDirection < -30 }">
                <text class="indicator-icon">✕</text>
                <text class="indicator-text">跳过</text>
              </view>
              <view class="indicator like" :class="{ active: swipeDirection > 30 }">
                <text class="indicator-icon">❤️</text>
                <text class="indicator-text">喜欢</text>
              </view>
            </view>
          </view>
        </swiper-item>
      </swiper>
    </view>
    
    <view class="empty-state" v-else>
      <text class="empty-icon">😴</text>
      <text class="empty-text">暂无更多推荐</text>
      <view class="refresh-btn" @click="refreshUsers">
        <text>刷新推荐</text>
      </view>
    </view>
    
    <view class="actions-bar">
      <view class="action-btn skip" @click="handleDislike">
        <text class="action-icon">✕</text>
        <text class="action-label">跳过</text>
      </view>
      <view class="action-btn super-like">
        <text class="action-icon">💎</text>
        <text class="action-label">超级喜欢</text>
      </view>
      <view class="action-btn like" @click="handleLike">
        <text class="action-icon">❤️</text>
        <text class="action-label">喜欢</text>
      </view>
      <view class="action-btn gift">
        <text class="action-icon">🎁</text>
        <text class="action-label">送礼</text>
      </view>
    </view>
    
    <view class="quick-filters">
      <scroll-view scroll-x class="filters-scroll">
        <view 
          class="filter-item" 
          v-for="filter in filters" 
          :key="filter.value"
          :class="{ active: currentFilter === filter.value }"
          @click="setFilter(filter.value)"
        >
          <text>{{ filter.label }}</text>
        </view>
      </scroll-view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useDiscoverStore } from '@/stores/discover'

const discoverStore = useDiscoverStore()

const currentCardIndex = ref(0)
const swipeDirection = ref(0)
const currentFilter = ref('all')

const filters = [
  { label: '全部', value: 'all' },
  { label: '附近', value: 'nearby' },
  { label: '同城', value: 'city' },
  { label: '大学生', value: 'student' },
  { label: '90后', value: '90s' },
  { label: '00后', value: '00s' }
]

const visibleUsers = computed(() => {
  return discoverStore.users.slice(0, 3)
})

const cardStyle = ref({})

function calculateAge(birthday: string) {
  if (!birthday) return ''
  const birthDate = new Date(birthday)
  const now = new Date()
  let age = now.getFullYear() - birthDate.getFullYear()
  if (now.getMonth() < birthDate.getMonth() || 
      (now.getMonth() === birthDate.getMonth() && now.getDate() < birthDate.getDate())) {
    age--
  }
  return age + '岁'
}

function onSwiperChange(e: any) {
  currentCardIndex.value = e.detail.current
}

function onTouchStart(e: any) {
}

function onTouchMove(e: any) {
  const touch = e.touches[0]
  swipeDirection.value = touch.clientX - 375
}

function onTouchEnd() {
  if (swipeDirection.value > 80) {
    handleLike()
  } else if (swipeDirection.value < -80) {
    handleDislike()
  }
  swipeDirection.value = 0
}

async function handleLike() {
  const currentUser = discoverStore.getCurrentUser()
  if (currentUser) {
    await discoverStore.like(currentUser.id)
    refreshIfNeeded()
  }
}

async function handleDislike() {
  const currentUser = discoverStore.getCurrentUser()
  if (currentUser) {
    await discoverStore.dislike(currentUser.id)
    refreshIfNeeded()
  }
}

function refreshIfNeeded() {
  if (!discoverStore.hasMoreUsers()) {
    discoverStore.fetchUsers()
  }
}

function refreshUsers() {
  discoverStore.fetchUsers()
}

function setFilter(value: string) {
  currentFilter.value = value
  discoverStore.updateFilter({
    gender: value === 'male' ? 1 : value === 'female' ? 2 : 0
  })
  discoverStore.fetchUsers()
}

onMounted(() => {
  discoverStore.fetchUsers()
})
</script>

<style lang="scss" scoped>
.discover-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 200rpx;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 60rpx 32rpx 32rpx;
  background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
}

.page-title {
  font-size: 40rpx;
  font-weight: bold;
  color: #fff;
}

.likes-info {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 30rpx;
  padding: 12rpx 24rpx;
}

.likes-icon {
  font-size: 28rpx;
  margin-right: 8rpx;
}

.likes-count {
  font-size: 28rpx;
  color: #fff;
  font-weight: bold;
}

.cards-container {
  padding: 32rpx;
}

.cards-swiper {
  height: 700rpx;
}

.user-card {
  background: #fff;
  border-radius: 24rpx;
  overflow: hidden;
  position: relative;
  height: 100%;
  box-shadow: 0 8rpx 32rpx rgba(0, 0, 0, 0.1);
}

.card-bg {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
}

.card-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(transparent 40%, rgba(0, 0, 0, 0.7));
}

.card-info {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 32rpx;
}

.user-header {
  display: flex;
  align-items: center;
  margin-bottom: 20rpx;
}

.avatar-wrapper {
  position: relative;
  margin-right: 20rpx;
}

.user-avatar {
  width: 100rpx;
  height: 100rpx;
  border-radius: 50%;
  border: 4rpx solid #fff;
}

.vip-badge {
  position: absolute;
  bottom: -4rpx;
  right: -4rpx;
  font-size: 24rpx;
  background: #ffd700;
  border-radius: 50%;
  padding: 4rpx;
}

.user-basic {
  display: flex;
  align-items: baseline;
}

.user-name {
  font-size: 36rpx;
  font-weight: bold;
  color: #fff;
  margin-right: 12rpx;
}

.user-age {
  font-size: 28rpx;
  color: rgba(255, 255, 255, 0.8);
}

.user-details {
  display: flex;
  align-items: center;
  margin-bottom: 12rpx;
}

.detail-icon {
  font-size: 24rpx;
  margin-right: 8rpx;
}

.detail-text {
  font-size: 26rpx;
  color: rgba(255, 255, 255, 0.9);
}

.user-hobbies {
  display: flex;
  flex-wrap: wrap;
  gap: 12rpx;
  margin-top: 16rpx;
}

.hobby-tag {
  font-size: 22rpx;
  color: rgba(255, 255, 255, 0.9);
  background: rgba(255, 255, 255, 0.2);
  padding: 6rpx 16rpx;
  border-radius: 20rpx;
}

.action-indicators {
  position: absolute;
  top: 100rpx;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between;
  padding: 0 40rpx;
  pointer-events: none;
}

.indicator {
  width: 120rpx;
  height: 120rpx;
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s;
  
  &.active {
    opacity: 1;
  }
  
  &.dislike {
    border: 4rpx solid #ff4d4f;
    background: rgba(255, 77, 79, 0.3);
  }
  
  &.like {
    border: 4rpx solid #52c41a;
    background: rgba(82, 196, 26, 0.3);
  }
}

.indicator-icon {
  font-size: 40rpx;
}

.indicator-text {
  font-size: 20rpx;
  color: #fff;
  margin-top: 4rpx;
}

.actions-bar {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 40rpx;
  padding: 32rpx;
  background: #fff;
  box-shadow: 0 -4rpx 20rpx rgba(0, 0, 0, 0.05);
  position: fixed;
  bottom: 180rpx;
  left: 0;
  right: 0;
}

.action-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  
  &.skip .action-icon {
    width: 100rpx;
    height: 100rpx;
    background: #eee;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  &.like .action-icon {
    width: 120rpx;
    height: 120rpx;
    background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8rpx 24rpx rgba(248, 124, 124, 0.4);
  }
  
  &.super-like .action-icon {
    width: 90rpx;
    height: 90rpx;
    background: linear-gradient(135deg, #67c23a 0%, #85ce61 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  &.gift .action-icon {
    width: 90rpx;
    height: 90rpx;
    background: linear-gradient(135deg, #e6a23c 0%, #f0c78a 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
}

.action-icon {
  font-size: 44rpx;
}

.action-label {
  font-size: 22rpx;
  color: #666;
  margin-top: 8rpx;
}

.quick-filters {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #fff;
  padding-bottom: constant(safe-area-inset-bottom);
  padding-bottom: env(safe-area-inset-bottom);
}

.filters-scroll {
  white-space: nowrap;
  padding: 20rpx 32rpx;
}

.filter-item {
  display: inline-block;
  padding: 16rpx 32rpx;
  background: #f5f5f5;
  border-radius: 30rpx;
  font-size: 26rpx;
  color: #666;
  margin-right: 16rpx;
  
  &.active {
    background: #f87c7c;
    color: #fff;
  }
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 100rpx 32rpx;
}

.empty-icon {
  font-size: 100rpx;
  margin-bottom: 32rpx;
}

.empty-text {
  font-size: 32rpx;
  color: #999;
  margin-bottom: 40rpx;
}

.refresh-btn {
  padding: 20rpx 60rpx;
  background: #f87c7c;
  border-radius: 40rpx;
  
  text {
    color: #fff;
    font-size: 28rpx;
  }
}
</style>
