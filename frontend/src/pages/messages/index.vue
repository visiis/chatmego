<template>
  <view class="chat-page">
    <scroll-view class="chat-list" scroll-y @scrolltolower="loadMore">
      <view 
        class="chat-item" 
        v-for="chat in chats" 
        :key="chat.id"
        @click="openChat(chat)"
      >
        <image class="chat-avatar" :src="chat.friend.avatar || 'https://chatmego.com/images/default-avatar.svg'" mode="aspectFill" />
        <view class="chat-info">
          <view class="chat-header">
            <text class="chat-name">{{ chat.friend.name || chat.friend.nickname }}</text>
            <text class="chat-time">{{ formatTime(chat.last_message?.created_at) }}</text>
          </view>
          <text class="chat-preview">{{ getPreview(chat.last_message) }}</text>
        </view>
        <view class="chat-badge" v-if="chat.unread_count > 0">{{ chat.unread_count }}</view>
      </view>
      
      <view class="empty-state" v-if="chats.length === 0 && !loading">
        <FontAwesome name="comment-slash" size="80px" color="#ccc" />
        <text class="empty-text">還沒有聊天記錄</text>
        <text class="empty-hint">添加好友後就可以開始聊天了</text>
        <view class="empty-btn" @click="goFriends">
          <FontAwesome name="users" size="24px" color="#fff" />
          <text> 前往好友</text>
        </view>
      </view>
      
      <view class="loading-more" v-if="loading">
        <text class="loading-text">載入中...</text>
      </view>
    </scroll-view>
    
    <view class="bottom-tab">
      <view class="bottom-tab-item" @click="goDiscover">
        <FontAwesome name="compass" size="24px" color="#999" />
        <text class="tab-text">發現</text>
      </view>
      <view class="bottom-tab-item" @click="goFriends">
        <FontAwesome name="users" size="24px" color="#999" />
        <text class="tab-text">好友</text>
      </view>
      <view class="bottom-tab-item active">
        <FontAwesome name="comment" size="24px" color="#ff6b9d" />
        <text class="tab-text active">聊天</text>
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
import { ref, onMounted } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import FontAwesome from '../../components/FontAwesome.vue'
import { getConversations, type Conversation } from '../../api/chat'

const chats = ref<Conversation[]>([])
const loading = ref(false)
const activeTab = ref('chat')

onMounted(() => {
  loadConversations()
})

onShow(() => {
  loadConversations()
})

async function loadConversations() {
  loading.value = true
  try {
    chats.value = await getConversations()
  } catch (error) {
    console.error('載入聊天列表失敗:', error)
  } finally {
    loading.value = false
  }
}

function loadMore() {
  loadConversations()
}

function formatTime(dateStr?: string): string {
  if (!dateStr) return ''
  
  const date = new Date(dateStr)
  const now = new Date()
  const diff = now.getTime() - date.getTime()
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)
  
  if (minutes < 1) return '剛剛'
  if (minutes < 60) return `${minutes}分鐘前`
  if (hours < 24) return `${hours}小時前`
  if (days < 7) return `${days}天前`
  
  const month = (date.getMonth() + 1).toString().padStart(2, '0')
  const day = date.getDate().toString().padStart(2, '0')
  return `${month}-${day}`
}

function getPreview(msg?: { type: string; content: string }): string {
  if (!msg) return '來聊聊吧'
  
  switch (msg.type) {
    case 'image':
      return '[圖片]'
    case 'voice':
      return '[語音]'
    case 'video':
      return '[影片]'
    case 'gift':
      return '[禮物]'
    case 'emoji':
      return '[表情]'
    default:
      return msg.content ? msg.content.slice(0, 30) : '來聊聊吧'
  }
}

function openChat(chat: Conversation) {
  const name = encodeURIComponent(chat.friend.name || chat.friend.nickname || '')
  const avatar = encodeURIComponent(chat.friend.avatar || '')
  uni.navigateTo({ 
    url: `/pages/messages/detail?id=${chat.friend.id}&name=${name}&avatar=${avatar}` 
  })
}

function searchChats() {
  uni.showToast({ title: '搜尋功能開發中', icon: 'none' })
}

function goDiscover() {
  activeTab.value = 'discover'
  uni.switchTab({ url: '/pages/discover/cards' })
}

function goFriends() {
  activeTab.value = 'friends'
  uni.switchTab({ url: '/pages/friends/index' })
}

function goStatuses() {
  uni.switchTab({ url: '/pages/statuses/index' })
}

function goProfile() {
  activeTab.value = 'profile'
  uni.switchTab({ url: '/pages/profile/index' })
}
</script>

<style lang="scss">
* {
  box-sizing: border-box;
}

page {
  height: 100%;
  margin: 0;
  padding: 0;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.chat-page {
  display: flex;
  flex-direction: column;
  height: 100vh;
  height: 100dvh;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
}

.chat-list {
  flex: 1;
  padding-bottom: calc(160rpx + env(safe-area-inset-bottom));
  overflow: hidden;
}

.chat-item {
  display: flex;
  align-items: center;
  background: #fff;
  padding: 24rpx 32rpx;
  border-bottom: 1rpx solid #f0f0f0;
  transition: background 0.2s;
  
  &:active {
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  }
}

.chat-avatar {
  width: 104rpx;
  height: 104rpx;
  border-radius: 50%;
  background: #f0f0f0;
  flex-shrink: 0;
}

.chat-info {
  flex: 1;
  margin-left: 24rpx;
  min-width: 0;
}

.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8rpx;
}

.chat-name {
  font-size: 32rpx;
  color: #333;
  font-weight: 500;
}

.chat-time {
  font-size: 22rpx;
  color: #999;
}

.chat-preview {
  font-size: 26rpx;
  color: #999;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.chat-badge {
  min-width: 40rpx;
  height: 40rpx;
  background: #ff4757;
  border-radius: 20rpx;
  font-size: 22rpx;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 12rpx;
  margin-left: 16rpx;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 150rpx 60rpx;
}

.empty-icon {
  font-size: 80rpx;
}

.empty-text {
  font-size: 32rpx;
  color: #999;
  margin-top: 30rpx;
}

.empty-hint {
  font-size: 26rpx;
  color: #ccc;
  margin-top: 12rpx;
}

.empty-btn {
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  color: #fff;
  padding: 20rpx 40rpx;
  border-radius: 40rpx;
  margin-top: 40rpx;
  font-size: 28rpx;
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

.tab-icon {
  font-size: 40rpx;
}

.tab-text {
  font-size: 20rpx;
  color: #999;
  
  &.active {
    color: #ff6b9d;
  }
}
</style>
