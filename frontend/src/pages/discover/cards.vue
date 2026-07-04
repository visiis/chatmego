<template>
  <view class="container">
    <view class="tab-header">
      <view 
        class="tab-item" 
        :class="{ active: activeTab === 'cmg' }"
        @click="switchTab('cmg')"
      >
        <text>CMG</text>
        <view class="tab-indicator" v-if="activeTab === 'cmg'"></view>
      </view>
      <view 
        class="tab-item" 
        :class="{ active: activeTab === 'discover' }"
        @click="switchTab('discover')"
      >
        <text>發現</text>
        <view class="tab-indicator" v-if="activeTab === 'discover'"></view>
      </view>
    </view>
    
    <view class="header-actions">
      <view class="filter-btn" @click="showFilter = true">
        <FontAwesome name="sliders" size="24px" color="#fff" />
      </view>
    </view>
    
    <view class="main-content" v-if="activeTab === 'cmg'">
      <view class="cards-container" v-if="cardUsers.length > 0">
        <view 
          v-for="(user, index) in cardUsers.slice(0, 3)" 
          :key="user.id"
          class="card-wrapper"
          :style="{ zIndex: cardUsers.length - index, transform: `translateY(${index * 16}rpx) scale(${1 - index * 0.02})` }"
        >
          <SwipeCard 
            v-if="index === 0"
            :disabled="loading"
            @swipeLeft="handleDislike(currentUser)"
            @swipeRight="handleLike(currentUser)"
          >
            <view class="card-inner">
              <ImageCarousel :photos="user.photos" />
              
              <view class="card-info">
                <view class="user-header">
                  <view class="avatar-wrap">
                    <image 
                      class="user-avatar" 
                      :src="formatAvatar(user.avatar)" 
                      mode="aspectFill"
                    />
                    <view class="gender-tag" :class="user.gender === 'male' ? 'male' : 'female'">
                      <FontAwesome :name="user.gender === 'male' ? 'mars' : 'venus'" size="12px" color="#fff" />
                    </view>
                  </view>
                  <view class="user-info">
                    <text class="user-name">{{ user.nickname }}</text>
                    <view class="user-meta">
                      <text>{{ user.age }}歲</text>
                      <text class="meta-dot">·</text>
                      <text>{{ user.distance }}</text>
                    </view>
                  </view>
                  <view class="vip-tag" v-if="user.is_vip">VIP</view>
                </view>
                
                <view class="profile-details" v-if="user.height || user.weight">
                  <text v-if="user.height">{{ user.height }}cm</text>
                  <text v-if="user.height && user.weight" class="detail-dot">·</text>
                  <text v-if="user.weight">{{ user.weight }}kg</text>
                </view>
                
                <text class="love-declaration" v-if="user.love_declaration">{{ user.love_declaration }}</text>
              </view>
            </view>
          </SwipeCard>
          
          <view class="card-inner" v-else>
            <ImageCarousel :photos="user.photos" />
            <view class="card-info">
              <view class="user-header">
                <view class="avatar-wrap">
                  <image 
                    class="user-avatar" 
                    :src="formatAvatar(user.avatar)" 
                    mode="aspectFill"
                  />
                  <view class="gender-tag" :class="user.gender === 'male' ? 'male' : 'female'">
                    <FontAwesome :name="user.gender === 'male' ? 'mars' : 'venus'" size="12px" color="#fff" />
                  </view>
                </view>
                <view class="user-info">
                  <text class="user-name">{{ user.nickname }}</text>
                  <view class="user-meta">
                    <text>{{ user.age }}歲</text>
                    <text class="meta-dot">·</text>
                    <text>{{ user.distance }}</text>
                  </view>
                </view>
                <view class="vip-tag" v-if="user.is_vip">VIP</view>
              </view>
            </view>
          </view>
        </view>
      </view>
      
      <view class="empty-state" v-if="cardUsers.length === 0 && !loading">
        <FontAwesome name="users" size="80px" color="#ddd" />
        <text class="empty-text">暫時沒有更多用戶</text>
        <view class="retry-btn" @click="loadCardUsers">
          <text>重新加載</text>
        </view>
      </view>
      
      <view class="loading-state" v-if="loading">
        <view class="loading-spinner"></view>
        <text class="loading-text">加載中...</text>
      </view>
      
      <view class="bottom-actions">
        <view class="action-btn chat" @click="startChat(currentUser)">
          <FontAwesome name="comment" size="32px" color="#4a90d9" />
          <text>聊天</text>
        </view>
        <view class="action-btn nope" @click="handleDislike(currentUser)">
          <FontAwesome name="xmark" size="40px" color="#ff4d4f" />
        </view>
        <view class="action-btn like" @click="handleLike(currentUser)">
          <FontAwesome name="heart" size="40px" color="#52c41a" />
        </view>
        <view class="action-btn follow" @click="handleFollow(currentUser)">
          <FontAwesome name="user-plus" size="32px" color="#ff6b9d" />
          <text>關注</text>
        </view>
      </view>
    </view>
    
    <view class="main-content discover-view" v-else>
      <scroll-view 
        class="user-list"
        scroll-y
        :refresher-enabled="true"
        @refresherrefresh="onRefresh"
        :refresher-triggered="refreshing"
      >
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
              <FontAwesome name="xmark" size="20px" color="#999" />
            </view>
          </view>
        </view>
        
        <view class="user-grid">
          <view 
            class="user-card" 
            v-for="user in discoverUsers" 
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
        
        <view class="empty-state" v-if="discoverUsers.length === 0 && !loading">
          <FontAwesome name="users" size="60px" color="#ddd" />
          <text class="empty-text">暫時沒有更多用戶</text>
        </view>
        
        <view class="loading-more" v-if="loading">
          <text class="loading-text">加載中...</text>
        </view>
      </scroll-view>
    </view>
    
    <FilterPanel 
      :visible="showFilter"
      @close="showFilter = false"
      @confirm="handleFilterConfirm"
    />
    
    <view class="match-modal" :class="{ visible: showMatchModal }">
      <view class="match-mask" @click="showMatchModal = false"></view>
      <view class="match-content">
        <view class="match-heart">
          <FontAwesome name="heart" size="80px" color="#ff6b9d" />
        </view>
        <text class="match-title">匹配成功！</text>
        <text class="match-subtitle">你和 {{ matchUser?.nickname }} 互相喜歡了</text>
        <view class="match-buttons">
          <view class="btn-chat" @click="startMatchChat">
            <text>立即聊天</text>
          </view>
          <view class="btn-close" @click="showMatchModal = false">
            <text>稍後再聊</text>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, reactive, onMounted, onUnmounted } from 'vue'
import { onShow, onHide } from '@dcloudio/uni-app'
import FontAwesome from '../../components/FontAwesome.vue'
import SwipeCard from '../../components/SwipeCard.vue'
import ImageCarousel from '../../components/ImageCarousel.vue'
import FilterPanel from '../../components/FilterPanel.vue'
import { request } from '../../utils/request'
import { getRecommend, likeUser, dislikeUser } from '../../api/discover'
import { sendFriendRequest } from '../../api/friends'
import { 
  likeUser as attractionLike, 
  dislikeUser as attractionDislike, 
  cancelAttraction,
  getAttractionStatus,
  type AttractionStatus 
} from '../../api/attraction'
import { useRefreshTimer } from '../../utils/refresh'

interface CardPhoto {
  id?: number
  url: string
  blur_url?: string
  is_main?: number
  is_premium?: boolean
  points_price?: number
}

interface CardUser {
  id: number
  nickname: string
  avatar: string
  gender: string
  age: number
  height: string
  weight: string
  hobbies: string
  love_declaration: string
  is_vip: number
  distance?: string
  location?: string
  photos: CardPhoto[]
}

interface DiscoverUser {
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

const activeTab = ref<'cmg' | 'discover'>('cmg')
const showFilter = ref(false)
const showMatchModal = ref(false)
const matchUser = ref<CardUser | null>(null)
const loading = ref(false)
const refreshing = ref(false)
const searchKeyword = ref('')
const defaultAvatar = 'https://chatmego.com/images/default-avatar.svg'

const cardUsers = ref<CardUser[]>([])
const discoverUsers = ref<DiscoverUser[]>([])
const attractionStatuses = reactive<Record<number, AttractionStatus>>({})

const currentUser = computed(() => cardUsers.value[0] || null)

const { start: startRefresh, stop: stopRefresh } = useRefreshTimer(() => {
  if (!searchKeyword.value.trim() && activeTab.value === 'discover') {
    loadDiscoverUsers()
  }
}, 20000)

function switchTab(tab: 'cmg' | 'discover') {
  activeTab.value = tab
  if (tab === 'cmg' && cardUsers.value.length === 0) {
    loadCardUsers()
  }
}

function formatAvatar(url: string): string {
  if (!url) return defaultAvatar
  if (url.startsWith('http://') || url.startsWith('https://')) {
    return url
  }
  return 'https://chatmego.com/storage/' + url
}

async function loadCardUsers() {
  if (loading.value) return
  
  loading.value = true
  try {
    const users = await getRecommend()
    if (users && Array.isArray(users)) {
      cardUsers.value = users.map(u => ({
        ...u,
        photos: u.photos ? u.photos.slice(0, 4) : []
      }))
    } else if (users && users.users) {
      cardUsers.value = users.users.map(u => ({
        ...u,
        photos: u.photos ? u.photos.slice(0, 4) : []
      }))
    }
  } catch (error) {
    console.error('加载卡片用户失败:', error)
    uni.showToast({ title: '加載失敗', icon: 'none' })
  } finally {
    loading.value = false
  }
}

async function loadDiscoverUsers(refresh = false) {
  if (loading.value) return
  
  loading.value = true
  try {
    const token = uni.getStorageSync('token')
    const search = searchKeyword.value.trim()
    const url = search ? `/api/discover/cards?search=${encodeURIComponent(search)}` : '/api/discover/cards'
    const response = await request<{ code: number; message: string; data: DiscoverUser[] }>(
      url,
      'GET',
      undefined,
      { 'Authorization': 'Bearer ' + token }
    )
    
    if (response.code === 200) {
      discoverUsers.value = response.data
      
      const statusPromises = discoverUsers.value.map(user => {
        return getAttractionStatus(user.id).then(status => {
          attractionStatuses[user.id] = status
        }).catch(() => {})
      })
      
      await Promise.all(statusPromises)
    }
  } catch (error) {
    console.error('加载用户失败:', error)
    uni.showToast({ title: '加載失敗', icon: 'none' })
  } finally {
    loading.value = false
    refreshing.value = false
  }
}

async function handleLike(user: CardUser | null) {
  if (!user || loading.value) return
  
  loading.value = true
  try {
    const result = await likeUser(user.id)
    
    if (result.is_match && result.match) {
      matchUser.value = user
      showMatchModal.value = true
    } else {
      uni.showToast({ title: '已喜歡', icon: 'success' })
    }
    
    await attractionLike(user.id)
    
    cardUsers.value.shift()
    
    if (cardUsers.value.length < 3) {
      await loadCardUsers()
    }
  } catch (error) {
    console.error('喜歡失敗:', error)
    uni.showToast({ title: '操作失敗', icon: 'none' })
  } finally {
    loading.value = false
  }
}

async function handleDislike(user: CardUser | null) {
  if (!user || loading.value) return
  
  loading.value = true
  try {
    await dislikeUser(user.id)
    await attractionDislike(user.id)
    
    uni.showToast({ title: '已跳過', icon: 'none' })
    
    cardUsers.value.shift()
    
    if (cardUsers.value.length < 3) {
      await loadCardUsers()
    }
  } catch (error) {
    console.error('跳過失敗:', error)
    uni.showToast({ title: '操作失敗', icon: 'none' })
  } finally {
    loading.value = false
  }
}

async function handleFollow(user: CardUser | null) {
  if (!user) return
  
  try {
    await sendFriendRequest(user.id)
    uni.showToast({ title: '已發送好友請求', icon: 'success' })
  } catch (error) {
    console.error('發送好友請求失敗:', error)
    uni.showToast({ title: '操作失敗', icon: 'none' })
  }
}

async function handleAttraction(user: DiscoverUser) {
  const currentStatus = getUserStatus(user.id)
  const currentType = currentStatus?.my_type || 0
  
  const options = ['讚', '踩']
  
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
            uni.showToast({ title: '已讚', icon: 'success' })
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
        console.error('操作失敗:', error)
        uni.showToast({ title: '操作失敗', icon: 'none' })
      }
    }
  })
}

function getUserStatus(userId: number): AttractionStatus | undefined {
  return attractionStatuses[userId]
}

function viewProfile(user: DiscoverUser | CardUser) {
  const name = 'nickname' in user ? user.nickname : user.name
  const avatar = 'nickname' in user ? user.avatar : user.avatar || defaultAvatar
  uni.navigateTo({ 
    url: `/pages/profile/user-profile?id=${user.id}&name=${encodeURIComponent(name)}&avatar=${encodeURIComponent(avatar)}` 
  })
}

function startChat(user: DiscoverUser | CardUser | null) {
  if (!user) return
  
  const name = 'nickname' in user ? user.nickname : user.name
  const avatar = 'nickname' in user ? user.avatar : user.avatar || defaultAvatar
  uni.navigateTo({ 
    url: `/pages/messages/detail?id=${user.id}&nickname=${encodeURIComponent(name)}&avatar=${encodeURIComponent(avatar)}` 
  })
}

function startMatchChat() {
  if (!matchUser.value) return
  
  startChat(matchUser.value)
  showMatchModal.value = false
}

function handleFilterConfirm(filters: { gender: string; age: string; height: string; weight: string }) {
  console.log('筛选条件:', filters)
}

function onRefresh() {
  refreshing.value = true
  loadDiscoverUsers()
}

function onAvatarError(e: any, user: DiscoverUser) {
  user.avatar = defaultAvatar
}

function focusSearch() {}

function onSearch() {
  if (!searchKeyword.value.trim()) {
    loadDiscoverUsers()
    return
  }
  loadDiscoverUsers()
}

function clearSearch() {
  searchKeyword.value = ''
  loadDiscoverUsers()
}

onShow(() => {
  startRefresh()
})

onHide(() => {
  stopRefresh()
})

onUnmounted(() => {
  stopRefresh()
})

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
    loadCardUsers()
    loadDiscoverUsers()
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
  background: #ffffff;
}

.container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  height: 100dvh;
  background: #ffffff;
}

.tab-header {
  display: flex;
  justify-content: center;
  gap: 80rpx;
  padding: 24rpx 0;
  background: linear-gradient(135deg, #ff6b9d, #c44569);
  position: relative;
}

.tab-item {
  position: relative;
  padding: 16rpx 32rpx;
  
  text {
    font-size: 32rpx;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.7);
    transition: color 0.3s ease;
  }
  
  &.active {
    text {
      color: #fff;
    }
  }
}

.tab-indicator {
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 48rpx;
  height: 6rpx;
  background: #fff;
  border-radius: 3rpx;
}

.header-actions {
  position: absolute;
  right: 24rpx;
  top: 50%;
  transform: translateY(-50%);
}

.filter-btn {
  width: 64rpx;
  height: 64rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
}

.main-content {
  flex: 1;
  overflow: hidden;
  position: relative;
}

.cards-container {
  position: relative;
  width: 100%;
  height: calc(100% - 160rpx);
  padding: 24rpx;
  box-sizing: border-box;
}

.card-wrapper {
  position: absolute;
  top: 24rpx;
  left: 24rpx;
  right: 24rpx;
  border-radius: 24rpx;
  overflow: hidden;
  box-shadow: 0 8rpx 32rpx rgba(0, 0, 0, 0.15);
  background: #fff;
}

.card-inner {
  position: relative;
  width: 100%;
  aspect-ratio: 3/4;
}

.card-info {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 32rpx;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
}

.user-header {
  display: flex;
  align-items: center;
  gap: 16rpx;
}

.avatar-wrap {
  position: relative;
}

.user-avatar {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
  border: 3rpx solid #fff;
}

.gender-tag {
  position: absolute;
  bottom: -4rpx;
  right: -4rpx;
  width: 36rpx;
  height: 36rpx;
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

.user-info {
  flex: 1;
}

.user-name {
  display: block;
  font-size: 34rpx;
  font-weight: 600;
  color: #fff;
  margin-bottom: 4rpx;
}

.user-meta {
  display: flex;
  align-items: center;
  font-size: 24rpx;
  color: rgba(255, 255, 255, 0.8);
}

.meta-dot {
  margin: 0 8rpx;
}

.vip-tag {
  background: linear-gradient(135deg, #ffd700, #ffb347);
  color: #fff;
  font-size: 20rpx;
  font-weight: bold;
  padding: 6rpx 16rpx;
  border-radius: 8rpx;
}

.profile-details {
  display: flex;
  align-items: center;
  font-size: 26rpx;
  color: rgba(255, 255, 255, 0.9);
  margin-top: 12rpx;
}

.detail-dot {
  margin: 0 8rpx;
}

.love-declaration {
  display: block;
  font-size: 24rpx;
  color: rgba(255, 255, 255, 0.8);
  margin-top: 8rpx;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.empty-state {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  flex-direction: column;
  align-items: center;
}

.empty-text {
  font-size: 28rpx;
  color: #999;
  margin-top: 24rpx;
}

.retry-btn {
  margin-top: 32rpx;
  padding: 16rpx 48rpx;
  background: #ff6b9d;
  border-radius: 32rpx;
  
  text {
    font-size: 28rpx;
    color: #fff;
  }
}

.loading-state {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  flex-direction: column;
  align-items: center;
}

.loading-spinner {
  width: 60rpx;
  height: 60rpx;
  border: 6rpx solid rgba(255, 107, 157, 0.2);
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
  font-size: 26rpx;
  color: #999;
  margin-top: 16rpx;
}

.bottom-actions {
  display: flex;
  justify-content: center;
  gap: 48rpx;
  padding: 24rpx 0;
  padding-bottom: calc(24rpx + env(safe-area-inset-bottom));
  background: #fff;
  border-top: 1rpx solid #eee;
}

.action-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8rpx;
  padding: 16rpx;
  
  text {
    font-size: 22rpx;
    color: #666;
  }
  
  &:active {
    opacity: 0.7;
    transform: scale(0.95);
  }
  
  &.nope,
  &.like {
    width: 100rpx;
    height: 100rpx;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    
    &.nope {
      background: #fff0f0;
    }
    
    &.like {
      background: #f6ffed;
    }
  }
  
  &.chat {
    text {
      color: #4a90d9;
    }
  }
  
  &.follow {
    text {
      color: #ff6b9d;
    }
  }
}

.discover-view {
  display: flex;
  flex-direction: column;
}

.user-list {
  flex: 1;
  overflow: hidden;
}

.search-section {
  padding: 24rpx;
  background: #ffffff;
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

.card-actions .action-btn {
  flex: 1;
  padding: 12rpx 8rpx;
  background: #f8f9fa;
  border-radius: 12rpx;
  
  text {
    font-size: 22rpx;
    color: #666;
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
}

.loading-more {
  text-align: center;
  padding: 40rpx 0;
}

.match-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 2000;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.3s ease;
  
  &.visible {
    pointer-events: auto;
    opacity: 1;
  }
}

.match-mask {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
}

.match-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #fff;
  border-radius: 32rpx;
  padding: 64rpx;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 500rpx;
}

.match-heart {
  width: 120rpx;
  height: 120rpx;
  background: linear-gradient(135deg, #ff6b9d, #c44569);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

.match-title {
  font-size: 40rpx;
  font-weight: bold;
  color: #333;
  margin-top: 32rpx;
}

.match-subtitle {
  font-size: 28rpx;
  color: #999;
  margin-top: 16rpx;
}

.match-buttons {
  display: flex;
  gap: 24rpx;
  margin-top: 48rpx;
}

.btn-chat,
.btn-close {
  padding: 20rpx 64rpx;
  border-radius: 32rpx;
  font-size: 30rpx;
  font-weight: 500;
  
  &:active {
    opacity: 0.8;
  }
}

.btn-chat {
  background: linear-gradient(135deg, #ff6b9d, #c44569);
  color: #fff;
}

.btn-close {
  background: #f5f5f5;
  color: #666;
}
</style>
