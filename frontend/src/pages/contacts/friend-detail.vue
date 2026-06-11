<template>
  <view class="friend-detail-page">
    <view class="profile-header">
      <image 
        class="profile-bg" 
        :src="friend?.photos[0]?.url || 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=beautiful%20scenery&image_size=portrait_16_9'" 
        mode="aspectFill" 
      />
      <view class="header-overlay"></view>
      
      <view class="profile-info">
        <view class="avatar-wrapper">
          <image 
            class="profile-avatar" 
            :src="friend?.avatar || 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20person&image_size=square'" 
            mode="aspectFill" 
          />
          <view class="vip-badge" v-if="friend?.is_vip">👑</view>
        </view>
        <text class="profile-name">{{ friend?.nickname }}</text>
        <text class="profile-age">{{ calculateAge(friend?.birthday) }} | {{ friend?.location }}</text>
      </view>
    </view>
    
    <view class="actions-bar">
      <view class="action-item" @click="goToChat">
        <text class="action-icon">💬</text>
        <text class="action-label">发消息</text>
      </view>
      <view class="action-item" @click="sendGift">
        <text class="action-icon">🎁</text>
        <text class="action-label">送礼物</text>
      </view>
      <view class="action-item" @click="viewAlbum">
        <text class="action-icon">📷</text>
        <text class="action-label">相册</text>
      </view>
      <view class="action-item" @click="reportUser">
        <text class="action-icon">🚫</text>
        <text class="action-label">举报</text>
      </view>
    </view>
    
    <view class="info-section">
      <view class="section-title">个人资料</view>
      
      <view class="info-item">
        <text class="info-label">性别</text>
        <text class="info-value">{{ friend?.gender === 1 ? '女' : '男' }}</text>
      </view>
      <view class="info-item">
        <text class="info-label">生日</text>
        <text class="info-value">{{ friend?.birthday || '未填写' }}</text>
      </view>
      <view class="info-item">
        <text class="info-label">身高</text>
        <text class="info-value">{{ friend?.height ? friend.height + 'cm' : '未填写' }}</text>
      </view>
      <view class="info-item">
        <text class="info-label">体重</text>
        <text class="info-value">{{ friend?.weight ? friend.weight + 'kg' : '未填写' }}</text>
      </view>
      <view class="info-item">
        <text class="info-label">星座</text>
        <text class="info-value">{{ friend?.zodiac || '未填写' }}</text>
      </view>
      <view class="info-item">
        <text class="info-label">婚姻状况</text>
        <text class="info-value">{{ friend?.marital_status || '未填写' }}</text>
      </view>
    </view>
    
    <view class="info-section">
      <view class="section-title">个人简介</view>
      <text class="bio-text">{{ friend?.love_declaration || '暂无简介' }}</text>
    </view>
    
    <view class="info-section">
      <view class="section-title">兴趣爱好</view>
      <view class="hobbies-list">
        <text 
          class="hobby-tag" 
          v-for="(hobby, idx) in (friend?.hobbies || '').split(',')" 
          :key="idx"
        >
          {{ hobby || '未填写' }}
        </text>
      </view>
    </view>
    
    <view class="info-section">
      <view class="section-title">相册</view>
      <scroll-view scroll-x class="photos-scroll">
        <view class="photos-container">
          <image 
            class="photo-item" 
            v-for="(photo, idx) in friend?.photos?.slice(0, 6) || []" 
            :key="idx"
            :src="photo.url" 
            mode="aspectFill" 
          />
          <view class="photo-item placeholder" v-if="!friend?.photos?.length">
            <text class="placeholder-icon">📷</text>
          </view>
        </view>
      </scroll-view>
    </view>
    
    <view class="bottom-actions">
      <view class="btn-secondary" @click="goBack">
        <text>返回</text>
      </view>
      <view class="btn-primary" @click="goToChat">
        <text>立即聊天</text>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { getUserProfile } from '@/api/user'

const friend = ref<any>(null)
const userId = ref(0)

function calculateAge(birthday?: string) {
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

function goToChat() {
  uni.navigateTo({ url: `/pages/chats/detail?userId=${userId.value}` })
}

function sendGift() {
  uni.showToast({ title: '送礼物功能开发中', icon: 'none' })
}

function viewAlbum() {
  uni.showToast({ title: '相册功能开发中', icon: 'none' })
}

function reportUser() {
  uni.showModal({
    title: '举报用户',
    content: '确定要举报该用户吗?',
    success: (res) => {
      if (res.confirm) {
        uni.showToast({ title: '举报已提交', icon: 'success' })
      }
    }
  })
}

function goBack() {
  uni.navigateBack()
}

onLoad((options: any) => {
  if (options?.userId) {
    userId.value = parseInt(options.userId)
    loadFriendProfile()
  }
})

async function loadFriendProfile() {
  try {
    const response = await getUserProfile(userId.value)
    friend.value = response.data
  } catch (e) {
    console.error('Failed to load friend profile:', e)
    friend.value = {
      id: userId.value,
      nickname: '用户' + userId.value,
      avatar: '',
      gender: 1,
      birthday: '',
      location: '未知',
      height: 0,
      weight: 0,
      zodiac: '',
      marital_status: '',
      love_declaration: '',
      hobbies: '',
      photos: [],
      is_vip: 0
    }
  }
}
</script>

<style lang="scss" scoped>
.friend-detail-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 160rpx;
}

.profile-header {
  position: relative;
  height: 400rpx;
}

.profile-bg {
  width: 100%;
  height: 100%;
}

.header-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(transparent 50%, rgba(0, 0, 0, 0.6));
}

.profile-info {
  position: absolute;
  bottom: 32rpx;
  left: 32rpx;
}

.avatar-wrapper {
  position: relative;
  margin-bottom: 16rpx;
}

.profile-avatar {
  width: 140rpx;
  height: 140rpx;
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

.profile-name {
  display: block;
  font-size: 40rpx;
  font-weight: bold;
  color: #fff;
  margin-bottom: 8rpx;
}

.profile-age {
  font-size: 26rpx;
  color: rgba(255, 255, 255, 0.8);
}

.actions-bar {
  display: flex;
  justify-content: space-around;
  padding: 32rpx;
  background: #fff;
  margin: -40rpx 24rpx 24rpx;
  border-radius: 20rpx;
  box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.08);
  position: relative;
  z-index: 10;
}

.action-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.action-icon {
  font-size: 48rpx;
  margin-bottom: 12rpx;
}

.action-label {
  font-size: 24rpx;
  color: #666;
}

.info-section {
  background: #fff;
  margin: 0 24rpx 24rpx;
  border-radius: 16rpx;
  padding: 24rpx;
}

.section-title {
  font-size: 30rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 24rpx;
  padding-bottom: 16rpx;
  border-bottom: 1rpx solid #f0f0f0;
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 20rpx 0;
  border-bottom: 1rpx solid #f8f8f8;
  
  &:last-child {
    border-bottom: none;
  }
}

.info-label {
  font-size: 28rpx;
  color: #999;
}

.info-value {
  font-size: 28rpx;
  color: #333;
}

.bio-text {
  font-size: 28rpx;
  color: #666;
  line-height: 1.6;
}

.hobbies-list {
  display: flex;
  flex-wrap: wrap;
  gap: 16rpx;
}

.hobby-tag {
  font-size: 24rpx;
  color: #666;
  background: #f5f5f5;
  padding: 12rpx 24rpx;
  border-radius: 20rpx;
}

.photos-scroll {
  white-space: nowrap;
}

.photos-container {
  display: inline-flex;
  gap: 16rpx;
}

.photo-item {
  width: 160rpx;
  height: 160rpx;
  border-radius: 12rpx;
  flex-shrink: 0;
  
  &.placeholder {
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
  }
}

.placeholder-icon {
  font-size: 48rpx;
}

.bottom-actions {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  gap: 24rpx;
  padding: 24rpx;
  background: #fff;
  box-shadow: 0 -4rpx 20rpx rgba(0, 0, 0, 0.05);
  padding-bottom: calc(24rpx + constant(safe-area-inset-bottom));
  padding-bottom: calc(24rpx + env(safe-area-inset-bottom));
}

.btn-secondary, .btn-primary {
  flex: 1;
  height: 88rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 40rpx;
  
  text {
    font-size: 30rpx;
    font-weight: bold;
  }
}

.btn-secondary {
  background: #f5f5f5;
  
  text {
    color: #666;
  }
}

.btn-primary {
  background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
  
  text {
    color: #fff;
  }
}
</style>
