<template>
  <view class="edit-container">
    <view class="status-bar"></view>
    
    <view class="nav-bar">
      <view class="nav-left" @click="goBack">
        <FontAwesome name="arrow-left" size="24px" color="#fff" />
      </view>
      <view class="nav-center">
        <text class="nav-title">修改資料</text>
      </view>
      <view class="nav-right" @click="saveProfile">
        <text class="nav-save">儲存</text>
      </view>
    </view>
    
    <scroll-view class="content" scroll-y>
      <view class="form-section">
        <view class="avatar-row">
          <text class="form-label">頭像</text>
          <view class="avatar-preview">
            <image v-if="form.avatar_url" class="avatar-img" :src="form.avatar_url" mode="aspectFill" />
            <view class="avatar-placeholder" v-else>
              <text class="avatar-text">{{ form.name?.charAt(0) || '?' }}</text>
            </view>
            <view class="avatar-upload" @click="uploadAvatar">
              <FontAwesome name="camera" size="24px" color="#fff" />
            </view>
          </view>
        </view>
        
        <view class="form-item">
          <text class="form-label">暱稱</text>
          <input class="form-input" v-model="form.name" placeholder="請輸入暱稱" />
        </view>
        
        <view class="form-item">
          <text class="form-label">性別</text>
          <view class="gender-select">
            <view class="gender-option" :class="{ active: form.gender === 'male' }" @click="form.gender = 'male'">
              <text>男</text>
            </view>
            <view class="gender-option" :class="{ active: form.gender === 'female' }" @click="form.gender = 'female'">
              <text>女</text>
            </view>
          </view>
        </view>
        
        <view class="form-item">
          <text class="form-label">年齡</text>
          <input class="form-input" v-model="form.age" type="number" placeholder="請輸入年齡" />
        </view>
        
        <view class="form-item">
          <text class="form-label">身高</text>
          <input class="form-input" v-model="form.height" placeholder="請輸入身高(cm)" />
        </view>
        
        <view class="form-item">
          <text class="form-label">體重</text>
          <input class="form-input" v-model="form.weight" placeholder="請輸入體重(kg)" />
        </view>
        
        <view class="form-item">
          <text class="form-label">興趣愛好</text>
          <input class="form-input" v-model="form.hobbies" placeholder="請輸入興趣愛好" />
        </view>
        
        <view class="form-item">
          <text class="form-label">專長</text>
          <input class="form-input" v-model="form.specialty" placeholder="請輸入專長" />
        </view>
        
        <view class="form-item">
          <text class="form-label">愛情宣言</text>
          <textarea class="form-textarea" v-model="form.love_declaration" placeholder="請輸入你的愛情宣言" />
        </view>
      </view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { reactive, onMounted } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { getProfile, updateProfile, type UserProfile } from '../../api/user'

const form = reactive({
  name: '',
  avatar_url: '',
  gender: '',
  age: '',
  height: '',
  weight: '',
  hobbies: '',
  specialty: '',
  love_declaration: ''
})

onMounted(() => {
  loadProfile()
})

async function loadProfile() {
  try {
    const data = await getProfile()
    form.name = data.name || ''
    form.avatar_url = data.avatar_url || data.avatar || ''
    form.gender = data.gender || ''
    form.age = data.age?.toString() || ''
    form.height = data.height || ''
    form.weight = data.weight || ''
    form.hobbies = data.hobbies || ''
    form.specialty = data.specialty || ''
    form.love_declaration = data.love_declaration || ''
  } catch (error) {
    console.error('加載用戶資料失敗:', error)
  }
}

function uploadAvatar() {
  uni.chooseImage({
    count: 1,
    success: (res) => {
      const tempFilePath = res.tempFilePaths[0]
      form.avatar_url = tempFilePath
    }
  })
}

async function saveProfile() {
  try {
    const data: Partial<UserProfile> = {
      name: form.name,
      gender: form.gender,
      age: form.age ? parseInt(form.age) : undefined,
      height: form.height,
      weight: form.weight,
      hobbies: form.hobbies,
      specialty: form.specialty,
      love_declaration: form.love_declaration
    }
    
    const result = await updateProfile(data)
    uni.setStorageSync('user', JSON.stringify(result))
    uni.showToast({ title: '儲存成功', icon: 'success' })
    setTimeout(() => {
      uni.navigateBack()
    }, 1000)
  } catch (error) {
    console.error('儲存失敗:', error)
    uni.showToast({ title: '儲存失敗', icon: 'none' })
  }
}

function goBack() {
  uni.navigateBack()
}
</script>

<style lang="scss">
page {
  height: 100%;
  box-sizing: border-box;
}

*, *::before, *::after {
  box-sizing: border-box;
}

.edit-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background: #f5f5f5;
}

.status-bar {
  height: var(--status-bar-height, 44px);
}

.nav-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 16rpx 24rpx;
}

.nav-left, .nav-right {
  width: 80rpx;
  height: 80rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-title {
  font-size: 34rpx;
  color: #fff;
  font-weight: 600;
}

.nav-save {
  font-size: 30rpx;
  color: #fff;
}

.content {
  flex: 1;
  padding: 24rpx;
}

.form-section {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
}

.avatar-row {
  display: flex;
  align-items: center;
  margin-bottom: 32rpx;
}

.form-label {
  font-size: 30rpx;
  color: #333;
  width: 160rpx;
  flex-shrink: 0;
}

.avatar-preview {
  flex: 1;
  display: flex;
  justify-content: flex-end;
  position: relative;
}

.avatar-img {
  width: 120rpx;
  height: 120rpx;
  border-radius: 50%;
  border: 4rpx solid #ff6b9d;
}

.avatar-placeholder {
  width: 120rpx;
  height: 120rpx;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
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

.avatar-upload {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 40rpx;
  height: 40rpx;
  background: #ff6b9d;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2rpx solid #fff;
}

.form-item {
  display: flex;
  align-items: center;
  padding: 24rpx 0;
  border-bottom: 1rpx solid #f0f0f0;
  
  &:last-child {
    border-bottom: none;
  }
}

.form-input {
  flex: 1;
  text-align: right;
  font-size: 30rpx;
  color: #333;
}

.form-textarea {
  flex: 1;
  text-align: right;
  font-size: 30rpx;
  color: #333;
  height: 120rpx;
}

.gender-select {
  flex: 1;
  display: flex;
  justify-content: flex-end;
  gap: 24rpx;
}

.gender-option {
  padding: 12rpx 32rpx;
  border-radius: 24rpx;
  background: #f5f5f5;
  
  text {
    font-size: 28rpx;
    color: #666;
  }
  
  &.active {
    background: #ff6b9d;
    
    text {
      color: #fff;
    }
  }
}
</style>
