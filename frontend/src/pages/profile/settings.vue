<template>
  <view class="settings-container">
    <view class="status-bar"></view>
    
    <view class="nav-bar">
      <view class="nav-left" @click="goBack">
        <FontAwesome name="arrow-left" size="24px" color="#fff" />
      </view>
      <text class="nav-title">設定</text>
      <view class="nav-right"></view>
    </view>
    
    <scroll-view class="settings-content" scroll-y>
      <view class="section">
        <view class="section-title">帳戶設定</view>
        
        <view class="menu-item" @click="changePassword">
          <view class="menu-icon">
            <FontAwesome name="lock" size="24px" color="#999" />
          </view>
          <text class="menu-text">修改密碼</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="20px" color="#999" />
        </view>
        
        <view class="menu-item" @click="changePhone">
          <view class="menu-icon">
            <FontAwesome name="phone" size="24px" color="#999" />
          </view>
          <text class="menu-text">綁定手機號</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="20px" color="#999" />
        </view>
        
        <view class="menu-item" @click="changeEmail">
          <view class="menu-icon">
            <FontAwesome name="envelope" size="24px" color="#999" />
          </view>
          <text class="menu-text">修改郵箱</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="20px" color="#999" />
        </view>
      </view>
      
      <view class="section">
        <view class="section-title">隱私設定</view>
        
        <view class="menu-item">
          <view class="menu-icon">
            <FontAwesome name="eye" size="24px" color="#999" />
          </view>
          <text class="menu-text">在線狀態</text>
          <switch :checked="privacy.onlineStatus" @change="toggleOnlineStatus" color="#ff6b9d" />
        </view>
        
        <view class="menu-item">
          <view class="menu-icon">
            <FontAwesome name="location-arrow" size="24px" color="#999" />
          </view>
          <text class="menu-text">顯示距離</text>
          <switch :checked="privacy.showDistance" @change="toggleShowDistance" color="#ff6b9d" />
        </view>
        
        <view class="menu-item">
          <view class="menu-icon">
            <FontAwesome name="heart" size="24px" color="#999" />
          </view>
          <text class="menu-text">允許他人喜歡我</text>
          <switch :checked="privacy.allowLikes" @change="toggleAllowLikes" color="#ff6b9d" />
        </view>
      </view>
      
      <view class="section">
        <view class="section-title">通知設定</view>
        
        <view class="menu-item">
          <view class="menu-icon">
            <FontAwesome name="bell" size="24px" color="#999" />
          </view>
          <text class="menu-text">消息通知</text>
          <switch :checked="notification.message" @change="toggleMessageNotification" color="#ff6b9d" />
        </view>
        
        <view class="menu-item">
          <view class="menu-icon">
            <FontAwesome name="heart" size="24px" color="#ff6b9d" />
          </view>
          <text class="menu-text">匹配通知</text>
          <switch :checked="notification.match" @change="toggleMatchNotification" color="#ff6b9d" />
        </view>
        
        <view class="menu-item">
          <view class="menu-icon">
            <FontAwesome name="gift" size="24px" color="#ffd700" />
          </view>
          <text class="menu-text">禮物通知</text>
          <switch :checked="notification.gift" @change="toggleGiftNotification" color="#ff6b9d" />
        </view>
      </view>
      
      <view class="section">
        <view class="section-title">關於</view>
        
        <view class="menu-item" @click="showAbout">
          <view class="menu-icon">
            <FontAwesome name="info-circle" size="24px" color="#999" />
          </view>
          <text class="menu-text">關於我們</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="20px" color="#999" />
        </view>
        
        <view class="menu-item" @click="showPrivacy">
          <view class="menu-icon">
            <FontAwesome name="file-text" size="24px" color="#999" />
          </view>
          <text class="menu-text">隱私政策</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="20px" color="#999" />
        </view>
        
        <view class="menu-item" @click="showTerms">
          <view class="menu-icon">
            <FontAwesome name="file-contract" size="24px" color="#999" />
          </view>
          <text class="menu-text">服務條款</text>
          <FontAwesome class="menu-arrow" name="chevron-right" size="20px" color="#999" />
        </view>
      </view>
      
      <view class="version-info">
        <text class="version-text">ChatMeGo v1.0.0</text>
      </view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { reactive } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'

const privacy = reactive({
  onlineStatus: true,
  showDistance: true,
  allowLikes: true
})

const notification = reactive({
  message: true,
  match: true,
  gift: true
})

function goBack() {
  uni.navigateBack()
}

function changePassword() {
  uni.showToast({ title: '修改密碼功能開發中', icon: 'none' })
}

function changePhone() {
  uni.showToast({ title: '綁定手機號功能開發中', icon: 'none' })
}

function changeEmail() {
  uni.showToast({ title: '修改郵箱功能開發中', icon: 'none' })
}

function toggleOnlineStatus(e: any) {
  privacy.onlineStatus = e.detail.value
}

function toggleShowDistance(e: any) {
  privacy.showDistance = e.detail.value
}

function toggleAllowLikes(e: any) {
  privacy.allowLikes = e.detail.value
}

function toggleMessageNotification(e: any) {
  notification.message = e.detail.value
}

function toggleMatchNotification(e: any) {
  notification.match = e.detail.value
}

function toggleGiftNotification(e: any) {
  notification.gift = e.detail.value
}

function showAbout() {
  uni.showModal({
    title: '关于我们',
    content: 'ChatMeGo - 遇见心动的TA',
    showCancel: false
  })
}

function showPrivacy() {
  uni.showToast({ title: '隐私政策开发中', icon: 'none' })
}

function showTerms() {
  uni.showToast({ title: '服务条款开发中', icon: 'none' })
}
</script>

<style lang="scss">
page {
  height: 100%;
  margin: 0;
  padding: 0;
}

.settings-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  height: 100dvh;
  background: #f5f5f5;
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
  padding: 20rpx 24rpx;
  flex-shrink: 0;
}

.nav-left, .nav-right {
  width: 80rpx;
  height: 80rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-title {
  font-size: 32rpx;
  color: #fff;
  font-weight: 500;
}

.settings-content {
  flex: 1;
  padding: 24rpx;
  overflow: hidden;
  box-sizing: border-box;
}

.section {
  background: #fff;
  border-radius: 16rpx;
  margin-bottom: 24rpx;
  overflow: hidden;
}

.section-title {
  font-size: 24rpx;
  color: #999;
  padding: 20rpx 24rpx;
  border-bottom: 1rpx solid #f0f0f0;
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
  width: 56rpx;
  height: 56rpx;
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

.version-info {
  text-align: center;
  padding: 40rpx 0;
}

.version-text {
  font-size: 24rpx;
  color: #999;
}
</style>