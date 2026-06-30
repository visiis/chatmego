<template>
  <view class="register-container">
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
    
    <scroll-view class="form-section" scroll-y>
      <view class="form-content">
        <view class="input-item">
          <view class="input-box">
            <FontAwesome class="input-icon" name="user-o" size="22px" color="#999" />
            <input 
              v-model="form.name" 
              class="input-field" 
              type="text" 
              placeholder="請輸入姓名"
              placeholder-class="input-placeholder"
            />
          </view>
        </view>
        
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
        
        <view class="input-item">
          <view class="input-box">
            <FontAwesome class="input-icon" name="lock" size="24px" color="#999" />
            <input 
              v-model="form.password_confirmation" 
              class="input-field" 
              :type="showConfirmPassword ? 'text' : 'password'" 
              placeholder="確認密碼"
              placeholder-class="input-placeholder"
            />
            <FontAwesome class="input-icon-right" :name="showConfirmPassword ? 'eye-slash' : 'eye'" size="24px" color="#999" @click="toggleConfirmPassword" />
          </view>
        </view>
        
        <view class="input-item">
          <view class="gender-section">
            <FontAwesome class="gender-icon" name="user" size="22px" color="#999" />
            <text class="gender-label">性別</text>
            <view class="gender-options">
              <view 
                class="gender-option" 
                :class="{ active: form.gender === 'male' }" 
                @click="selectGender('male')"
              >
                <FontAwesome name="male" size="40px" :color="form.gender === 'male' ? '#ff6b9d' : '#999'" />
              </view>
              <view 
                class="gender-option" 
                :class="{ active: form.gender === 'female' }" 
                @click="selectGender('female')"
              >
                <FontAwesome name="female" size="40px" :color="form.gender === 'female' ? '#ff6b9d' : '#999'" />
              </view>
            </view>
          </view>
          <text class="gender-hint">男性用戶將自動激活帳號，女性用戶需要後台審核</text>
        </view>
        
        <view class="agree-section">
          <view class="checkbox-wrapper" @click="toggleAgree">
            <view class="checkbox" :class="{ checked: form.agree }">
              <FontAwesome v-if="form.agree" name="check" size="20px" color="#fff" />
            </view>
            <text class="agree-text">我已閱讀並同意</text>
            <text class="agree-link">服務條款</text>
            <text class="agree-text">與</text>
            <text class="agree-link">隱私政策</text>
          </view>
        </view>
        
        <button class="register-btn" :disabled="loading" @click="handleRegister">
          <text v-if="loading">註冊中...</text>
          <view v-else class="btn-content">
            <FontAwesome name="user-plus" size="20px" color="#fff" />
            <text class="btn-text">註 冊</text>
          </view>
        </button>
      </view>
    </scroll-view>
    
    <view class="footer">
      <text class="footer-text">已有帳號？</text>
      <text class="footer-link" @click="goLogin">立即登入</text>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { register } from '../../api/auth'

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  gender: '',
  agree: false
})

const loading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

function togglePassword() {
  showPassword.value = !showPassword.value
}

function toggleConfirmPassword() {
  showConfirmPassword.value = !showConfirmPassword.value
}

function selectGender(gender: string) {
  form.gender = gender
}

function toggleAgree() {
  form.agree = !form.agree
}

function goLogin() {
  uni.redirectTo({
    url: '/pages/auth/login'
  })
}

async function handleRegister() {
  if (!form.name) {
    uni.showToast({ title: '請輸入姓名', icon: 'none' })
    return
  }
  
  if (!form.email) {
    uni.showToast({ title: '請輸入電子郵件', icon: 'none' })
    return
  }
  
  if (!form.password) {
    uni.showToast({ title: '請輸入密碼', icon: 'none' })
    return
  }
  
  if (form.password.length < 8) {
    uni.showToast({ title: '密碼至少需要八位', icon: 'none' })
    return
  }
  
  if (form.password !== form.password_confirmation) {
    uni.showToast({ title: '密碼不一致', icon: 'none' })
    return
  }
  
  if (!form.gender) {
    uni.showToast({ title: '請選擇性別', icon: 'none' })
    return
  }
  
  if (!form.agree) {
    uni.showToast({ title: '請同意服務條款', icon: 'none' })
    return
  }
  
  loading.value = true
  
  try {
    const data = await register({
      name: form.name,
      email: form.email,
      password: form.password,
      password_confirmation: form.password_confirmation,
      gender: form.gender
    })
    
    uni.setStorageSync('user', JSON.stringify(data.user))
    uni.setStorageSync('token', data.token)
    
    uni.showToast({ title: '註冊成功', icon: 'success' })
    
    setTimeout(() => {
      uni.reLaunch({ url: '/pages/auth/success' })
    }, 500)
  } catch (error) {
    uni.showToast({ title: (error as Error).message || '註冊失敗', icon: 'none' })
  } finally {
    loading.value = false
  }
}
</script>

<style lang="scss">
* {
  box-sizing: border-box;
}

page {
  margin: 0;
  padding: 0;
}

.register-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 40rpx;
  padding-bottom: calc(40rpx + env(safe-area-inset-bottom));
  box-sizing: border-box;
  position: relative;
  display: flex;
  flex-direction: column;
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
  padding: 40rpx 0 20rpx;
  position: relative;
  z-index: 1;
  flex-shrink: 0;
}

.logo-wrapper {
  width: 120rpx;
  height: 120rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-image {
  width: 90rpx;
  height: 90rpx;
}

.logo-title {
  font-size: 38rpx;
  font-weight: bold;
  color: #fff;
  margin-top: 16rpx;
  text-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.2);
}

.logo-subtitle {
  font-size: 24rpx;
  color: rgba(255, 255, 255, 0.9);
  margin-top: 12rpx;
}

.form-section {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 32rpx;
  box-shadow: 0 20rpx 60rpx rgba(0, 0, 0, 0.15);
  position: relative;
  z-index: 1;
  flex: 1;
  overflow: hidden;
}

.form-content {
  padding: 32rpx;
}

.input-item {
  margin-bottom: 28rpx;
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

.gender-section {
  display: flex;
  align-items: center;
  background: #f8f9fa;
  border-radius: 16rpx;
  border: 2rpx solid transparent;
  padding: 0 28rpx;
  transition: all 0.3s ease;
}

.gender-section:focus-within {
  border-color: #ff6b9d;
  background: #fff;
}

.gender-icon {
  margin-right: 16rpx;
}

.gender-label {
  font-size: 30rpx;
  color: #666;
  margin-right: 20rpx;
  flex-shrink: 0;
}

.gender-options {
  flex: 1;
  display: flex;
  justify-content: center;
  gap: 80rpx;
}

.gender-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20rpx 60rpx;
  border-radius: 16rpx;
  transition: all 0.3s ease;
  
  &.active {
    background: rgba(255, 107, 157, 0.1);
  }
}

.gender-hint {
  display: block;
  font-size: 22rpx;
  color: #999;
  margin-top: 16rpx;
  padding-left: 72rpx;
}

.agree-section {
  margin-bottom: 28rpx;
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
}

.checkbox {
  width: 36rpx;
  height: 36rpx;
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

.agree-text {
  font-size: 24rpx;
  color: #666;
  margin-left: 12rpx;
}

.agree-link {
  font-size: 24rpx;
  color: #ff6b9d;
}

.register-btn {
  width: 100%;
  height: 96rpx;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  border-radius: 16rpx;
  border: none;
  margin-top: 16rpx;
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

.footer {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 24rpx;
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
