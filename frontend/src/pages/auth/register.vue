<template>
  <view class="register-page">
    <view class="bg-container">
      <image class="bg-image" src="https://images.unsplash.com/photo-1504215680853-026ed2a45def?w=1080" mode="aspectFill" />
      <view class="bg-overlay"></view>
    </view>
    
    <view class="register-form">
      <view class="header">
        <text class="back-btn" @click="goBack">←</text>
        <text class="page-title">注册</text>
        <view class="placeholder"></view>
      </view>
      
      <view class="form-group">
        <view class="input-wrapper">
          <text class="input-icon">📱</text>
          <input 
            class="form-input" 
            v-model="form.phone" 
            placeholder="请输入手机号" 
            type="number"
          />
        </view>
      </view>
      
      <view class="form-group">
        <view class="input-wrapper">
          <text class="input-icon">🔢</text>
          <input 
            class="form-input" 
            v-model="form.code" 
            placeholder="请输入验证码" 
            type="number"
          />
          <text class="send-code-btn" :class="{ disabled: !canSendCode }" @click="sendCode">
            {{ countdown > 0 ? `${countdown}s` : '发送验证码' }}
          </text>
        </view>
      </view>
      
      <view class="form-group">
        <view class="input-wrapper">
          <text class="input-icon">🔒</text>
          <input 
            class="form-input" 
            v-model="form.password" 
            placeholder="请设置密码" 
            :type="showPassword ? 'text' : 'password'"
          />
          <text class="toggle-password" @click="showPassword = !showPassword">
            {{ showPassword ? '🙈' : '👁️' }}
          </text>
        </view>
      </view>
      
      <view class="form-group">
        <view class="input-wrapper">
          <text class="input-icon">👤</text>
          <input 
            class="form-input" 
            v-model="form.nickname" 
            placeholder="请输入昵称" 
          />
        </view>
      </view>
      
      <view class="form-group">
        <view class="input-wrapper">
          <text class="input-icon">🎂</text>
          <picker mode="date" @change="onBirthdayChange">
            <view class="picker-value">
              <text>{{ form.birthday || '请选择生日' }}</text>
            </view>
          </picker>
        </view>
      </view>
      
      <view class="form-group">
        <view class="input-wrapper">
          <text class="input-icon">👫</text>
          <picker mode="selector" :range="genderOptions" @change="onGenderChange">
            <view class="picker-value">
              <text>{{ genderOptions[form.gender] || '请选择性别' }}</text>
            </view>
          </picker>
        </view>
      </view>
      
      <view class="form-group">
        <view class="input-wrapper">
          <text class="input-icon">🎁</text>
          <input 
            class="form-input" 
            v-model="form.inviteCode" 
            placeholder="邀请码（选填）" 
          />
        </view>
      </view>
      
      <view class="agree-terms">
        <text class="checkbox" :class="{ checked: form.agree }" @click="form.agree = !form.agree">
          {{ form.agree ? '✓' : '' }}
        </text>
        <text class="agree-text">我已阅读并同意</text>
        <text class="link-text">《用户协议》</text>
        <text class="agree-text">和</text>
        <text class="link-text">《隐私政策》</text>
      </view>
      
      <view class="btn-register" @click="handleRegister">
        <text class="btn-text">注册</text>
      </view>
      
      <view class="login-link">
        <text>已有账号? </text>
        <text class="link-text" @click="goToLogin">立即登录</text>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useUserStore } from '@/stores/user'
import { sendCode, register } from '@/api/auth'

const userStore = useUserStore()
const showPassword = ref(false)
const countdown = ref(0)

const genderOptions = ['女', '男']

const form = reactive({
  phone: '',
  code: '',
  password: '',
  nickname: '',
  birthday: '',
  gender: 0,
  inviteCode: '',
  agree: false
})

const canSendCode = ref(true)

function onBirthdayChange(e: any) {
  form.birthday = e.detail.value
}

function onGenderChange(e: any) {
  form.gender = e.detail.value
}

async function sendCode() {
  if (!form.phone) {
    uni.showToast({ title: '请输入手机号', icon: 'none' })
    return
  }
  
  canSendCode.value = false
  countdown.value = 60
  
  try {
    await sendCode(form.phone, 'register')
    uni.showToast({ title: '验证码已发送', icon: 'success' })
    
    const timer = setInterval(() => {
      countdown.value--
      if (countdown.value <= 0) {
        clearInterval(timer)
        canSendCode.value = true
      }
    }, 1000)
  } catch (e: any) {
    uni.showToast({ title: e.message || '发送失败', icon: 'none' })
    canSendCode.value = true
    countdown.value = 0
  }
}

async function handleRegister() {
  if (!form.phone) {
    uni.showToast({ title: '请输入手机号', icon: 'none' })
    return
  }
  if (!form.code) {
    uni.showToast({ title: '请输入验证码', icon: 'none' })
    return
  }
  if (!form.password) {
    uni.showToast({ title: '请设置密码', icon: 'none' })
    return
  }
  if (!form.nickname) {
    uni.showToast({ title: '请输入昵称', icon: 'none' })
    return
  }
  if (!form.birthday) {
    uni.showToast({ title: '请选择生日', icon: 'none' })
    return
  }
  if (!form.agree) {
    uni.showToast({ title: '请同意用户协议', icon: 'none' })
    return
  }

  try {
    const response = await register({
      phone: form.phone,
      code: form.code,
      password: form.password,
      nickname: form.nickname,
      gender: form.gender + 1,
      birthday: form.birthday,
      invite_code: form.inviteCode || undefined
    })
    
    userStore.setUser(response.data.user)
    userStore.setToken(response.data.user.token)
    userStore.setImInfo(response.data.im.account, response.data.im.token)
    
    uni.showToast({ title: '注册成功', icon: 'success' })
    setTimeout(() => {
      uni.switchTab({ url: '/pages/discover/index' })
    }, 1500)
  } catch (e: any) {
    uni.showToast({ title: e.message || '注册失败', icon: 'none' })
  }
}

function goBack() {
  uni.navigateBack()
}

function goToLogin() {
  uni.navigateTo({ url: '/pages/auth/login' })
}
</script>

<style lang="scss" scoped>
.register-page {
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

.register-form {
  position: relative;
  z-index: 10;
  padding: 60rpx 40rpx;
  padding-top: 160rpx;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 60rpx;
}

.back-btn {
  font-size: 48rpx;
  color: #fff;
  width: 80rpx;
}

.page-title {
  font-size: 36rpx;
  font-weight: bold;
  color: #fff;
}

.placeholder {
  width: 80rpx;
}

.form-group {
  margin-bottom: 28rpx;
}

.input-wrapper {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 40rpx;
  padding: 0 32rpx;
  display: flex;
  align-items: center;
  height: 96rpx;
}

.input-icon {
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
}

.send-code-btn {
  font-size: 28rpx;
  color: #f87c7c;
  padding: 10rpx 20rpx;
  border-left: 2rpx solid #eee;
  
  &.disabled {
    color: #ccc;
  }
}

.picker-value {
  flex: 1;
  font-size: 32rpx;
  color: #333;
}

.agree-terms {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  padding: 20rpx 0;
  margin-bottom: 32rpx;
}

.checkbox {
  width: 36rpx;
  height: 36rpx;
  border: 2rpx solid rgba(255, 255, 255, 0.6);
  border-radius: 8rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12rpx;
  color: #fff;
  font-size: 22rpx;
  
  &.checked {
    background: #f87c7c;
    border-color: #f87c7c;
  }
}

.agree-text {
  font-size: 24rpx;
  color: rgba(255, 255, 255, 0.8);
}

.link-text {
  font-size: 24rpx;
  color: #fff;
}

.btn-register {
  background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
  border-radius: 40rpx;
  height: 96rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 40rpx;
  box-shadow: 0 8rpx 24rpx rgba(248, 124, 124, 0.4);
}

.btn-text {
  font-size: 34rpx;
  font-weight: bold;
  color: #fff;
}

.login-link {
  text-align: center;
  font-size: 28rpx;
  color: rgba(255, 255, 255, 0.8);
}
</style>
