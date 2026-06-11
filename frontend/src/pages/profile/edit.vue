<template>
  <view class="edit-profile-page">
    <view class="header">
      <text class="back-btn" @click="goBack">←</text>
      <text class="page-title">编辑资料</text>
      <text class="save-btn" @click="saveProfile">保存</text>
    </view>
    
    <view class="form-section">
      <view class="avatar-section">
        <text class="section-label">头像</text>
        <view class="avatar-edit">
          <image 
            class="avatar" 
            :src="form.avatar || 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20person&image_size=square'" 
            mode="aspectFill" 
          />
          <view class="upload-btn" @click="uploadAvatar">
            <text class="upload-icon">📷</text>
          </view>
        </view>
      </view>
      
      <view class="form-item">
        <text class="form-label">昵称</text>
        <input 
          class="form-input" 
          v-model="form.nickname" 
          placeholder="请输入昵称" 
        />
      </view>
      
      <view class="form-item">
        <text class="form-label">性别</text>
        <picker mode="selector" :range="genderOptions" @change="onGenderChange">
          <view class="picker-value">
            <text>{{ genderOptions[form.gender] || '请选择' }}</text>
            <text class="picker-arrow">→</text>
          </view>
        </picker>
      </view>
      
      <view class="form-item">
        <text class="form-label">生日</text>
        <picker mode="date" @change="onBirthdayChange">
          <view class="picker-value">
            <text>{{ form.birthday || '请选择' }}</text>
            <text class="picker-arrow">→</text>
          </view>
        </picker>
      </view>
      
      <view class="form-item">
        <text class="form-label">身高</text>
        <view class="height-input">
          <input 
            class="form-input short" 
            v-model="form.height" 
            placeholder="身高" 
            type="number"
          />
          <text class="unit">cm</text>
        </view>
      </view>
      
      <view class="form-item">
        <text class="form-label">体重</text>
        <view class="height-input">
          <input 
            class="form-input short" 
            v-model="form.weight" 
            placeholder="体重" 
            type="number"
          />
          <text class="unit">kg</text>
        </view>
      </view>
      
      <view class="form-item">
        <text class="form-label">所在地</text>
        <picker mode="region" @change="onLocationChange">
          <view class="picker-value">
            <text>{{ form.location || '请选择' }}</text>
            <text class="picker-arrow">→</text>
          </view>
        </picker>
      </view>
      
      <view class="form-item">
        <text class="form-label">恋爱宣言</text>
        <textarea 
          class="form-textarea" 
          v-model="form.love_declaration" 
          placeholder="写下你的恋爱宣言..."
          :maxlength="200"
        />
        <text class="word-count">{{ form.love_declaration.length }}/200</text>
      </view>
      
      <view class="form-item">
        <text class="form-label">兴趣爱好</text>
        <view class="hobbies-input">
          <scroll-view scroll-x class="hobbies-scroll">
            <view class="hobbies-list">
              <view 
                class="hobby-tag" 
                v-for="hobby in selectedHobbies" 
                :key="hobby"
              >
                <text>{{ hobby }}</text>
                <text class="remove-tag" @click="removeHobby(hobby)">✕</text>
              </view>
            </view>
          </scroll-view>
          <view class="add-hobby" @click="showHobbyPicker = true">
            <text>+ 添加爱好</text>
          </view>
        </view>
      </view>
    </view>
    
    <view class="hobby-picker" v-if="showHobbyPicker">
      <view class="picker-header">
        <text class="picker-title">选择爱好</text>
        <text class="picker-close" @click="showHobbyPicker = false">✕</text>
      </view>
      <view class="hobby-options">
        <view 
          class="hobby-option" 
          v-for="hobby in hobbyOptions" 
          :key="hobby"
          :class="{ selected: selectedHobbies.includes(hobby) }"
          @click="toggleHobby(hobby)"
        >
          <text>{{ hobby }}</text>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useUserStore } from '@/stores/user'
import { updateProfile } from '@/api/user'

const userStore = useUserStore()

const genderOptions = ['女', '男']
const hobbyOptions = [
  '旅游', '美食', '健身', '音乐', '电影', '阅读', '游戏', '运动',
  '摄影', '绘画', '唱歌', '跳舞', '烹饪', '宠物', '摄影', '科技',
  '艺术', '读书', '写作', '旅行', '美食', '咖啡', '手工', '户外'
]

const showHobbyPicker = ref(false)

const form = reactive({
  avatar: '',
  nickname: '',
  gender: 0,
  birthday: '',
  height: '',
  weight: '',
  location: '',
  love_declaration: '',
  hobbies: []
})

const selectedHobbies = ref<string[]>([])

function onGenderChange(e: any) {
  form.gender = e.detail.value
}

function onBirthdayChange(e: any) {
  form.birthday = e.detail.value
}

function onLocationChange(e: any) {
  form.location = e.detail.value.join(' ')
}

function uploadAvatar() {
  uni.chooseImage({
    count: 1,
    success: (res) => {
      form.avatar = res.tempFilePaths[0]
      uni.showToast({ title: '头像上传成功', icon: 'success' })
    }
  })
}

function toggleHobby(hobby: string) {
  const index = selectedHobbies.value.indexOf(hobby)
  if (index > -1) {
    selectedHobbies.value.splice(index, 1)
  } else if (selectedHobbies.value.length < 6) {
    selectedHobbies.value.push(hobby)
  } else {
    uni.showToast({ title: '最多选择6个爱好', icon: 'none' })
  }
}

function removeHobby(hobby: string) {
  const index = selectedHobbies.value.indexOf(hobby)
  if (index > -1) {
    selectedHobbies.value.splice(index, 1)
  }
}

async function saveProfile() {
  if (!form.nickname) {
    uni.showToast({ title: '请输入昵称', icon: 'none' })
    return
  }
  
  try {
    await updateProfile({
      nickname: form.nickname,
      gender: form.gender + 1,
      birthday: form.birthday,
      height: parseInt(form.height) || 0,
      weight: parseInt(form.weight) || 0,
      location: form.location,
      love_declaration: form.love_declaration,
      hobbies: selectedHobbies.value.join(',')
    })
    
    await userStore.fetchUserProfile()
    uni.showToast({ title: '保存成功', icon: 'success' })
    setTimeout(() => {
      uni.navigateBack()
    }, 1500)
  } catch (e: any) {
    uni.showToast({ title: e.message || '保存失败', icon: 'none' })
  }
}

function goBack() {
  uni.navigateBack()
}
</script>

<style lang="scss" scoped>
.edit-profile-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 40rpx;
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

.save-btn {
  font-size: 30rpx;
  color: #fff;
  font-weight: bold;
}

.form-section {
  background: #fff;
  margin: 24rpx;
  border-radius: 16rpx;
  padding: 24rpx;
}

.avatar-section {
  margin-bottom: 32rpx;
}

.section-label {
  display: block;
  font-size: 26rpx;
  color: #999;
  margin-bottom: 16rpx;
}

.avatar-edit {
  display: flex;
  align-items: center;
}

.avatar {
  width: 120rpx;
  height: 120rpx;
  border-radius: 50%;
  margin-right: 20rpx;
}

.upload-btn {
  width: 64rpx;
  height: 64rpx;
  background: #f5f5f5;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.upload-icon {
  font-size: 28rpx;
}

.form-item {
  padding: 24rpx 0;
  border-bottom: 1rpx solid #f8f8f8;
  
  &:last-child {
    border-bottom: none;
  }
}

.form-label {
  display: block;
  font-size: 28rpx;
  color: #333;
  margin-bottom: 16rpx;
}

.form-input {
  width: 100%;
  height: 80rpx;
  font-size: 30rpx;
  color: #333;
  background: #f8f8f8;
  border-radius: 12rpx;
  padding: 0 20rpx;
  
  &.short {
    width: 200rpx;
  }
}

.form-textarea {
  width: 100%;
  height: 160rpx;
  font-size: 30rpx;
  color: #333;
  background: #f8f8f8;
  border-radius: 12rpx;
  padding: 20rpx;
}

.word-count {
  display: block;
  text-align: right;
  font-size: 22rpx;
  color: #999;
  margin-top: 8rpx;
}

.picker-value {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 80rpx;
  background: #f8f8f8;
  border-radius: 12rpx;
  padding: 0 20rpx;
  
  text {
    font-size: 30rpx;
    color: #333;
  }
}

.picker-arrow {
  color: #999;
}

.height-input {
  display: flex;
  align-items: center;
}

.unit {
  margin-left: 12rpx;
  font-size: 28rpx;
  color: #666;
}

.hobbies-input {
  margin-top: 16rpx;
}

.hobbies-scroll {
  white-space: nowrap;
  margin-bottom: 16rpx;
}

.hobbies-list {
  display: inline-flex;
  gap: 12rpx;
}

.hobby-tag {
  display: inline-flex;
  align-items: center;
  padding: 12rpx 20rpx;
  background: #f87c7c;
  border-radius: 20rpx;
  
  text {
    font-size: 24rpx;
    color: #fff;
  }
}

.remove-tag {
  margin-left: 8rpx;
  font-size: 20rpx;
}

.add-hobby {
  padding: 16rpx 24rpx;
  border: 2rpx dashed #ddd;
  border-radius: 12rpx;
  text-align: center;
  
  text {
    font-size: 26rpx;
    color: #999;
  }
}

.hobby-picker {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: #fff;
  border-radius: 32rpx 32rpx 0 0;
  padding-bottom: constant(safe-area-inset-bottom);
  padding-bottom: env(safe-area-inset-bottom);
  z-index: 100;
}

.picker-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 32rpx;
  border-bottom: 1rpx solid #eee;
}

.picker-title {
  font-size: 34rpx;
  font-weight: bold;
}

.picker-close {
  font-size: 36rpx;
  color: #999;
}

.hobby-options {
  display: flex;
  flex-wrap: wrap;
  padding: 24rpx;
}

.hobby-option {
  padding: 16rpx 28rpx;
  background: #f5f5f5;
  border-radius: 30rpx;
  margin-right: 16rpx;
  margin-bottom: 16rpx;
  
  text {
    font-size: 26rpx;
    color: #666;
  }
  
  &.selected {
    background: #f87c7c;
    
    text {
      color: #fff;
    }
  }
}
</style>
