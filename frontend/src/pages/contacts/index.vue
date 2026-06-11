<template>
  <view class="contacts-page">
    <view class="header">
      <text class="page-title">通讯录</text>
      <view class="header-actions">
        <text class="action-icon" @click="goToAddFriend">➕</text>
      </view>
    </view>
    
    <view class="contacts-list">
      <view class="section">
        <view class="section-header">
          <text class="section-title">好友</text>
          <text class="section-count">{{ friends.length }}</text>
        </view>
        
        <view 
          class="contact-item" 
          v-for="friend in friends" 
          :key="friend.id"
          @click="goToFriendDetail(friend.id)"
        >
          <view class="contact-avatar">
            <image 
              class="avatar" 
              :src="friend.avatar || 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20person&image_size=square'" 
              mode="aspectFill" 
            />
            <view class="online-badge" v-if="isOnline(friend.id)">🟢</view>
          </view>
          <view class="contact-info">
            <text class="contact-name">{{ friend.nickname }}</text>
            <text class="contact-status">{{ friend.love_declaration || '暂无签名' }}</text>
          </view>
          <view class="contact-action">
            <text class="chat-btn" @click.stop="goToChat(friend.id)">💬</text>
          </view>
        </view>
        
        <view class="empty-section" v-if="friends.length === 0">
          <text class="empty-text">暂无好友</text>
        </view>
      </view>
      
      <view class="section">
        <view class="section-header">
          <text class="section-title">好友申请</text>
          <view class="badge" v-if="pendingRequests.length > 0">
            <text>{{ pendingRequests.length }}</text>
          </view>
        </view>
        
        <view 
          class="request-item" 
          v-for="request in pendingRequests" 
          :key="request.id"
        >
          <image 
            class="request-avatar" 
            :src="request.user.avatar || 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20person&image_size=square'" 
            mode="aspectFill" 
          />
          <view class="request-info">
            <text class="request-name">{{ request.user.nickname }}</text>
            <text class="request-message">{{ request.message || '请求添加你为好友' }}</text>
          </view>
          <view class="request-actions">
            <view class="action-btn reject" @click="rejectRequest(request.id)">
              <text>拒绝</text>
            </view>
            <view class="action-btn accept" @click="acceptRequest(request.id)">
              <text>接受</text>
            </view>
          </view>
        </view>
        
        <view class="empty-section" v-if="pendingRequests.length === 0">
          <text class="empty-text">暂无好友申请</text>
        </view>
      </view>
      
      <view class="section">
        <view class="section-header">
          <text class="section-title">黑名单</text>
        </view>
        
        <view 
          class="contact-item" 
          v-for="blocked in blockedUsers" 
          :key="blocked.id"
          @click="goToFriendDetail(blocked.id)"
        >
          <image 
            class="avatar blocked" 
            :src="blocked.avatar || 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20person&image_size=square'" 
            mode="aspectFill" 
          />
          <view class="contact-info">
            <text class="contact-name">{{ blocked.nickname }}</text>
            <text class="contact-status">已拉黑</text>
          </view>
        </view>
        
        <view class="empty-section" v-if="blockedUsers.length === 0">
          <text class="empty-text">暂无拉黑用户</text>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const friends = ref([
  { id: 1, nickname: '小美', avatar: '', love_declaration: '喜欢旅行和美食' },
  { id: 2, nickname: '阳光男孩', avatar: '', love_declaration: '热爱生活每一天' },
  { id: 3, nickname: '小仙女', avatar: '', love_declaration: '努力变优秀' }
])

const pendingRequests = ref([
  { id: 1, user: { id: 4, nickname: '新用户', avatar: '' }, message: '嗨! 很高兴认识你' },
  { id: 2, user: { id: 5, nickname: '缘分天注定', avatar: '' }, message: '' }
])

const blockedUsers = ref([
  { id: 6, nickname: '陌生人', avatar: '' }
])

function isOnline(userId: number) {
  return Math.random() > 0.5
}

function goToFriendDetail(userId: number) {
  uni.navigateTo({ url: `/pages/contacts/friend-detail?userId=${userId}` })
}

function goToChat(userId: number) {
  uni.navigateTo({ url: `/pages/chats/detail?userId=${userId}` })
}

function goToAddFriend() {
  uni.showToast({ title: '添加好友功能开发中', icon: 'none' })
}

function acceptRequest(requestId: number) {
  uni.showToast({ title: '已接受好友请求', icon: 'success' })
  pendingRequests.value = pendingRequests.value.filter(r => r.id !== requestId)
}

function rejectRequest(requestId: number) {
  uni.showToast({ title: '已拒绝好友请求', icon: 'none' })
  pendingRequests.value = pendingRequests.value.filter(r => r.id !== requestId)
}
</script>

<style lang="scss" scoped>
.contacts-page {
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

.action-icon {
  font-size: 40rpx;
}

.contacts-list {
  padding: 24rpx;
}

.section {
  background: #fff;
  border-radius: 16rpx;
  margin-bottom: 24rpx;
  overflow: hidden;
}

.section-header {
  display: flex;
  align-items: center;
  padding: 24rpx;
  border-bottom: 1rpx solid #f0f0f0;
}

.section-title {
  font-size: 30rpx;
  font-weight: bold;
  color: #333;
}

.section-count {
  font-size: 24rpx;
  color: #999;
  margin-left: 12rpx;
}

.badge {
  background: #f87c7c;
  border-radius: 50%;
  min-width: 36rpx;
  height: 36rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 12rpx;
  padding: 0 10rpx;
  
  text {
    font-size: 22rpx;
    color: #fff;
  }
}

.contact-item {
  display: flex;
  align-items: center;
  padding: 24rpx;
  
  &:active {
    background: #f9f9f9;
  }
}

.contact-avatar {
  position: relative;
  margin-right: 20rpx;
}

.avatar {
  width: 96rpx;
  height: 96rpx;
  border-radius: 50%;
  
  &.blocked {
    filter: grayscale(100%);
  }
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

.contact-info {
  flex: 1;
  min-width: 0;
}

.contact-name {
  display: block;
  font-size: 30rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 8rpx;
}

.contact-status {
  font-size: 24rpx;
  color: #999;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.contact-action {
  margin-left: 20rpx;
}

.chat-btn {
  font-size: 40rpx;
}

.request-item {
  display: flex;
  align-items: center;
  padding: 24rpx;
  border-bottom: 1rpx solid #f0f0f0;
  
  &:last-child {
    border-bottom: none;
  }
}

.request-avatar {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
  margin-right: 20rpx;
}

.request-info {
  flex: 1;
}

.request-name {
  display: block;
  font-size: 28rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 8rpx;
}

.request-message {
  font-size: 24rpx;
  color: #999;
}

.request-actions {
  display: flex;
  gap: 16rpx;
}

.action-btn {
  padding: 12rpx 24rpx;
  border-radius: 24rpx;
  
  text {
    font-size: 24rpx;
  }
  
  &.reject {
    background: #f5f5f5;
    
    text {
      color: #666;
    }
  }
  
  &.accept {
    background: #f87c7c;
    
    text {
      color: #fff;
    }
  }
}

.empty-section {
  padding: 40rpx;
  text-align: center;
}

.empty-text {
  font-size: 26rpx;
  color: #999;
}
</style>
