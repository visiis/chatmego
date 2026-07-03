<template>
  <view class="success-container">
    <view class="success-icon">
  <view class="check-mark"></view>
</view>
    <text class="success-title">登入成功</text>
    <text class="success-subtitle">歡迎回來</text>
    <view class="user-info" v-if="user">
      <image v-if="user.avatar" class="avatar" :src="user.avatar" mode="aspectFill" />
      <view class="avatar-placeholder" v-else>
        <text class="avatar-text">{{ user.nickname?.charAt(0) || '?' }}</text>
      </view>
      <text class="nickname">{{ user.nickname }}</text>
    </view>
    <button class="back-btn" @click="goToDiscover">
      <text>開始邂逅吧（{{ countdown }}S）</text>
    </button>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import type { User } from '../../types'

const user = ref<User | null>(null)
const countdown = ref(5)
let timer: ReturnType<typeof setInterval> | null = null

onMounted(() => {
  const userStr = uni.getStorageSync('user')
  if (userStr) {
    user.value = JSON.parse(userStr)
  }
  
  timer = setInterval(() => {
    if (countdown.value > 0) {
      countdown.value--
    } else {
      if (timer) {
        clearInterval(timer)
        timer = null
      }
      goToDiscover()
    }
  }, 1000)
})

onUnmounted(() => {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
})

function goToDiscover() {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
  uni.reLaunch({ url: '/pages/discover/cards' })
}
</script>

<style lang="scss">
.success-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40rpx;
}

.success-icon {
  width: 160rpx;
  height: 160rpx;
  background: linear-gradient(135deg, #52c41a 0%, #73d13d 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8rpx 32rpx rgba(82, 196, 26, 0.3);
}

.check-mark {
  width: 64rpx;
  height: 64rpx;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E");
  background-size: 100% 100%;
  background-repeat: no-repeat;
}

.success-title {
  font-size: 40rpx;
  font-weight: bold;
  color: #333;
  margin-top: 40rpx;
}

.success-subtitle {
  font-size: 28rpx;
  color: #999;
  margin-top: 16rpx;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 48rpx;
}

.avatar {
  width: 120rpx;
  height: 120rpx;
  border-radius: 50%;
}

.avatar-placeholder {
  width: 120rpx;
  height: 120rpx;
  background: linear-gradient(135deg, #f87c7c 0%, #ff9a9e 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-text {
  font-size: 48rpx;
  color: #fff;
  font-weight: bold;
}

.nickname {
  font-size: 32rpx;
  color: #333;
  margin-top: 24rpx;
}

.back-btn {
  width: 400rpx;
  height: 88rpx;
  background: linear-gradient(135deg, #f87c7c 0%, #ff8fab 100%);
  border-radius: 44rpx;
  border: none;
  margin-top: 64rpx;
  color: #fff;
  font-size: 30rpx;
  line-height: 88rpx;
  box-shadow: 0 12rpx 30rpx rgba(255, 107, 157, 0.4);
  
  &::after {
    border: none;
  }
  
  &:active {
    transform: scale(0.98);
  }
}
</style>
