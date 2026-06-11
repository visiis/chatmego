<template>
  <view class="chats-page">
    <view class="header">
      <text class="page-title">聊天</text>
      <view class="header-actions">
        <text class="action-icon" @click="goToSearch">🔍</text>
        <text class="action-icon" @click="goToNewChat">✏️</text>
      </view>
    </view>
    
    <view class="chat-list">
      <view 
        class="chat-item" 
        v-for="match in chatStore.matches" 
        :key="match.id"
        @click="goToChat(match.user.id)"
      >
        <view class="chat-avatar">
          <image 
            class="avatar" 
            :src="match.user.avatar || 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20person&image_size=square'" 
            mode="aspectFill" 
          />
          <view class="online-badge" v-if="isOnline(match.user.id)">🟢</view>
        </view>
        
        <view class="chat-info">
          <view class="chat-header">
            <text class="chat-name">{{ match.user.nickname }}</text>
            <text class="chat-time">{{ formatTime(match.last_message?.created_at) }}</text>
          </view>
          <view class="chat-content">
            <text class="last-message" :class="{ unread: chatStore.getUnreadCount(match.user.id) > 0 }">
              {{ getLastMessageContent(match) }}
            </text>
            <view class="unread-badge" v-if="chatStore.getUnreadCount(match.user.id) > 0">
              <text>{{ chatStore.getUnreadCount(match.user.id) }}</text>
            </view>
          </view>
        </view>
      </view>
      
      <view class="empty-state" v-if="chatStore.matches.length === 0">
        <text class="empty-icon">💬</text>
        <text class="empty-text">暂无聊天记录</text>
        <view class="explore-btn" @click="goToDiscover">
          <text>去发现新朋友</text>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import { useChatStore } from '@/stores/chat'
import { getMatches } from '@/api/discover'

const chatStore = useChatStore()

async function loadMatches() {
  try {
    const response = await getMatches()
    chatStore.setMatches(response.data.matches)
  } catch (e) {
    console.error('Failed to load matches:', e)
  }
}

function formatTime(timeStr: string | undefined) {
  if (!timeStr) return ''
  const date = new Date(timeStr)
  const now = new Date()
  const diff = now.getTime() - date.getTime()
  
  if (diff < 60000) {
    return '刚刚'
  } else if (diff < 3600000) {
    return Math.floor(diff / 60000) + '分钟前'
  } else if (diff < 86400000) {
    return Math.floor(diff / 3600000) + '小时前'
  } else if (diff < 604800000) {
    return Math.floor(diff / 86400000) + '天前'
  } else {
    return `${date.getMonth() + 1}/${date.getDate()}`
  }
}

function getLastMessageContent(match: any) {
  if (!match.last_message) return '暂无消息'
  
  if (match.last_message.type === 'gift') {
    return '🎁 发送了礼物'
  } else if (match.last_message.type === 'image') {
    return '📷 发送了图片'
  } else if (match.last_message.type === 'voice') {
    return '🎤 发送了语音'
  }
  
  return match.last_message.content
}

function isOnline(userId: number) {
  return Math.random() > 0.5
}

function goToChat(userId: number) {
  chatStore.setCurrentChatUserId(userId)
  uni.navigateTo({ url: `/pages/chats/detail?userId=${userId}` })
}

function goToSearch() {
  uni.showToast({ title: '搜索功能开发中', icon: 'none' })
}

function goToNewChat() {
  uni.showToast({ title: '新建聊天开发中', icon: 'none' })
}

function goToDiscover() {
  uni.switchTab({ url: '/pages/discover/index' })
}

onMounted(() => {
  loadMatches()
})

onShow(() => {
  loadMatches()
})
</script>

<style lang="scss" scoped>
.chats-page {
  min-height: 100vh;
  background: #f5f5f5;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 60rpx 32rpx 32rpx;
  background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
}

.page-title {
  font-size: 40rpx;
  font-weight: bold;
  color: #fff;
}

.header-actions {
  display: flex;
  gap: 32rpx;
}

.action-icon {
  font-size: 36rpx;
}

.chat-list {
  padding: 24rpx;
}

.chat-item {
  display: flex;
  align-items: center;
  padding: 24rpx;
  background: #fff;
  border-radius: 16rpx;
  margin-bottom: 16rpx;
  box-shadow: 0 2rpx 8rpx rgba(0, 0, 0, 0.04);
  
  &:active {
    background: #f9f9f9;
  }
}

.chat-avatar {
  position: relative;
  margin-right: 24rpx;
}

.avatar {
  width: 100rpx;
  height: 100rpx;
  border-radius: 50%;
}

.online-badge {
  position: absolute;
  bottom: 4rpx;
  right: 4rpx;
  width: 24rpx;
  height: 24rpx;
  border-radius: 50%;
  border: 4rpx solid #fff;
}

.chat-info {
  flex: 1;
  min-width: 0;
}

.chat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8rpx;
}

.chat-name {
  font-size: 30rpx;
  font-weight: bold;
  color: #333;
}

.chat-time {
  font-size: 22rpx;
  color: #999;
}

.chat-content {
  display: flex;
  align-items: center;
}

.last-message {
  flex: 1;
  font-size: 26rpx;
  color: #999;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  
  &.unread {
    color: #f87c7c;
    font-weight: bold;
  }
}

.unread-badge {
  background: #f87c7c;
  border-radius: 20rpx;
  min-width: 36rpx;
  height: 36rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 12rpx;
  padding: 0 12rpx;
  
  text {
    font-size: 22rpx;
    color: #fff;
  }
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 120rpx 32rpx;
}

.empty-icon {
  font-size: 120rpx;
  margin-bottom: 32rpx;
}

.empty-text {
  font-size: 32rpx;
  color: #999;
  margin-bottom: 40rpx;
}

.explore-btn {
  padding: 24rpx 60rpx;
  background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
  border-radius: 40rpx;
  
  text {
    color: #fff;
    font-size: 30rpx;
  }
}
</style>
