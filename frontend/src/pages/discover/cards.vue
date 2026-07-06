<template>
  <view class="container">
    <view class="search-section">
      <view class="search-bar">
        <FontAwesome name="search" size="24px" color="#999" />
        <input 
          class="search-input" 
          v-model="searchKeyword" 
          placeholder="搜索会员" 
          @confirm="onSearch"
          @input="onSearch"
        />
        <view class="search-clear" v-if="searchKeyword" @click.stop="clearSearch">
          <FontAwesome name="xmark" size="20px" color="#999" />
        </view>
      </view>
    </view>
    
    <scroll-view 
      class="user-scroll"
      scroll-y
      :refresher-enabled="true"
      @refresherrefresh="onRefresh"
      :refresher-triggered="refreshing"
      :show-scrollbar="false"
    >
      <view class="card-list">
        <view 
          class="user-card" 
          v-for="user in users" 
          :key="user.id" 
        >
          <view class="card-photo-area">
            <swiper 
              v-if="user.photos && user.photos.length > 0"
              class="photo-swiper"
              :indicator-dots="user.photos.length > 1"
              :circular="true"
              :autoplay="false"
              :duration="300"
              indicator-color="rgba(255,255,255,0.4)"
              indicator-active-color="#fff"
              @click="previewPhoto(user)"
            >
              <swiper-item v-for="(photo, index) in user.photos.slice(0, 4)" :key="index">
                <image 
                  class="card-photo" 
                  :src="photo.url" 
                  mode="aspectFill"
                />
              </swiper-item>
            </swiper>
            <view class="no-photo-bg" v-else>
              <FontAwesome name="image" size="80px" color="#ddd" />
            </view>
            
            <view class="card-overlay">
              <view class="card-info">
                <view class="avatar-wrap">
                  <image 
                    class="user-avatar" 
                    :src="user.avatar" 
                    mode="aspectFill"
                  />
                  <view class="gender-tag" :class="user.gender === 'male' ? 'male' : 'female'">
                    <FontAwesome :name="user.gender === 'male' ? 'mars' : 'venus'" size="12px" color="#fff" />
                  </view>
                </view>
                <view class="name-section">
                  <text class="user-name">{{ user.name }}</text>
                  <view class="user-meta">
                    <text v-if="user.age">{{ user.age }}岁</text>
                    <text v-if="user.age && user.height" class="meta-dot">·</text>
                    <text v-if="user.height">{{ user.height }}cm</text>
                    <text v-if="(user.age || user.height) && user.weight" class="meta-dot">·</text>
                    <text v-if="user.weight">{{ user.weight }}kg</text>
                  </view>
                </view>
              </view>
            </view>
            
            <view class="vip-badge" v-if="user.is_vip">
              <FontAwesome name="star" size="18px" color="#ffd700" />
            </view>
          </view>
          
          <view class="card-content">
            <text class="love-declaration" v-if="user.love_declaration">{{ user.love_declaration }}</text>
            <text class="love-declaration placeholder" v-else>暂无爱情宣言</text>
            
            <view class="user-actions">
              <view 
                class="action-btn attraction"
                :class="{ liked: getUserStatus(user.id)?.my_type === 1, disliked: getUserStatus(user.id)?.my_type === 2 }"
                @click="handleAttraction(user)"
              >
                <FontAwesome name="heart" size="28px" :color="getUserStatus(user.id)?.my_type === 1 ? '#ff6b9d' : getUserStatus(user.id)?.my_type === 2 ? '#ff4757' : '#999'" />
                <text>{{ getUserStatus(user.id)?.my_type === 1 ? '已赞' : getUserStatus(user.id)?.my_type === 2 ? '已踩' : '好感' }}</text>
              </view>
              <view class="action-btn chat" @click="startChat(user)">
                <FontAwesome name="comment" size="28px" color="#4a90d9" />
                <text>聊天</text>
              </view>
              <view class="action-btn view" @click="viewProfile(user)">
                <FontAwesome name="user-circle" size="28px" color="#999" />
                <text>查看</text>
              </view>
            </view>
          </view>
        </view>
      </view>
      
      <view class="empty-state" v-if="users.length === 0 && !loading">
        <FontAwesome name="users" size="80px" color="#ddd" />
        <text class="empty-text">暂无会员</text>
      </view>
      
      <view class="loading-state" v-if="loading">
        <view class="loading-spinner"></view>
        <text class="loading-text">加载中...</text>
      </view>
      
      <view class="bottom-space"></view>
    </scroll-view>
    
    <TabBar />
  </view>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import FontAwesome from '../../components/FontAwesome.vue'
import TabBar from '../../components/TabBar.vue'
import { request } from '../../utils/request'
import { 
  likeUser as attractionLike, 
  dislikeUser as attractionDislike, 
  cancelAttraction,
  getAttractionStatus,
  type AttractionStatus 
} from '../../api/attraction'

interface UserPhoto {
  id?: number
  url: string
  blur_url?: string
  is_main?: number
  is_premium?: boolean
  points_price?: number
}

interface UserItem {
  id: number
  name: string
  avatar: string
  gender: string
  age: number | null
  height: string | null
  weight: string | null
  hobbies: string | null
  love_declaration: string | null
  is_vip: number
  photos: UserPhoto[]
}

const searchKeyword = ref('')
const loading = ref(false)
const refreshing = ref(false)
const users = ref<UserItem[]>([])
const attractionStatuses = reactive<Record<number, AttractionStatus>>({})
const defaultAvatar = 'https://chatmego.com/images/default-avatar.svg'

async function loadUsers(refresh = false) {
  if (loading.value) return
  
  loading.value = true
  try {
    const token = uni.getStorageSync('token')
    const search = searchKeyword.value.trim()
    const url = search ? `/api/discover/cards?search=${encodeURIComponent(search)}` : '/api/discover/cards'
    const response = await request<{ code: number; message: string; data: UserItem[] }>(
      url,
      'GET',
      undefined,
      { 'Authorization': 'Bearer ' + token }
    )
    
    if (response.code === 200) {
      users.value = response.data
      
      const statusPromises = users.value.map(user => {
        return getAttractionStatus(user.id).then(status => {
          attractionStatuses[user.id] = status
        }).catch(() => {})
      })
      
      await Promise.all(statusPromises)
    }
  } catch (error) {
    console.error('加载用户失败:', error)
    uni.showToast({ title: '加载失败', icon: 'none' })
  } finally {
    loading.value = false
    refreshing.value = false
  }
}

async function handleAttraction(user: UserItem) {
  const currentStatus = getUserStatus(user.id)
  const currentType = currentStatus?.my_type || 0
  
  const options = ['赞', '踩']
  
  uni.showActionSheet({
    itemList: options,
    success: async (res) => {
      try {
        if (res.tapIndex === 0) {
          if (currentType === 1) {
            await cancelAttraction(user.id)
            if (attractionStatuses[user.id]) {
              attractionStatuses[user.id].my_type = 0
            }
            uni.showToast({ title: '已取消', icon: 'none' })
          } else {
            const result = await attractionLike(user.id)
            if (!attractionStatuses[user.id]) {
              attractionStatuses[user.id] = { my_type: 0, their_type: 0, is_mutual: false }
            }
            attractionStatuses[user.id].my_type = 1
            attractionStatuses[user.id].is_mutual = result.data?.is_mutual || false
            uni.showToast({ title: '已赞', icon: 'success' })
          }
        } else {
          if (currentType === 2) {
            await cancelAttraction(user.id)
            if (attractionStatuses[user.id]) {
              attractionStatuses[user.id].my_type = 0
            }
            uni.showToast({ title: '已取消', icon: 'none' })
          } else {
            await attractionDislike(user.id)
            if (!attractionStatuses[user.id]) {
              attractionStatuses[user.id] = { my_type: 0, their_type: 0, is_mutual: false }
            }
            attractionStatuses[user.id].my_type = 2
            uni.showToast({ title: '已踩', icon: 'none' })
          }
        }
      } catch (error) {
        console.error('操作失败:', error)
        uni.showToast({ title: '操作失败', icon: 'none' })
      }
    }
  })
}

function getUserStatus(userId: number): AttractionStatus | undefined {
  return attractionStatuses[userId]
}

function viewProfile(user: UserItem) {
  uni.navigateTo({ 
    url: `/pages/profile/user-profile?id=${user.id}&name=${encodeURIComponent(user.name)}&avatar=${encodeURIComponent(user.avatar)}` 
  })
}

function startChat(user: UserItem) {
  uni.navigateTo({ 
    url: `/pages/messages/detail?id=${user.id}&name=${encodeURIComponent(user.name)}&avatar=${encodeURIComponent(user.avatar)}` 
  })
}

function previewPhoto(user: UserItem) {
  if (!user.photos || user.photos.length === 0) {
    uni.showToast({ title: '暂无照片', icon: 'none' })
    return
  }
  const urls = user.photos.map(p => p.url)
  uni.previewImage({
    urls,
    current: urls[0]
  })
}

function onRefresh() {
  refreshing.value = true
  loadUsers()
}

function onSearch() {
  loadUsers()
}

function clearSearch() {
  searchKeyword.value = ''
  loadUsers()
}

onShow(() => {
  loadUsers()
})

onMounted(() => {
  const token = uni.getStorageSync('token')
  if (!token) {
    uni.reLaunch({ url: '/pages/auth/login' })
    return
  }
  loadUsers()
})
</script>

<style lang="scss">
page {
  height: 100%;
  background: #f5f5f5;
}

.container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  height: 100dvh;
}

.search-section {
  padding: 20rpx;
  background: #fff;
}

.search-bar {
  display: flex;
  align-items: center;
  background: #f5f5f5;
  border-radius: 32rpx;
  padding: 16rpx 24rpx;
}

.search-input {
  flex: 1;
  font-size: 28rpx;
  padding: 0 16rpx;
  color: #333;
  background: transparent;
}

.search-input::placeholder {
  color: #999;
}

.search-clear {
  padding: 8rpx;
}

.user-scroll {
  flex: 1;
  padding-bottom: calc(120rpx + env(safe-area-inset-bottom));
}

.card-list {
  padding: 20rpx;
}

.user-card {
  background: #fff;
  border-radius: 24rpx;
  overflow: hidden;
  margin-bottom: 30rpx;
  box-shadow: 0 8rpx 32rpx rgba(0, 0, 0, 0.08);
}

.card-photo-area {
  position: relative;
  width: 100%;
  height: 650rpx;
  background: #f0f0f0;
}

.photo-swiper {
  width: 100%;
  height: 100%;
}

.card-photo {
  width: 100%;
  height: 100%;
}

.no-photo-bg {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
}

.card-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.6));
  padding: 60rpx 30rpx 30rpx;
}

.card-info {
  display: flex;
  align-items: flex-end;
  gap: 20rpx;
}

.avatar-wrap {
  position: relative;
  flex-shrink: 0;
}

.user-avatar {
  width: 88rpx;
  height: 88rpx;
  border-radius: 50%;
  border: 4rpx solid #fff;
}

.gender-tag {
  position: absolute;
  bottom: -4rpx;
  right: -4rpx;
  width: 32rpx;
  height: 32rpx;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2rpx solid #fff;
  
  &.male {
    background: #4a90d9;
  }
  
  &.female {
    background: #ff6b9d;
  }
}

.name-section {
  flex: 1;
}

.user-name {
  display: block;
  font-size: 40rpx;
  color: #fff;
  font-weight: 600;
  text-shadow: 0 2rpx 8rpx rgba(0, 0, 0, 0.3);
}

.user-meta {
  display: flex;
  align-items: center;
  font-size: 24rpx;
  color: rgba(255, 255, 255, 0.85);
  margin-top: 8rpx;
}

.meta-dot {
  margin: 0 8rpx;
}

.vip-badge {
  position: absolute;
  top: 24rpx;
  right: 24rpx;
  width: 56rpx;
  height: 56rpx;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.card-content {
  padding: 30rpx;
}

.love-declaration {
  font-size: 28rpx;
  color: #666;
  line-height: 1.6;
  margin-bottom: 30rpx;
  
  &.placeholder {
    color: #ccc;
    font-style: italic;
  }
}

.user-actions {
  display: flex;
  gap: 24rpx;
}

.action-btn {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8rpx;
  padding: 20rpx 16rpx;
  border-radius: 16rpx;
  
  text {
    font-size: 24rpx;
    color: #666;
  }
  
  &.attraction {
    background: rgba(153, 153, 153, 0.1);
    text { color: #999; }
  }
  
  &.liked {
    background: rgba(255, 107, 157, 0.1);
    text { color: #ff6b9d; }
  }
  
  &.disliked {
    background: rgba(255, 71, 87, 0.1);
    text { color: #ff4757; }
  }
  
  &.chat {
    background: rgba(74, 144, 217, 0.1);
    text { color: #4a90d9; }
  }
  
  &.view {
    background: rgba(153, 153, 153, 0.1);
    text { color: #999; }
  }
  
  &:active {
    opacity: 0.7;
    transform: scale(0.95);
  }
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 160rpx 0;
}

.empty-text {
  font-size: 32rpx;
  color: #999;
  margin-top: 32rpx;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 60rpx 0;
}

.loading-spinner {
  width: 56rpx;
  height: 56rpx;
  border: 4rpx solid rgba(255, 107, 157, 0.2);
  border-top-color: #ff6b9d;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.loading-text {
  font-size: 28rpx;
  color: #999;
  margin-top: 16rpx;
}

.bottom-space {
  height: 40rpx;
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
