<template>
  <view class="login-container">
    <view class="bg-decoration">
      <view class="heart heart-1"></view>
      <view class="heart heart-2"></view>
      <view class="heart heart-3"></view>
      <view class="heart heart-4"></view>
      <view class="heart heart-5"></view>
      <view class="heart heart-6"></view>
    </view>
    
    <view class="logo-section">
      <view class="logo-wrapper">
        <image class="logo-image" src="/static/images/logo.svg" mode="aspectFit" />
      </view>
      <text class="logo-title">ChatMeGo</text>
      <text class="logo-subtitle">遇見心動的TA</text>
    </view>
    
    <view class="form-section">
      <view class="input-item">
        <view class="input-box">
          <FontAwesome class="input-icon" name="envelope-o" size="20px" color="#999" />
          <input 
            v-model="form.email" 
            class="input-field" 
            type="text" 
            placeholder="請輸入電子郵件"
            placeholder-class="input-placeholder"
          />
        </view>
      </view>
      
      <view class="input-item">
        <view class="input-box">
          <FontAwesome class="input-icon" name="lock" size="24px" color="#999" />
          <input 
            v-model="form.password" 
            class="input-field" 
            :type="showPassword ? 'text' : 'password'" 
            placeholder="請輸入密碼"
            placeholder-class="input-placeholder"
          />
          <FontAwesome class="input-icon-right" :name="showPassword ? 'eye-slash' : 'eye'" size="24px" color="#999" @click="togglePassword" />
        </view>
      </view>
      
      <view class="remember-section">
        <view class="checkbox-wrapper" @click="toggleRemember">
          <view class="checkbox" :class="{ checked: form.remember }">
            <FontAwesome v-if="form.remember" name="check" size="20px" color="#fff" />
          </view>
          <text class="remember-text">記住我的帳號</text>
        </view>
        <text class="forgot-link" @click="goForgotPassword">忘記密碼？</text>
      </view>
      
      <button class="login-btn" :disabled="loading" @click="handleLogin">
        <text v-if="loading">登入中...</text>
        <view v-else class="btn-content">
          <FontAwesome name="arrow-right" size="20px" color="#fff" />
          <text class="btn-text">登 入</text>
        </view>
      </button>
      
      <view class="divider">
        <view class="divider-line"></view>
        <text class="divider-text">或者</text>
        <view class="divider-line"></view>
      </view>
      
      <view class="social-section">
        <view class="social-btn google" @click="googleLogin">
          <FontAwesome class="social-icon" name="google" type="brands" size="24px" color="#fff" />
          <text class="social-text">Google 登入</text>
        </view>
        <view class="social-btn phone" @click="phoneLogin">
          <FontAwesome class="social-icon" name="mobile" size="24px" color="#fff" />
          <text class="social-text">手機號登入</text>
        </view>
      </view>
    </view>
    
    <view class="footer">
      <text class="footer-text">還沒有帳號？</text>
      <text class="footer-link" @click="goRegister">馬上註冊</text>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { login } from '../../api/auth'
import FontAwesome from '../../components/FontAwesome.vue'
import type { LoginForm } from '../../types'

const form = reactive<LoginForm>({
  email: '',
  password: '',
  remember: false
})

const loading = ref(false)
const showPassword = ref(false)

onMounted(() => {
  const savedEmail = uni.getStorageSync('remembered_email')
  if (savedEmail) {
    form.email = savedEmail
    form.remember = true
  }
})

function toggleRemember() {
  form.remember = !form.remember
}

function togglePassword() {
  showPassword.value = !showPassword.value
}

function goForgotPassword() {
  uni.showToast({ title: '忘記密碼功能開發中', icon: 'none' })
}

function goRegister() {
  uni.navigateTo({ url: '/pages/auth/register' })
}

function googleLogin() {
  uni.showToast({ title: 'Google 登入功能開發中', icon: 'none' })
}

function phoneLogin() {
  uni.showToast({ title: '手機號登入功能開發中', icon: 'none' })
}

async function handleLogin() {
  if (!form.email || !form.password) {
    uni.showToast({ title: '請填寫電子郵件與密碼', icon: 'none' })
    return
  }
  
  loading.value = true
  
  try {
    const result = await login({
      email: form.email,
      password: form.password
    })
    
    if (form.remember) {
      uni.setStorageSync('remembered_email', form.email)
    } else {
      uni.removeStorageSync('remembered_email')
    }
    
    uni.setStorageSync('token', result.token)
    uni.setStorageSync('user', JSON.stringify(result.user))
    
    uni.showToast({ title: '登入成功', icon: 'success' })
    
    setTimeout(() => {
      uni.navigateTo({ url: '/pages/auth/success' })
    }, 1500)
  } catch (error) {
    uni.showToast({ title: (error as Error).message || '登入失敗', icon: 'none' })
  } finally {
    loading.value = false
  }
}
</script>

<style lang="scss">
page {
  height: 100%;
  margin: 0;
  padding: 0;
}

.login-container {
  min-height: 100vh;
  height: 100%;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 40rpx;
  box-sizing: border-box;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.bg-decoration {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
}

.heart {
  position: absolute;
  opacity: 0.3;
  animation: float 3s ease-in-out infinite;
  width: 48rpx;
  height: 48rpx;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23fff' stroke='%23fff' stroke-width='1.5'%3E%3Cpath d='M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z'/%3E%3C/svg%3E");
  background-size: 100% 100%;
  background-repeat: no-repeat;
  
  &.heart-1 {
    top: 10%;
    left: 10%;
    animation-delay: 0s;
  }
  
  &.heart-2 {
    top: 20%;
    right: 15%;
    animation-delay: 0.5s;
    width: 36rpx;
    height: 36rpx;
  }
  
  &.heart-3 {
    bottom: 30%;
    left: 20%;
    animation-delay: 1s;
    width: 42rpx;
    height: 42rpx;
  }
  
  &.heart-4 {
    bottom: 20%;
    right: 10%;
    animation-delay: 1.5s;
    width: 30rpx;
    height: 30rpx;
  }
  
  &.heart-5 {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation-delay: 2s;
    width: 54rpx;
    height: 54rpx;
  }
  
  &.heart-6 {
    top: 15%;
    right: 8%;
    animation-delay: 2.5s;
    width: 72rpx;
    height: 72rpx;
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0) rotate(0deg);
    opacity: 0.3;
  }
  50% {
    transform: translateY(-30rpx) rotate(10deg);
    opacity: 0.6;
  }
}

.logo-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 60rpx 0 40rpx;
  position: relative;
  z-index: 1;
  flex-shrink: 0;
}

.logo-wrapper {
  width: 160rpx;
  height: 160rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-image {
  width: 120rpx;
  height: 120rpx;
}

.logo-title {
  font-size: 44rpx;
  font-weight: bold;
  color: #fff;
  margin-top: 20rpx;
  text-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.2);
}

.logo-subtitle {
  font-size: 28rpx;
  color: rgba(255, 255, 255, 0.9);
  margin-top: 16rpx;
}

.form-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 32rpx;
  padding: 48rpx 32rpx;
  box-shadow: 0 20rpx 60rpx rgba(0, 0, 0, 0.15);
  position: relative;
  z-index: 1;
  flex-shrink: 0;
}

.input-item {
  margin-bottom: 32rpx;
}

.input-label {
  font-size: 28rpx;
  color: #333;
  font-weight: 500;
  margin-bottom: 12rpx;
  display: block;
}

.input-box {
  position: relative;
  background: #f8f9fa;
  border-radius: 16rpx;
  border: 2rpx solid transparent;
  transition: all 0.3s ease;
}

.input-box:focus-within {
  border-color: #ff6b9d;
  background: #fff;
}

.input-icon {
  position: absolute;
  left: 28rpx;
  top: 50%;
  transform: translateY(-50%);
  z-index: 1;
}

.input-icon-right {
  position: absolute;
  right: 28rpx;
  top: 50%;
  transform: translateY(-50%);
  z-index: 1;
}

.input-icon-right:active {
  opacity: 0.7;
}

.input-field {
  width: 100%;
  height: 96rpx;
  background: transparent;
  border-radius: 16rpx;
  padding: 0 80rpx;
  font-size: 30rpx;
  box-sizing: border-box;
}

.input-placeholder {
  color: #ccc;
}

.remember-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 36rpx;
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
}

.checkbox {
  width: 40rpx;
  height: 40rpx;
  border: 2rpx solid #ddd;
  border-radius: 8rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  
  &.checked {
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
    border-color: #ff6b9d;
  }
}

.remember-text {
  font-size: 28rpx;
  color: #666;
  margin-left: 16rpx;
}

.forgot-link {
  font-size: 28rpx;
  color: #ff6b9d;
}

.login-btn {
  width: 100%;
  height: 96rpx;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  border-radius: 16rpx;
  border: none;
  margin-top: 20rpx;
  box-shadow: 0 8rpx 24rpx rgba(255, 107, 157, 0.4);
  transition: all 0.3s ease;
  
  &:active {
    transform: scale(0.98);
    box-shadow: 0 4rpx 12rpx rgba(255, 107, 157, 0.4);
  }
  
  &[disabled] {
    opacity: 0.6;
    box-shadow: none;
  }
}

.btn-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12rpx;
}

.btn-text {
  color: #fff;
  font-size: 34rpx;
  font-weight: bold;
}

.divider {
  display: flex;
  align-items: center;
  margin: 40rpx 0;
  
  .divider-line {
    flex: 1;
    height: 1rpx;
    background: linear-gradient(90deg, transparent 0%, #ddd 50%, transparent 100%);
  }
  
  .divider-text {
    padding: 0 24rpx;
    font-size: 26rpx;
    color: #999;
  }
}

.social-section {
  display: flex;
  gap: 24rpx;
}

.social-btn {
  flex: 1;
  height: 88rpx;
  border-radius: 16rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12rpx;
  transition: all 0.3s ease;
  
  &:active {
    transform: scale(0.98);
  }
  
  &.google {
    background: linear-gradient(135deg, #4285f4 0%, #3367d6 100%);
    box-shadow: 0 4rpx 16rpx rgba(66, 133, 244, 0.3);
  }
  
  &.phone {
    background: linear-gradient(135deg, #1890ff 0%, #096dd9 100%);
    box-shadow: 0 4rpx 16rpx rgba(24, 144, 255, 0.3);
  }
}

.social-icon {
  font-size: 32rpx;
  font-weight: bold;
}

.social-text {
  font-size: 28rpx;
  color: #fff;
  font-weight: 500;
}

.footer {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 32rpx;
  position: relative;
  z-index: 1;
  flex-shrink: 0;
}

.footer-text {
  font-size: 28rpx;
  color: rgba(255, 255, 255, 0.9);
}

.footer-link {
  font-size: 28rpx;
  color: #fff;
  margin-left: 12rpx;
  font-weight: 500;
}
</style>
