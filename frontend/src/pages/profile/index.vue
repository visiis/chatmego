<template>
  <view class="profile-page">
    <view class="profile-header">
      <image 
        class="header-bg" 
        src="https://neeko-copilot.bytedance.net/api/text_to_image?prompt=beautiful%20sunset%20sky%20romantic&image_size=portrait_16_9" 
        mode="aspectFill" 
      />
      <view class="header-overlay"></view>
      
      <view class="profile-card">
        <view class="avatar-section">
          <image 
            class="profile-avatar" 
            :src="userStore.user?.avatar || 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20person%20cute&image_size=square'" 
            mode="aspectFill" 
          />
          <view class="vip-badge" v-if="userStore.user?.is_vip">
            <text class="icon-crown">♛</text>
          </view>
          <view class="edit-avatar" @click="editAvatar">
            <text class="icon-camera">📷</text>
          </view>
        </view>
        
        <text class="profile-name">{{ userStore.user?.nickname || '用户' }}</text>
        <view class="profile-level" v-if="userStore.user?.vip_level">
          <text class="icon-star">⭐</text>
          <text class="level-text">VIP{{ userStore.user.vip_level }}</text>
        </view>
        
        <view class="points-info">
          <view class="points-item">
            <text class="points-value">{{ userStore.user?.points.balance || 0 }}</text>
            <text class="points-label">积分</text>
          </view>
          <view class="divider"></view>
          <view class="points-item">
            <text class="points-value">{{ userStore.user?.points.total_earned || 0 }}</text>
            <text class="points-label">累计获得</text>
          </view>
          <view class="divider"></view>
          <view class="points-item">
            <text class="points-value">{{ userStore.user?.points.total_spent || 0 }}</text>
            <text class="points-label">累计消费</text>
          </view>
        </view>
        
        <view class="edit-btn" @click="goToEditProfile">
          <text>编辑资料</text>
          <text class="arrow">→</text>
        </view>
      </view>
    </view>
    
    <view class="quick-actions">
      <view class="action-card" @click="goToMembership">
        <view class="action-icon-wrapper vip">
          <text class="icon-crown">♛</text>
        </view>
        <text class="action-title">会员中心</text>
        <text class="action-desc">升级尊享更多特权</text>
      </view>
      
      <view class="action-card" @click="goToGifts">
        <view class="action-icon-wrapper gift">
          <text class="icon-gift">▤</text>
        </view>
        <text class="action-title">礼物中心</text>
        <text class="action-desc">查看收到的礼物</text>
      </view>
      
      <view class="action-card" @click="goToPhotos">
        <view class="action-icon-wrapper photo">
          <text class="action-icon">📷</text>
        </view>
        <text class="action-title">我的相册</text>
        <text class="action-desc">{{ userStore.user?.photos?.length || 0 }}张照片</text>
      </view>
    </view>
    
    <view class="menu-section">
      <view class="menu-item" @click="checkIn">
        <text class="menu-icon">✅</text>
        <text class="menu-text">每日签到</text>
        <text class="menu-badge" v-if="!hasCheckedIn">+{{ dailyPoints }}</text>
        <text class="menu-status" v-else>已签到</text>
      </view>
      
      <view class="menu-item" @click="goToWallet">
        <text class="menu-icon">💰</text>
        <text class="menu-text">我的钱包</text>
        <text class="menu-value">{{ userStore.user?.points.balance || 0 }}积分</text>
      </view>
      
      <view class="menu-item" @click="goToMatchRecord">
        <text class="icon-heart">♥</text>
        <text class="menu-text">匹配记录</text>
      </view>
      
      <view class="menu-item" @click="goToSettings">
        <text class="icon-settings">⚙</text>
        <text class="menu-text">设置</text>
      </view>
      
      <view class="menu-item" @click="logout">
        <text class="icon-logout">⎋</text>
        <text class="menu-text">退出登录</text>
        <text class="logout-label">安全退出</text>
      </view>
    </view>
    
    <view class="footer">
      <text class="version">ChatMeGo v1.0.0</text>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useUserStore } from '@/stores/user'
import { checkIn } from '@/api/points'

const userStore = useUserStore()
const hasCheckedIn = ref(false)
const dailyPoints = ref(10)

async function checkIn() {
  if (hasCheckedIn.value) {
    uni.showToast({ title: '今日已签到', icon: 'none' })
    return
  }
  
  try {
    await checkIn()
    hasCheckedIn.value = true
    uni.showToast({ title: `签到成功 +${dailyPoints.value}积分`, icon: 'success' })
    userStore.fetchUserProfile()
  } catch (e: any) {
    uni.showToast({ title: e.message || '签到失败', icon: 'none' })
  }
}

function editAvatar() {
  uni.chooseImage({
    count: 1,
    success: (res) => {
      uni.showToast({ title: '头像上传成功', icon: 'success' })
    }
  })
}

function goToEditProfile() {
  uni.navigateTo({ url: '/pages/profile/edit' })
}

function goToMembership() {
  uni.navigateTo({ url: '/pages/membership/index' })
}

function goToGifts() {
  uni.navigateTo({ url: '/pages/gifts/index' })
}

function goToPhotos() {
  uni.showToast({ title: '相册功能开发中', icon: 'none' })
}

function goToWallet() {
  uni.showToast({ title: '钱包功能开发中', icon: 'none' })
}

function goToMatchRecord() {
  uni.showToast({ title: '匹配记录开发中', icon: 'none' })
}

function goToSettings() {
  uni.navigateTo({ url: '/pages/settings/index' })
}

function logout() {
  uni.showModal({
    title: '退出登录',
    content: '确定要退出登录吗?',
    success: (res) => {
      if (res.confirm) {
        userStore.logout()
      }
    }
  })
}
</script>

<style lang="scss" scoped>
.profile-page {
  min-height: 100vh;
  background: #f5f5f5;
}

.profile-header {
  position: relative;
  padding-bottom: 60rpx;
}

.header-bg {
  width: 100%;
  height: 320rpx;
}

.header-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(transparent 30%, rgba(0, 0, 0, 0.5));
}

.profile-card {
  position: relative;
  background: #fff;
  margin: -100rpx 24rpx 0;
  border-radius: 24rpx;
  padding: 60rpx 32rpx 32rpx;
  box-shadow: 0 8rpx 32rpx rgba(0, 0, 0, 0.1);
}

.avatar-section {
  position: absolute;
  top: -80rpx;
  left: 50%;
  transform: translateX(-50%);
}

.profile-avatar {
  width: 160rpx;
  height: 160rpx;
  border-radius: 50%;
  border: 6rpx solid #fff;
}

.vip-badge {
  position: absolute;
  bottom: -8rpx;
  right: -8rpx;
  font-size: 36rpx;
  background: #ffd700;
  border-radius: 50%;
  padding: 8rpx;
  border: 4rpx solid #fff;
}

.edit-avatar {
  position: absolute;
  bottom: 0;
  right: -8rpx;
  width: 48rpx;
  height: 48rpx;
  background: #f87c7c;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 4rpx solid #fff;
}

.edit-icon {
  font-size: 24rpx;
}

.profile-name {
  display: block;
  text-align: center;
  font-size: 36rpx;
  font-weight: bold;
  color: #333;
  margin-top: 60rpx;
  margin-bottom: 12rpx;
}

.profile-level {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 32rpx;
}

.level-icon {
  font-size: 28rpx;
  margin-right: 8rpx;
}

.level-text {
  font-size: 24rpx;
  color: #f87c7c;
  font-weight: bold;
}

.points-info {
  display: flex;
  justify-content: space-around;
  padding: 24rpx 0;
  border-top: 1rpx solid #f0f0f0;
  border-bottom: 1rpx solid #f0f0f0;
  margin-bottom: 24rpx;
}

.points-item {
  text-align: center;
}

.points-value {
  display: block;
  font-size: 36rpx;
  font-weight: bold;
  color: #f87c7c;
  margin-bottom: 8rpx;
}

.points-label {
  font-size: 22rpx;
  color: #999;
}

.divider {
  width: 1rpx;
  background: #f0f0f0;
}

.edit-btn {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20rpx;
  background: #f8f8f8;
  border-radius: 40rpx;
  
  text {
    font-size: 28rpx;
    color: #666;
  }
  
  .arrow {
    margin-left: 8rpx;
    color: #999;
  }
}

.quick-actions {
  display: flex;
  gap: 16rpx;
  padding: 24rpx;
}

.action-card {
  flex: 1;
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
  text-align: center;
  
  &:active {
    background: #f9f9f9;
  }
}

.action-icon-wrapper {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16rpx;
  
  &.vip {
    background: linear-gradient(135deg, #ffd700 0%, #ffb700 100%);
  }
  
  &.gift {
    background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
  }
  
  &.photo {
    background: linear-gradient(135deg, #67c23a 0%, #85ce61 100%);
  }
}

.action-icon {
  font-size: 40rpx;
}

.action-title {
  display: block;
  font-size: 28rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 8rpx;
}

.action-desc {
  font-size: 22rpx;
  color: #999;
}

.menu-section {
  background: #fff;
  margin: 0 24rpx;
  border-radius: 16rpx;
  overflow: hidden;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 28rpx 24rpx;
  border-bottom: 1rpx solid #f8f8f8;
  
  &:last-child {
    border-bottom: none;
  }
  
  &:active {
    background: #f9f9f9;
  }
}

.menu-icon {
  font-size: 36rpx;
  margin-right: 20rpx;
}

.menu-text {
  flex: 1;
  font-size: 30rpx;
  color: #333;
}

.menu-badge {
  font-size: 24rpx;
  color: #f87c7c;
  font-weight: bold;
  background: rgba(248, 124, 124, 0.1);
  padding: 6rpx 16rpx;
  border-radius: 20rpx;
}

.menu-status {
  font-size: 24rpx;
  color: #999;
}

.menu-value {
  font-size: 28rpx;
  color: #f87c7c;
  font-weight: bold;
}

.logout-label {
  font-size: 24rpx;
  color: #ff4d4f;
}

.footer {
  text-align: center;
  padding: 40rpx;
}

.version {
  font-size: 22rpx;
  color: #ccc;
}
</style>
