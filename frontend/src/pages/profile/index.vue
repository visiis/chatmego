<template>
  <view class="profile-container">
    <view class="status-bar"></view>
    
    <view class="user-section">
      <image 
        v-if="userAvatar" 
        class="avatar" 
        :src="userAvatar" 
        mode="aspectFill"
        @error="onAvatarError"
      />
      <view class="avatar-placeholder" v-else>
        <text class="avatar-text">{{ user?.name?.charAt(0) || '?' }}</text>
      </view>
      <text class="nickname">{{ user?.name || '用戶' }}</text>
      <view class="vip-badge" v-if="user?.has_membership">
        <text class="vip-text">{{ user.membership?.name || 'VIP' }}</text>
      </view>
      <view class="level-badge" v-if="user?.current_level">
        <FontAwesome :name="user.current_level.icon || 'star'" size="16px" color="#ffd700" />
        <text class="level-text">{{ user.current_level.name }}</text>
      </view>
      <view class="points-info">
        <view class="point-item">
          <text class="points-value">{{ user?.points || 0 }}</text>
          <text class="points-label">積分</text>
        </view>
        <view class="point-divider"></view>
        <view class="point-item">
          <text class="points-value">{{ user?.coins || 0 }}</text>
          <text class="points-label">金幣</text>
        </view>
      </view>
    </view>
    
    <view class="content">
      <view class="menu-section">
        <view class="menu-item" @click="editProfile">
          <view class="menu-icon">
            <FontAwesome name="user-circle" size="28px" color="#ff6b9d" />
          </view>
          <text class="menu-text">修改資料</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="24px" color="#999" />
        </view>
        
        <view class="menu-item" @click="viewAlbum">
          <view class="menu-icon">
            <FontAwesome name="image" size="28px" color="#4a90d9" />
          </view>
          <text class="menu-text">我的相冊</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="24px" color="#999" />
        </view>
        
        <view class="menu-item" @click="viewStatuses">
          <view class="menu-icon">
            <FontAwesome name="comment-dots" size="28px" color="#ff9f43" />
          </view>
          <text class="menu-text">我的動態</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="24px" color="#999" />
        </view>
        
        <view class="menu-item" @click="viewGifts">
          <view class="menu-icon">
            <FontAwesome name="gift" size="28px" color="#e74c3c" />
          </view>
          <text class="menu-text">我的禮物</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="24px" color="#999" />
        </view>
        
        <view class="menu-item" @click="viewMatches">
          <view class="menu-icon">
            <FontAwesome name="heart" size="28px" color="#ff6b9d" />
          </view>
          <text class="menu-text">我的匹配</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="24px" color="#999" />
        </view>
        
        <view class="menu-item" @click="viewMembership">
          <view class="menu-icon">
            <FontAwesome name="star" size="28px" color="#ffd700" />
          </view>
          <text class="menu-text">會員中心</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="24px" color="#999" />
        </view>
        
        <view class="menu-item" @click="viewPoints">
          <view class="menu-icon">
            <FontAwesome name="coins" size="28px" color="#ffd700" />
          </view>
          <text class="menu-text">積分中心</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="24px" color="#999" />
        </view>
        
        <view class="menu-item" @click="viewSettings">
          <view class="menu-icon">
            <FontAwesome name="cog" size="28px" color="#999" />
          </view>
          <text class="menu-text">設定</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="24px" color="#999" />
        </view>
      </view>
      
      <view class="check-in-section" v-if="!checkedIn" @click="handleCheckIn">
        <view class="check-in-icon">
          <FontAwesome name="calendar-check" size="36px" color="#ff6b9d" />
        </view>
        <view class="check-in-content">
          <text class="check-in-title">每日簽到</text>
          <text class="check-in-desc">點擊獲取積分獎勵</text>
        </view>
        <view class="check-in-btn">
          <text class="check-in-text">簽到</text>
        </view>
      </view>
      
      <view class="check-in-section checked" v-else>
        <view class="check-in-icon">
          <FontAwesome name="check-circle" size="36px" color="#2ed573" />
        </view>
        <view class="check-in-content">
          <text class="check-in-title">今日已簽到</text>
          <text class="check-in-desc">已獲得 +10 積分</text>
        </view>
        <text class="check-in-success">+10積分</text>
      </view>
    </view>
    
    <button class="logout-btn" @click="handleLogout">
      <text>登出</text>
    </button>
    
    <view class="bottom-tab">
      <view class="bottom-tab-item" @click="goDiscover">
        <FontAwesome name="compass" size="24px" color="#999" />
        <text class="tab-text">發現</text>
      </view>
      <view class="bottom-tab-item" @click="goFriends">
        <FontAwesome name="users" size="24px" color="#999" />
        <text class="tab-text">好友</text>
      </view>
      <view class="bottom-tab-item" @click="goChats">
        <FontAwesome name="comment" size="24px" color="#999" />
        <text class="tab-text">聊天</text>
      </view>
      <view class="bottom-tab-item" @click="goStatuses">
        <FontAwesome name="comment-dots" size="24px" color="#999" />
        <text class="tab-text">說說</text>
      </view>
      <view class="bottom-tab-item active">
        <FontAwesome name="user" size="24px" color="#ff6b9d" />
        <text class="tab-text active">我的</text>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { getProfile, checkIn, type UserProfile } from '../../api/user'

const user = ref<UserProfile | null>(null)
const checkedIn = ref(false)
const consecutiveDays = ref(0)
const avatarError = ref(false)

const userAvatar = computed(() => {
  if (avatarError.value) return ''
  return user.value?.avatar || user.value?.avatar_url || ''
})

onMounted(() => {
  loadProfile()
  checkTodaySignedIn()
})

function onAvatarError() {
  avatarError.value = true
}

function checkTodaySignedIn() {
  const lastSignIn = uni.getStorageSync('last_sign_in_date')
  const today = new Date().toDateString()
  if (lastSignIn === today) {
    checkedIn.value = true
  }
}

async function loadProfile() {
  try {
    user.value = await getProfile()
    uni.setStorageSync('user', JSON.stringify(user.value))
  } catch (error) {
    console.error('加載用戶資料失敗:', error)
    const userStr = uni.getStorageSync('user')
    if (userStr) {
      user.value = JSON.parse(userStr)
    }
  }
}

async function handleCheckIn() {
  try {
    const result = await checkIn()
    if (user.value) {
      user.value.points = result.points || user.value.points || 0
      user.value.total_points_earned = (user.value.total_points_earned || 0) + 10
    }
    uni.setStorageSync('last_sign_in_date', new Date().toDateString())
    checkedIn.value = true
    uni.showToast({ title: '簽到成功 +10積分', icon: 'success' })
  } catch (error) {
    console.error('簽到失敗:', error)
    const errMsg = (error as any)?.message || ''
    if (errMsg.includes('已簽到') || errMsg.includes('today')) {
      checkedIn.value = true
      uni.showToast({ title: '今日已簽到', icon: 'none' })
    } else {
      uni.showToast({ title: '簽到失敗', icon: 'none' })
    }
  }
}

function editProfile() {
  uni.navigateTo({ url: '/pages/profile/edit' })
}

function viewAlbum() {
  uni.navigateTo({ url: '/pages/profile/album' })
}

function viewStatuses() {
  uni.navigateTo({ url: '/pages/profile/statuses' })
}

function viewGifts() {
  uni.navigateTo({ url: '/pages/profile/gifts' })
}

function viewMatches() {
  uni.switchTab({ url: '/pages/discover/cards' })
}

function viewMembership() {
  uni.navigateTo({ url: '/pages/profile/membership' })
}

function viewPoints() {
  uni.navigateTo({ url: '/pages/profile/points' })
}

function viewSettings() {
  uni.navigateTo({ url: '/pages/profile/settings' })
}

function handleLogout() {
  uni.showModal({
    title: '提示',
    content: '確定要登出嗎？',
    success: (res) => {
      if (res.confirm) {
        uni.removeStorageSync('token')
        uni.removeStorageSync('user')
        uni.reLaunch({ url: '/pages/auth/login' })
      }
    }
  })
}

function goDiscover() {
  uni.switchTab({ url: '/pages/discover/cards' })
}

function goFriends() {
  uni.switchTab({ url: '/pages/friends/index' })
}

function goChats() {
  uni.switchTab({ url: '/pages/messages/index' })
}

function goStatuses() {
  uni.switchTab({ url: '/pages/statuses/index' })
}
</script>

<style lang="scss">
page {
  height: 100%;
  margin: 0;
  padding: 0;
}

.profile-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding-bottom: calc(160rpx + env(safe-area-inset-bottom));
}

.status-bar {
  height: var(--status-bar-height, 44px);
}

.user-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 60rpx 0 40rpx;
  position: relative;
}

.avatar {
  width: 160rpx;
  height: 160rpx;
  border-radius: 50%;
  border: 4rpx solid #fff;
  box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.2);
  background: #fff;
}

.avatar-placeholder {
  width: 160rpx;
  height: 160rpx;
  background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 4rpx solid #fff;
  box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.2);
}

.avatar-text {
  font-size: 64rpx;
  color: #ff6b9d;
  font-weight: bold;
}

.nickname {
  font-size: 36rpx;
  color: #fff;
  font-weight: bold;
  margin-top: 20rpx;
}

.vip-badge {
  background: linear-gradient(135deg, #ffd700, #ffb347);
  padding: 6rpx 20rpx;
  border-radius: 16rpx;
  margin-top: 12rpx;
  display: flex;
  align-items: center;
  gap: 8rpx;
}

.vip-text {
  font-size: 22rpx;
  color: #fff;
  font-weight: bold;
}

.level-badge {
  background: rgba(255, 255, 255, 0.95);
  padding: 8rpx 20rpx;
  border-radius: 16rpx;
  margin-top: 8rpx;
  display: flex;
  align-items: center;
  gap: 8rpx;
}

.level-text {
  font-size: 22rpx;
  color: #ff6b9d;
  font-weight: 500;
}

.points-info {
  display: flex;
  align-items: center;
  margin-top: 16rpx;
  background: rgba(255, 255, 255, 0.95);
  padding: 12rpx 40rpx;
  border-radius: 20rpx;
}

.point-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.points-value {
  font-size: 36rpx;
  color: #ff6b9d;
  font-weight: bold;
}

.points-label {
  font-size: 22rpx;
  color: #999;
}

.point-divider {
  width: 2rpx;
  height: 48rpx;
  background: #eee;
  margin: 0 32rpx;
}

.content {
  padding: 24rpx;
  position: relative;
}

.menu-section {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 16rpx;
  overflow: hidden;
  margin-bottom: 20rpx;
  backdrop-filter: blur(10px);
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 28rpx 24rpx;
  border-bottom: 1rpx solid #f0f0f0;
  
  &:last-child {
    border-bottom: none;
  }
  
  &:active {
    background: #f8f9fa;
  }
}

.menu-icon {
  width: 64rpx;
  height: 64rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 20rpx;
}

.menu-text {
  flex: 1;
  font-size: 30rpx;
  color: #333;
}

.menu-arrow {
  flex-shrink: 0;
}

.check-in-section {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 16rpx;
  padding: 24rpx;
  display: flex;
  align-items: center;
  margin-bottom: 20rpx;
  border: 2rpx solid rgba(255, 255, 255, 0.8);
  
  &:active {
    transform: scale(0.98);
  }
  
  &.checked {
    border-color: #2ed573;
  }
}

.check-in-icon {
  width: 80rpx;
  height: 80rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 107, 157, 0.1);
  border-radius: 50%;
  margin-right: 20rpx;
  
  .checked & {
    background: rgba(46, 213, 115, 0.1);
  }
}

.check-in-content {
  flex: 1;
}

.check-in-title {
  display: block;
  font-size: 30rpx;
  color: #333;
  font-weight: 500;
  margin-bottom: 4rpx;
}

.check-in-desc {
  font-size: 24rpx;
  color: #999;
}

.check-in-btn {
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 16rpx 32rpx;
  border-radius: 24rpx;
}

.check-in-text {
  font-size: 28rpx;
  color: #fff;
  font-weight: 500;
}

.check-in-success {
  font-size: 28rpx;
  color: #2ed573;
  font-weight: 500;
}

.logout-btn {
  width: calc(100% - 48rpx);
  margin: 0 24rpx 20rpx;
  height: 88rpx;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 44rpx;
  border: 2rpx solid rgba(255, 255, 255, 0.5);
  color: #fff;
  font-size: 30rpx;
  line-height: 88rpx;
  
  &::after {
    border: none;
  }
  
  &:active {
    background: rgba(255, 255, 255, 0.3);
  }
}

.bottom-tab {
  display: flex;
  background: #fff;
  border-top: 1rpx solid #eee;
  padding: 12rpx 0 24rpx;
  padding-bottom: calc(24rpx + env(safe-area-inset-bottom));
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 999;
}

.bottom-tab-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4rpx;
  
  &.active {
    .tab-text {
      color: #ff6b9d;
    }
  }
}

.tab-text {
  font-size: 20rpx;
  color: #999;
  
  &.active {
    color: #ff6b9d;
  }
}
</style>
