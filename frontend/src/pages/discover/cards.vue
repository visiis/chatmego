<template>
  <view class="container">
    <view class="search-section">
      <view class="search-bar" @click="focusSearch">
        <FontAwesome name="search" size="24px" color="#999" />
        <input 
          class="search-input" 
          v-model="searchKeyword" 
          placeholder="發現好友" 
          @confirm="onSearch"
          @input="onSearch"
        />
        <view class="search-clear" v-if="searchKeyword" @click.stop="clearSearch">
          <FontAwesome name="times" size="20px" color="#999" />
        </view>
      </view>
    </view>
    
    <scroll-view 
      class="user-list"
      scroll-y
      :refresher-enabled="true"
      @refresherrefresh="onRefresh"
      :refresher-triggered="refreshing"
    >
      <view class="user-grid">
        <view 
          class="user-card" 
          v-for="user in users" 
          :key="user.id" 
        >
          <view class="card-avatar-wrap">
            <image 
              class="card-avatar" 
              :src="user.avatar || defaultAvatar" 
              mode="aspectFill"
              @error="onAvatarError($event, user)"
            />
            <view class="gender-tag" :class="user.gender === 'male' ? 'male' : 'female'">
              <FontAwesome :name="user.gender === 'male' ? 'mars' : 'venus'" size="12px" color="#fff" />
            </view>
            <view class="vip-tag" v-if="user.is_vip">VIP</view>
            <view class="mutual-tag" v-if="getUserStatus(user.id)?.is_mutual">
              <FontAwesome name="heart" size="12px" color="#ff6b9d" />
            </view>
          </view>
          
          <view class="card-content">
            <text class="card-name">{{ user.name }}</text>
            
            <view class="card-meta" v-if="user.age || user.height">
              <text v-if="user.age">{{ user.age }}歲</text>
              <text v-if="user.age && user.height" class="meta-dot">·</text>
              <text v-if="user.height">{{ user.height }}cm</text>
            </view>
            
            <text class="card-declaration" v-if="user.love_declaration">{{ user.love_declaration }}</text>
          </view>
          
          <view class="card-actions">
            <view 
              class="action-btn attraction"
              :class="{ liked: getUserStatus(user.id)?.my_type === 1, disliked: getUserStatus(user.id)?.my_type === 2 }"
              @click="handleAttraction(user)"
            >
              <FontAwesome name="heart" size="24px" :color="getUserStatus(user.id)?.my_type === 1 ? '#ff6b9d' : getUserStatus(user.id)?.my_type === 2 ? '#ff4757' : '#999'" />
              <text>{{ getUserStatus(user.id)?.my_type === 1 ? '已讚' : getUserStatus(user.id)?.my_type === 2 ? '已踩' : '好感' }}</text>
            </view>
            <view class="action-btn chat" @click="startChat(user)">
              <FontAwesome name="comment" size="24px" color="#4a90d9" />
              <text>聊天</text>
            </view>
            <view class="action-btn view" @click="viewProfile(user)">
              <FontAwesome name="user-circle" size="24px" color="#999" />
              <text>查看</text>
            </view>
          </view>
        </view>
      </view>
      
      <view class="empty-state" v-if="users.length === 0 && !loading">
        <FontAwesome name="users" size="60px" color="#ddd" />
        <text class="empty-text">暫時沒有更多用戶</text>
      </view>
      
      <view class="loading-more" v-if="loading">
        <text class="loading-text">加載中...</text>
      </view>
    </scroll-view>
    
    <view class="bottom-tab">
      <view class="bottom-tab-item active">
        <FontAwesome name="compass" size="24px" color="#ff6b9d" />
        <text class="tab-text active">發現</text>
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
      <view class="bottom-tab-item" @click="goProfile">
        <FontAwesome name="user" size="24px" color="#999" />
        <text class="tab-text">我的</text>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { request } from '../../utils/request'
import { 
  likeUser, 
  dislikeUser, 
  cancelAttraction,
  getAttractionStatus,
  type AttractionStatus 
} from '../../api/attraction'

interface User {
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
}

const users = ref<User[]>([])
const loading = ref(false)
const refreshing = ref(false)
const defaultAvatar = 'https://chatmego.com/images/default-avatar.svg'
const searchKeyword = ref('')

const attractionStatuses = reactive<Record<number, AttractionStatus>>({})

function onAvatarError(e: any, user: User) {
  user.avatar = defaultAvatar
}

function getUserStatus(userId: number): AttractionStatus | undefined {
  return attractionStatuses[userId]
}

async function loadUsers(refresh = false) {
  if (loading.value) return
  
  loading.value = true
  try {
    const token = uni.getStorageSync('token')
    const search = searchKeyword.value.trim()
    const url = search ? `/api/discover/cards?search=${encodeURIComponent(search)}` : '/api/discover/cards'
    const response = await request<{ code: number; message: string; data: User[] }>(
      url,
      'GET',
      undefined,
      { 'Authorization': 'Bearer ' + token }
    )
    
    if (response.code === 200) {
      users.value = response.data
      
      for (const user of users.value) {
        try {
          const status = await getAttractionStatus(user.id)
          attractionStatuses[user.id] = status
        } catch (e) {
          console.error('获取好感度状态失败:', e)
        }
      }
    }
  } catch (error) {
    console.error('加载用户失败:', error)
    uni.showToast({ title: '加載失敗', icon: 'none' })
  } finally {
    loading.value = false
    refreshing.value = false
  }
}

function onRefresh() {
  refreshing.value = true
  loadUsers(true)
}

async function handleAttraction(user: User) {
  const currentStatus = getUserStatus(user.id)
  const currentType = currentStatus?.my_type || 0
  
  const options = ['讚', '踩']
  
  uni.showActionSheet({
    itemList: options,
    success: async (res) => {
      try {
        if (res.tapIndex === 0) {
          // 赞
          if (currentType === 1) {
            await cancelAttraction(user.id)
            if (attractionStatuses[user.id]) {
              attractionStatuses[user.id].my_type = 0
            }
            uni.showToast({ title: '已取消', icon: 'none' })
          } else {
            const result = await likeUser(user.id)
            if (!attractionStatuses[user.id]) {
              attractionStatuses[user.id] = { my_type: 0, their_type: 0, is_mutual: false }
            }
            attractionStatuses[user.id].my_type = 1
            attractionStatuses[user.id].is_mutual = result.data?.is_mutual || false
            uni.showToast({ title: '已讚', icon: 'success' })
          }
        } else {
          // 踩
          if (currentType === 2) {
            await cancelAttraction(user.id)
            if (attractionStatuses[user.id]) {
              attractionStatuses[user.id].my_type = 0
            }
            uni.showToast({ title: '已取消', icon: 'none' })
          } else {
            await dislikeUser(user.id)
            if (!attractionStatuses[user.id]) {
              attractionStatuses[user.id] = { my_type: 0, their_type: 0, is_mutual: false }
            }
            attractionStatuses[user.id].my_type = 2
            uni.showToast({ title: '已踩', icon: 'none' })
          }
        }
      } catch (error) {
        console.error('操作失败:', error)
        uni.showToast({ title: '操作失敗', icon: 'none' })
      }
    }
  })
}

function viewProfile(user: User) {
  uni.navigateTo({ 
    url: `/pages/profile/user-profile?id=${user.id}&name=${encodeURIComponent(user.name)}&avatar=${encodeURIComponent(user.avatar || defaultAvatar)}` 
  })
}

function startChat(user: User) {
  uni.navigateTo({ 
    url: `/pages/messages/detail?id=${user.id}&nickname=${encodeURIComponent(user.name)}&avatar=${encodeURIComponent(user.avatar || defaultAvatar)}` 
  })
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

function goProfile() {
  uni.switchTab({ url: '/pages/profile/index' })
}

function focusSearch() {
}

function onSearch() {
  if (!searchKeyword.value.trim()) {
    loadUsers()
    return
  }
  loadUsers()
}

function clearSearch() {
  searchKeyword.value = ''
  loadUsers()
}

onMounted(() => {
  const token = uni.getStorageSync('token')
  if (!token) {
    uni.reLaunch({ url: '/pages/auth/login' })
    return
  }
  verifyLogin(token)
})

async function verifyLogin(token: string) {
  try {
    await request<{ code: number; message: string; data: any }>(
      '/api/user/profile',
      'GET',
      undefined,
      { 'Authorization': 'Bearer ' + token }
    )
    loadUsers()
  } catch (error) {
    uni.removeStorageSync('token')
    uni.removeStorageSync('user')
    uni.reLaunch({ url: '/pages/auth/login' })
  }
}
</script>

<style lang="scss">
page {
  height: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
}

.container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  height: 100dvh;
  background: #ffffff;
}

.search-bar {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 40rpx;
  padding: 16rpx 24rpx;
}

.search-input {
  flex: 1;
  font-size: 28rpx;
  padding: 0 16rpx;
  color: #333;
}

.search-input::placeholder {
  color: #999;
}

.search-clear {
  padding: 8rpx;
}

.user-list {
  flex: 1;
  overflow: hidden;
  padding-bottom: calc(160rpx + env(safe-area-inset-bottom));
}

.user-grid {
  display: flex;
  flex-wrap: wrap;
  padding: 20rpx;
  gap: 20rpx;
}

.user-card {
  width: calc(50% - 10rpx);
  background: #fff;
  border-radius: 20rpx;
  overflow: hidden;
  box-shadow: 0 2rpx 12rpx rgba(0, 0, 0, 0.05);
  aspect-ratio: 1 / 2;
  display: flex;
  flex-direction: column;
}

.card-avatar-wrap {
  position: relative;
  width: 100%;
  padding-top: 100%;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.card-avatar {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.gender-tag {
  position: absolute;
  top: 16rpx;
  left: 16rpx;
  width: 40rpx;
  height: 40rpx;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  
  &.male {
    background: #4a90d9;
  }
  
  &.female {
    background: #ff6b9d;
  }
}

.vip-tag {
  position: absolute;
  top: 16rpx;
  right: 16rpx;
  background: linear-gradient(135deg, #ffd700, #ffb347);
  color: #fff;
  font-size: 20rpx;
  font-weight: bold;
  padding: 4rpx 12rpx;
  border-radius: 8rpx;
}

.mutual-tag {
  position: absolute;
  bottom: 16rpx;
  right: 16rpx;
  width: 40rpx;
  height: 40rpx;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2rpx solid #ff6b9d;
}

.card-content {
  padding: 20rpx;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.card-name {
  display: block;
  font-size: 32rpx;
  font-weight: 600;
  color: #333;
  margin-bottom: 8rpx;
}

.card-meta {
  display: flex;
  align-items: center;
  font-size: 24rpx;
  color: #999;
  margin-bottom: 8rpx;
}

.meta-dot {
  margin: 0 8rpx;
  color: #ccc;
}

.card-declaration {
  font-size: 24rpx;
  color: #999;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  display: block;
}

.card-actions {
  display: flex;
  padding: 16rpx 20rpx 20rpx;
  gap: 12rpx;
  border-top: 1rpx solid #eee;
}

.action-btn {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4rpx;
  padding: 12rpx 8rpx;
  background: #f8f9fa;
  border-radius: 12rpx;
  
  text {
    font-size: 22rpx;
    color: #666;
  }
  
  &:active {
    opacity: 0.7;
    transform: scale(0.98);
  }
  
  &.liked {
    background: rgba(255, 107, 157, 0.1);
    
    text {
      color: #ff6b9d;
    }
  }
  
  &.disliked {
    background: rgba(255, 71, 87, 0.1);
    
    text {
      color: #ff4757;
    }
  }
  
  &.chat {
    background: rgba(74, 144, 217, 0.1);
    
    text {
      color: #4a90d9;
    }
  }
  
  &.view {
    background: rgba(153, 153, 153, 0.1);
    
    text {
      color: #999;
    }
  }
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 200rpx 0;
}

.empty-text {
  font-size: 28rpx;
  color: #999;
  margin-top: 20rpx;
}

.loading-more {
  text-align: center;
  padding: 40rpx 0;
}

.loading-text {
  font-size: 26rpx;
  color: #999;
}

.bottom-tab {
  display: flex;
  background: #fff;
  border-top: 1rpx solid #eee;
  padding: 12rpx 0;
  padding-bottom: calc(12rpx + env(safe-area-inset-bottom));
  flex-shrink: 0;
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