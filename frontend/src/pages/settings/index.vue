<template>
  <view class="settings-page">
    <view class="header">
      <text class="back-btn" @click="goBack">←</text>
      <text class="page-title">设置</text>
      <view class="placeholder"></view>
    </view>
    
    <view class="settings-list">
      <view class="section">
        <view class="menu-item" @click="goToAccount">
          <text class="menu-icon">👤</text>
          <text class="menu-text">账号设置</text>
          <text class="menu-arrow">→</text>
        </view>
        
        <view class="menu-item" @click="goToPrivacy">
          <text class="menu-icon">🔒</text>
          <text class="menu-text">隐私设置</text>
          <text class="menu-arrow">→</text>
        </view>
        
        <view class="menu-item">
          <text class="menu-icon">🔔</text>
          <text class="menu-text">消息通知</text>
          <switch class="menu-switch" :checked="notificationsEnabled" @change="toggleNotifications" />
        </view>
        
        <view class="menu-item">
          <text class="menu-icon">🌙</text>
          <text class="menu-text">深色模式</text>
          <switch class="menu-switch" :checked="darkMode" @change="toggleDarkMode" />
        </view>
        
        <view class="menu-item">
          <text class="menu-icon">🌐</text>
          <text class="menu-text">语言设置</text>
          <text class="menu-value">{{ currentLanguage }}</text>
          <text class="menu-arrow">→</text>
        </view>
      </view>
      
      <view class="section">
        <view class="menu-item" @click="goToHelp">
          <text class="menu-icon">❓</text>
          <text class="menu-text">帮助中心</text>
          <text class="menu-arrow">→</text>
        </view>
        
        <view class="menu-item" @click="goToFeedback">
          <text class="menu-icon">💬</text>
          <text class="menu-text">意见反馈</text>
          <text class="menu-arrow">→</text>
        </view>
        
        <view class="menu-item" @click="goToAbout">
          <text class="menu-icon">ℹ️</text>
          <text class="menu-text">关于我们</text>
          <text class="menu-arrow">→</text>
        </view>
      </view>
      
      <view class="section">
        <view class="menu-item danger" @click="clearCache">
          <text class="menu-icon">🗑️</text>
          <text class="menu-text">清除缓存</text>
          <text class="menu-value">{{ cacheSize }}</text>
        </view>
        
        <view class="menu-item danger" @click="logout">
          <text class="menu-icon">🚪</text>
          <text class="menu-text">退出登录</text>
        </view>
      </view>
    </view>
    
    <view class="footer">
      <text class="version">ChatMeGo v1.0.0</text>
      <text class="copyright">© 2025 ChatMeGo. All rights reserved.</text>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()

const notificationsEnabled = ref(true)
const darkMode = ref(false)
const currentLanguage = ref('中文(繁体)')
const cacheSize = ref('2.3MB')

function toggleNotifications(e: any) {
  notificationsEnabled.value = e.detail.value
  uni.showToast({ 
    title: notificationsEnabled.value ? '通知已开启' : '通知已关闭', 
    icon: 'none' 
  })
}

function toggleDarkMode(e: any) {
  darkMode.value = e.detail.value
  uni.showToast({ 
    title: darkMode.value ? '深色模式已开启' : '深色模式已关闭', 
    icon: 'none' 
  })
}

function goToAccount() {
  uni.showToast({ title: '账号设置开发中', icon: 'none' })
}

function goToPrivacy() {
  uni.showToast({ title: '隐私设置开发中', icon: 'none' })
}

function goToHelp() {
  uni.showToast({ title: '帮助中心开发中', icon: 'none' })
}

function goToFeedback() {
  uni.showToast({ title: '意见反馈开发中', icon: 'none' })
}

function goToAbout() {
  uni.showModal({
    title: '关于 ChatMeGo',
    content: 'ChatMeGo v1.0.0\n\n一款全新的社交交友应用，帮助你遇见缘分。',
    showCancel: false
  })
}

function clearCache() {
  uni.showModal({
    title: '清除缓存',
    content: '确定要清除应用缓存吗?',
    success: (res) => {
      if (res.confirm) {
        cacheSize.value = '0KB'
        uni.showToast({ title: '缓存已清除', icon: 'success' })
      }
    }
  })
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

function goBack() {
  uni.navigateBack()
}
</script>

<style lang="scss" scoped>
.settings-page {
  min-height: 100vh;
  background: #f5f5f5;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 60rpx 32rpx 24rpx;
  background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
}

.back-btn {
  font-size: 44rpx;
  color: #fff;
}

.page-title {
  font-size: 36rpx;
  font-weight: bold;
  color: #fff;
}

.placeholder {
  width: 80rpx;
}

.settings-list {
  padding: 24rpx;
}

.section {
  background: #fff;
  border-radius: 16rpx;
  margin-bottom: 24rpx;
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
  
  &.danger {
    .menu-text {
      color: #ff4d4f;
    }
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

.menu-value {
  font-size: 26rpx;
  color: #999;
  margin-right: 12rpx;
}

.menu-arrow {
  font-size: 28rpx;
  color: #ccc;
}

.menu-switch {
  transform: scale(0.8);
}

.footer {
  text-align: center;
  padding: 60rpx 32rpx;
}

.version {
  display: block;
  font-size: 24rpx;
  color: #999;
  margin-bottom: 12rpx;
}

.copyright {
  font-size: 22rpx;
  color: #ccc;
}
</style>
