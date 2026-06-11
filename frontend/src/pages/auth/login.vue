<template>
  <view class="login-page">
    <view class="bg-container">
      <image class="bg-image" src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1080" mode="aspectFill" />
      <view class="bg-overlay"></view>
    </view>
    
    <view class="login-form">
      <view class="logo-section">
        <view class="logo">
          <text class="logo-heart">♥</text>
        </view>
        <text class="app-name">ChatMeGo</text>
        <text class="app-slogan">遇見你的緣分</text>
      </view>
      
      <view class="form-group">
        <view class="input-wrapper">
          <text class="icon-email">📧</text>
          <input 
            class="form-input" 
            v-model="form.email" 
            :placeholder="'請輸入信箱'" 
            type="text"
          />
        </view>
      </view>
      
      <view class="form-group">
        <view class="input-wrapper">
          <text class="icon-lock">🔒</text>
          <input 
            class="form-input" 
            v-model="form.password" 
            :placeholder="t('auth.password_error')" 
            :type="showPassword ? 'text' : 'password'"
          />
          <text class="toggle-password" @click="showPassword = !showPassword">
            <text class="icon-eye" v-if="showPassword">👁️</text>
            <text class="icon-eye-off" v-else>🙈</text>
          </text>
        </view>
      </view>
      
      <view class="form-options">
        <view class="option-item">
          <view class="checkbox" :class="{ checked: form.remember }" @click="form.remember = !form.remember">
            <text class="icon-check" v-if="form.remember">✓</text>
          </view>
          <text class="option-text">{{ t('auth.remember_me') }}</text>
        </view>
        <text class="forgot-password" @click="goToForgotPassword">{{ t('auth.forgot_password') }}</text>
      </view>
      
      <view class="btn-login" @click="handleLogin">
        <text class="btn-text">{{ t('common.login') }}</text>
      </view>
      
      <view class="alternative-login">
        <view class="divider">
          <view class="divider-line"></view>
          <text class="divider-text">{{ t('common.or') }}</text>
          <view class="divider-line"></view>
        </view>
        
        <view class="sms-login-btn" @click="goToSmsLogin">
          <text class="sms-btn-text">{{ t('auth.login_by_code') }}</text>
        </view>
      </view>
      
      <view class="register-link">
        <text>{{ t('auth.no_account') }} </text>
        <text class="link-text" @click="goToRegister">{{ t('auth.sign_up_now') }}</text>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useUserStore } from '@/stores/user'
import { login } from '@/api/auth'
import { t } from '@/utils/i18n'

const userStore = useUserStore()
const showPassword = ref(false)

const form = reactive({
  email: '',
  password: '',
  remember: false
})

async function handleLogin() {
  if (!form.email) {
    uni.showToast({ title: '請輸入信箱', icon: 'none' })
    return
  }
  if (!form.password) {
    uni.showToast({ title: '請輸入密碼', icon: 'none' })
    return
  }

  try {
    console.log('开始登录...')
    const response = await login(form.email, form.password)
    console.log('登录响应:', response)
    
    userStore.setUser(response.data.user)
    userStore.setToken(response.data.token)
    userStore.setImInfo(response.data.im.account, response.data.im.token)
    
    console.log('用户信息已保存')
    uni.showToast({ title: '登录成功', icon: 'success' })
    
    setTimeout(() => {
      console.log('尝试跳转到发现页面...')
      uni.reLaunch({ 
        url: '/pages/discover/index',
        success: function() {
          console.log('uni.reLaunch 跳转成功')
        },
        fail: function(err) {
          console.error('uni.reLaunch 跳转失败:', err)
          // 尝试直接使用 window.location
          console.log('尝试使用 window.location 跳转...')
          window.location.href = '/pages/discover/index'
        }
      })
    }, 500)
  } catch (e: any) {
    console.error('登录失败:', e)
    uni.showToast({ title: e.message || '登录失败', icon: 'none' })
  }
}

function goToSmsLogin() {
  uni.navigateTo({ url: '/pages/auth/login?type=sms' })
}

function goToRegister() {
  uni.navigateTo({ url: '/pages/auth/register' })
}

function goToForgotPassword() {
  uni.navigateTo({ url: '/pages/auth/forgot-password' })
}
</script>

<style lang="scss" scoped>
.login-page {
  min-height: 100vh;
  position: relative;
}

.bg-container {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;
}

.bg-image {
  width: 100%;
  height: 100%;
  filter: blur(2px);
}

.bg-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4);
}

.login-form {
  position: relative;
  z-index: 10;
  padding: 60rpx 40rpx;
  padding-top: 200rpx;
}

.logo-section {
  text-align: center;
  margin-bottom: 80rpx;
}

.logo {
  width: 160rpx;
  height: 160rpx;
  background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 30rpx;
  box-shadow: 0 8rpx 32rpx rgba(248, 124, 124, 0.4);
}

.logo-text {
  font-size: 64rpx;
}

.logo-heart {
  font-size: 64rpx;
  color: #fff;
}

.app-name {
  display: block;
  font-size: 48rpx;
  font-weight: bold;
  color: #fff;
  margin-bottom: 16rpx;
}

.app-slogan {
  font-size: 28rpx;
  color: rgba(255, 255, 255, 0.8);
}

.form-group {
  margin-bottom: 32rpx;
}

.input-wrapper {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 40rpx;
  padding: 0 32rpx;
  display: flex;
  align-items: center;
  height: 96rpx;
  box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.1);
}

.icon-phone, .icon-lock {
  font-size: 36rpx;
  margin-right: 20rpx;
}

.form-input {
  flex: 1;
  height: 100%;
  font-size: 32rpx;
  color: #333;
}

.toggle-password {
  font-size: 32rpx;
  padding: 10rpx;
  cursor: pointer;
}

.icon-eye, .icon-eye-off {
  font-size: 32rpx;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40rpx;
}

.option-item {
  display: flex;
  align-items: center;
}

.checkbox {
  width: 40rpx;
  height: 40rpx;
  border: 2rpx solid rgba(255, 255, 255, 0.6);
  border-radius: 8rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12rpx;
  color: #fff;
  font-size: 24rpx;
  transition: all 0.3s;
  
  &.checked {
    background: #f87c7c;
    border-color: #f87c7c;
  }
}

.icon-check {
  font-size: 24rpx;
  font-weight: bold;
}

.option-text {
  font-size: 26rpx;
  color: rgba(255, 255, 255, 0.8);
}

.forgot-password {
  font-size: 26rpx;
  color: rgba(255, 255, 255, 0.8);
}

.btn-login {
  background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
  border-radius: 40rpx;
  height: 96rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 40rpx;
  box-shadow: 0 8rpx 24rpx rgba(248, 124, 124, 0.4);
  
  &:active {
    opacity: 0.8;
  }
}

.btn-text {
  font-size: 34rpx;
  font-weight: bold;
  color: #fff;
}

.alternative-login {
  margin-bottom: 40rpx;
}

.divider {
  display: flex;
  align-items: center;
  margin-bottom: 32rpx;
}

.divider-line {
  flex: 1;
  height: 1rpx;
  background: rgba(255, 255, 255, 0.3);
}

.divider-text {
  padding: 0 24rpx;
  font-size: 26rpx;
  color: rgba(255, 255, 255, 0.6);
}

.sms-login-btn {
  background: rgba(255, 255, 255, 0.15);
  border: 2rpx solid rgba(255, 255, 255, 0.3);
  border-radius: 40rpx;
  height: 88rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sms-btn-text {
  font-size: 30rpx;
  color: #fff;
}

.register-link {
  text-align: center;
  font-size: 28rpx;
  color: rgba(255, 255, 255, 0.8);
}

.link-text {
  color: #fff;
  font-weight: bold;
}
</style>
