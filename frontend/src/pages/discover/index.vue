<template>
  <view class="discover-page">
    <view class="header">
      <text class="page-title">發現</text>
      <view class="likes-info">
        <text class="icon-heart">♥</text>
        <text class="likes-count">{{ likesCount }}</text>
      </view>
    </view>
    
    <view class="cards-container" v-if="users.length > 0">
      <swiper 
        class="cards-swiper"
        :current="currentIndex"
        :circular="false"
        @change="onSwiperChange"
      >
        <swiper-item v-for="(user, index) in users" :key="user.id">
          <view class="user-card">
            <image 
              class="user-avatar" 
              :src="user.avatar || '/static/images/default-avatar.png'" 
              mode="aspectFill"
            />
            
            <view class="vip-badge" v-if="user.is_vip">
              <text>青铜会员</text>
            </view>
            
            <view class="card-info">
              <text class="user-name">{{ user.nickname }}</text>
              
              <view class="user-details">
                <text class="detail-item">
                  {{ user.gender === 1 ? '男' : user.gender === 2 ? '女' : '' }}
                  {{ user.age ? '· ' + user.age + '岁' : '' }}
                </text>
                <text class="detail-item" v-if="user.distance">
                  · {{ user.distance }}
                </text>
              </view>
              
              <view class="user-stats" v-if="user.height || user.weight">
                <text v-if="user.height">{{ user.height }}cm</text>
                <text v-if="user.height && user.weight">·</text>
                <text v-if="user.weight">{{ user.weight }}kg</text>
              </view>
              
              <view class="love-declaration" v-if="user.love_declaration">
                <text>{{ user.love_declaration }}</text>
              </view>
            </view>
          </view>
        </swiper-item>
      </swiper>
      
      <view class="action-buttons">
        <view class="action-btn dislike" @click="handleDislike">
          <text class="btn-icon">✕</text>
        </view>
        <view class="action-btn like" @click="handleLike">
          <text class="btn-icon">♥</text>
        </view>
      </view>
    </view>
    
    <view class="empty-state" v-else>
      <text class="empty-icon">⊛</text>
      <text class="empty-text">暂无更多推荐</text>
      <view class="refresh-btn" @click="refreshUsers">
        <text>刷新推荐</text>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { getRecommend } from '@/api/discover'

interface User {
  id: number
  nickname: string
  avatar: string
  gender: number
  age: number | null
  height: number | null | string
  weight: number | null | string
  is_vip: number
  distance: string
  love_declaration: string | null
}

const users = ref<User[]>([])
const currentIndex = ref(0)
const likesCount = ref(50)

onMounted(() => {
  loadUsers()
})

async function loadUsers() {
  try {
    const response = await getRecommend()
    users.value = response.data.users || []
    currentIndex.value = 0
  } catch (e) {
    console.error('获取用户列表失败:', e)
  }
}

function onSwiperChange(e: any) {
  currentIndex.value = e.detail.current
}

async function handleLike() {
  if (currentIndex.value < users.value.length) {
    const user = users.value[currentIndex.value]
    try {
      // 调用喜欢API
      await fetch(`/api/discover/like/${user.id}`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${uni.getStorageSync('token')}`
        }
      })
      likesCount.value++
      // 移动到下一个
      if (currentIndex.value < users.value.length - 1) {
        currentIndex.value++
      } else {
        // 加载更多
        loadUsers()
      }
    } catch (e) {
      console.error('喜欢失败:', e)
    }
  }
}

async function handleDislike() {
  if (currentIndex.value < users.value.length) {
    const user = users.value[currentIndex.value]
    try {
      // 调用不喜欢API
      await fetch(`/api/discover/dislike/${user.id}`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${uni.getStorageSync('token')}`
        }
      })
      // 移动到下一个
      if (currentIndex.value < users.value.length - 1) {
        currentIndex.value++
      } else {
        // 加载更多
        loadUsers()
      }
    } catch (e) {
      console.error('不喜欢失败:', e)
    }
  }
}

function refreshUsers() {
  loadUsers()
}
</script>

<style lang="scss" scoped>
.discover-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 120rpx;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 60rpx 32rpx 32rpx;
  background: #f87c7c;
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

.icon-heart {
  font-size: 28rpx;
  margin-right: 8rpx;
  color: #fff;
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
  height: 1000rpx;
}

.user-card {
  position: relative;
  width: 100%;
  height: 100%;
  background: #fff;
  border-radius: 24rpx;
  overflow: hidden;
  box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.1);
}

.user-avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.vip-badge {
  position: absolute;
  top: 24rpx;
  right: 24rpx;
  background: #cd7f32;
  padding: 8rpx 20rpx;
  border-radius: 20rpx;
  
  text {
    font-size: 24rpx;
    color: #fff;
  }
}

.card-info {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
  padding: 80rpx 32rpx 40rpx;
}

.user-name {
  display: block;
  font-size: 48rpx;
  font-weight: bold;
  color: #fff;
  margin-bottom: 16rpx;
}

.user-details {
  display: flex;
  align-items: center;
  margin-bottom: 12rpx;
}

.detail-item {
  font-size: 28rpx;
  color: #fff;
  margin-right: 8rpx;
}

.user-stats {
  margin-bottom: 16rpx;
  
  text {
    font-size: 26rpx;
    color: #fff;
  }
}

.love-declaration {
  background: rgba(255, 255, 255, 0.2);
  padding: 16rpx 24rpx;
  border-radius: 12rpx;
  
  text {
    font-size: 24rpx;
    color: #fff;
    line-height: 1.5;
  }
}

.action-buttons {
  display: flex;
  justify-content: center;
  gap: 80rpx;
  margin-top: 40rpx;
}

.action-btn {
  width: 120rpx;
  height: 120rpx;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.2);
  
  &.dislike {
    background: #ff4757;
  }
  
  &.like {
    background: #2ed573;
  }
}

.btn-icon {
  font-size: 60rpx;
  color: #fff;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 200rpx 32rpx;
}

.empty-icon {
  font-size: 120rpx;
  color: #ccc;
  margin-bottom: 40rpx;
}

.empty-text {
  font-size: 32rpx;
  color: #999;
  margin-bottom: 60rpx;
}

.refresh-btn {
  padding: 24rpx 60rpx;
  background: #f87c7c;
  border-radius: 40rpx;
  
  text {
    color: #fff;
    font-size: 30rpx;
  }
}
</style>