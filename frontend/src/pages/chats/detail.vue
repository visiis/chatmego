<template>
  <view class="chat-detail-page">
    <view class="header">
      <view class="header-left" @click="goBack">
        <text class="back-icon">←</text>
      </view>
      <view class="header-center">
        <text class="chat-title">{{ currentUser?.nickname }}</text>
        <text class="online-status" v-if="isOnline">在线</text>
      </view>
      <view class="header-right">
        <text class="action-icon" @click="showUserProfile">👤</text>
      </view>
    </view>
    
    <scroll-view 
      class="messages-container" 
      scroll-y 
      :scroll-top="scrollTop"
      :scroll-with-animation="true"
    >
      <view class="messages-list">
        <view 
          class="message-item" 
          v-for="(msg, index) in messages" 
          :key="msg.id"
          :class="{ 'is-self': msg.from === currentUserId }"
        >
          <image 
            class="msg-avatar" 
            :src="msg.from === currentUserId ? userStore.user?.avatar : currentUser?.avatar" 
            mode="aspectFill" 
          />
          
          <view class="msg-content">
            <view class="msg-bubble" :class="{ 'is-gift': msg.type === 'gift' }">
              <view class="gift-content" v-if="msg.type === 'gift'">
                <text class="gift-icon">{{ getGiftIcon(msg.ext?.gift_id) }}</text>
                <view class="gift-info">
                  <text class="gift-name">送你礼物</text>
                  <text class="gift-points">{{ msg.ext?.gift_points }}积分</text>
                </view>
              </view>
              <text class="msg-text" v-else-if="msg.type === 'text'">{{ msg.content }}</text>
              <image class="msg-image" v-else-if="msg.type === 'image'" :src="msg.content" mode="widthFix" />
              <view class="voice-content" v-else-if="msg.type === 'voice'">
                <text class="voice-icon">🎤</text>
                <text class="voice-duration">{{ getVoiceDuration(msg.content) }}''</text>
              </view>
            </view>
            <text class="msg-time">{{ formatTime(msg.created_at) }}</text>
          </view>
        </view>
      </view>
    </scroll-view>
    
    <view class="input-bar">
      <view class="input-tools">
        <text class="tool-icon" @click="toggleVoice">🎤</text>
        <text class="tool-icon" @click="chooseImage">📷</text>
        <text class="tool-icon" @click="showGiftPanel">🎁</text>
      </view>
      
      <view class="input-wrapper" v-if="!isVoiceMode">
        <input 
          class="message-input" 
          v-model="inputText" 
          placeholder="输入消息..." 
          @confirm="sendMessage"
        />
        <text class="send-btn" :class="{ active: inputText.trim() }" @click="sendMessage">
          发送
        </text>
      </view>
      
      <view class="voice-wrapper" v-else>
        <text class="voice-hint">按住说话</text>
        <view class="voice-btn" @touchstart="startRecording" @touchend="stopRecording">
          <text class="voice-mic">🎙️</text>
        </view>
        <text class="voice-hint">松开发送</text>
      </view>
    </view>
    
    <view class="gift-panel" v-if="showGift">
      <view class="gift-panel-header">
        <text class="gift-title">选择礼物</text>
        <text class="gift-close" @click="showGift = false">✕</text>
      </view>
      <scroll-view scroll-x class="gift-categories">
        <text 
          class="category-item" 
          v-for="cat in giftCategories" 
          :key="cat.id"
          :class="{ active: selectedCategory === cat.id }"
          @click="selectedCategory = cat.id"
        >
          {{ cat.name }}
        </text>
      </scroll-view>
      <scroll-view scroll-y class="gift-list">
        <view 
          class="gift-item" 
          v-for="gift in currentGifts" 
          :key="gift.id"
          @click="selectGift(gift)"
        >
          <text class="gift-icon-large">{{ gift.icon }}</text>
          <text class="gift-name">{{ gift.name }}</text>
          <text class="gift-price">{{ gift.points }}积分</text>
        </view>
      </scroll-view>
      <view class="gift-footer">
        <text class="balance-info">余额: {{ userStore.user?.points.balance }}积分</text>
        <view class="send-gift-btn" @click="sendGift">
          <text>发送礼物</text>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { useUserStore } from '@/stores/user'
import { useChatStore } from '@/stores/chat'
import { getUserProfile } from '@/api/user'
import { sendGift } from '@/api/gifts'

const userStore = useUserStore()
const chatStore = useChatStore()

const currentUserId = ref(0)
const currentUser = ref<any>(null)
const inputText = ref('')
const scrollTop = ref(0)
const isVoiceMode = ref(false)
const showGift = ref(false)
const selectedCategory = ref(1)
const selectedGift = ref<any>(null)
const isOnline = ref(true)

const giftCategories = ref([
  { id: 1, name: '热门' },
  { id: 2, name: '浪漫' },
  { id: 3, name: '趣味' },
  { id: 4, name: '豪华' }
])

const currentGifts = ref([
  { id: 1, name: '玫瑰', icon: '🌹', points: 10, category_id: 1 },
  { id: 2, name: '爱心', icon: '❤️', points: 5, category_id: 1 },
  { id: 3, name: '星星', icon: '⭐', points: 8, category_id: 1 },
  { id: 4, name: '钻石', icon: '💎', points: 100, category_id: 4 },
  { id: 5, name: '皇冠', icon: '👑', points: 200, category_id: 4 },
  { id: 6, name: '气球', icon: '🎈', points: 15, category_id: 2 },
  { id: 7, name: '蛋糕', icon: '🎂', points: 25, category_id: 2 },
  { id: 8, name: '彩虹', icon: '🌈', points: 20, category_id: 3 },
  { id: 9, name: '火箭', icon: '🚀', points: 50, category_id: 3 },
  { id: 10, name: '花朵', icon: '🌸', points: 12, category_id: 2 }
])

const messages = computed(() => {
  return chatStore.getMessages(currentUserId.value)
})

function getGiftIcon(giftId?: number) {
  const gift = currentGifts.value.find(g => g.id === giftId)
  return gift?.icon || '🎁'
}

function getVoiceDuration(content: string) {
  return parseInt(content) || 0
}

function formatTime(timeStr: string) {
  const date = new Date(timeStr)
  return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`
}

function sendMessage() {
  if (!inputText.value.trim()) return
  
  const message = {
    id: Date.now().toString(),
    from: userStore.user?.id || 0,
    to: currentUserId.value,
    content: inputText.value,
    type: 'text' as const,
    created_at: new Date().toISOString(),
    is_read: false
  }
  
  chatStore.addMessage(currentUserId.value, message)
  inputText.value = ''
  
  nextTick(() => {
    scrollTop.value = 99999
  })
}

function toggleVoice() {
  isVoiceMode.value = !isVoiceMode.value
}

function chooseImage() {
  uni.chooseImage({
    count: 1,
    success: (res) => {
      const message = {
        id: Date.now().toString(),
        from: userStore.user?.id || 0,
        to: currentUserId.value,
        content: res.tempFilePaths[0],
        type: 'image' as const,
        created_at: new Date().toISOString(),
        is_read: false
      }
      chatStore.addMessage(currentUserId.value, message)
      
      nextTick(() => {
        scrollTop.value = 99999
      })
    }
  })
}

function showGiftPanel() {
  showGift.value = true
}

function selectGift(gift: any) {
  selectedGift.value = gift
}

async function sendGift() {
  if (!selectedGift.value) {
    uni.showToast({ title: '请选择礼物', icon: 'none' })
    return
  }
  
  if ((userStore.user?.points.balance || 0) < selectedGift.value.points) {
    uni.showToast({ title: '积分不足', icon: 'none' })
    return
  }
  
  try {
    await sendGift(currentUserId.value, selectedGift.value.id)
    
    const message = {
      id: Date.now().toString(),
      from: userStore.user?.id || 0,
      to: currentUserId.value,
      content: '',
      type: 'gift' as const,
      created_at: new Date().toISOString(),
      is_read: false,
      ext: {
        gift_id: selectedGift.value.id,
        gift_points: selectedGift.value.points
      }
    }
    
    chatStore.addMessage(currentUserId.value, message)
    showGift.value = false
    selectedGift.value = null
    
    nextTick(() => {
      scrollTop.value = 99999
    })
    
    uni.showToast({ title: '礼物发送成功', icon: 'success' })
  } catch (e: any) {
    uni.showToast({ title: e.message || '发送失败', icon: 'none' })
  }
}

function startRecording() {
  uni.showToast({ title: '开始录音', icon: 'none', duration: 1000 })
}

function stopRecording() {
  uni.showToast({ title: '录音结束', icon: 'none', duration: 1000 })
}

function showUserProfile() {
  uni.navigateTo({ url: `/pages/contacts/friend-detail?userId=${currentUserId.value}` })
}

function goBack() {
  uni.navigateBack()
}

onLoad((options: any) => {
  if (options?.userId) {
    currentUserId.value = parseInt(options.userId)
    loadUserProfile()
  }
})

async function loadUserProfile() {
  try {
    const response = await getUserProfile(currentUserId.value)
    currentUser.value = response.data
  } catch (e) {
    console.error('Failed to load user profile:', e)
  }
}

onMounted(() => {
  if (!chatStore.getMessages(currentUserId.value).length) {
    const mockMessages = [
      {
        id: '1',
        from: currentUserId.value,
        to: userStore.user?.id || 0,
        content: '你好呀! 很高兴认识你😊',
        type: 'text',
        created_at: new Date(Date.now() - 3600000).toISOString(),
        is_read: true
      },
      {
        id: '2',
        from: userStore.user?.id || 0,
        to: currentUserId.value,
        content: '你好! 我也是😄',
        type: 'text',
        created_at: new Date(Date.now() - 3000000).toISOString(),
        is_read: true
      },
      {
        id: '3',
        from: currentUserId.value,
        to: userStore.user?.id || 0,
        content: '今天天气真好~',
        type: 'text',
        created_at: new Date(Date.now() - 2400000).toISOString(),
        is_read: true
      }
    ]
    chatStore.setMessages(currentUserId.value, mockMessages)
    
    nextTick(() => {
      scrollTop.value = 99999
    })
  }
})
</script>

<style lang="scss" scoped>
.chat-detail-page {
  min-height: 100vh;
  background: #f5f5f5;
  display: flex;
  flex-direction: column;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 60rpx 32rpx 24rpx;
  background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
}

.header-left, .header-right {
  width: 80rpx;
}

.back-icon {
  font-size: 44rpx;
  color: #fff;
}

.header-center {
  flex: 1;
  text-align: center;
}

.chat-title {
  font-size: 34rpx;
  font-weight: bold;
  color: #fff;
}

.online-status {
  display: block;
  font-size: 22rpx;
  color: rgba(255, 255, 255, 0.7);
  margin-top: 4rpx;
}

.action-icon {
  font-size: 36rpx;
  float: right;
}

.messages-container {
  flex: 1;
  padding: 24rpx;
}

.messages-list {
  padding-bottom: 40rpx;
}

.message-item {
  display: flex;
  margin-bottom: 32rpx;
  
  &.is-self {
    flex-direction: row-reverse;
    
    .msg-avatar {
      margin-left: 16rpx;
      margin-right: 0;
    }
    
    .msg-bubble {
      background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
      border-radius: 24rpx 8rpx 24rpx 24rpx;
      
      .msg-text {
        color: #fff;
      }
    }
    
    .msg-content {
      align-items: flex-end;
    }
  }
}

.msg-avatar {
  width: 72rpx;
  height: 72rpx;
  border-radius: 50%;
  margin-right: 16rpx;
  flex-shrink: 0;
}

.msg-content {
  display: flex;
  flex-direction: column;
  max-width: 70%;
}

.msg-bubble {
  background: #fff;
  border-radius: 8rpx 24rpx 24rpx 24rpx;
  padding: 20rpx 24rpx;
  box-shadow: 0 2rpx 8rpx rgba(0, 0, 0, 0.06);
}

.msg-text {
  font-size: 30rpx;
  color: #333;
  line-height: 1.5;
}

.msg-image {
  max-width: 300rpx;
  border-radius: 8rpx;
}

.voice-content {
  display: flex;
  align-items: center;
  gap: 12rpx;
}

.voice-icon {
  font-size: 32rpx;
}

.voice-duration {
  font-size: 26rpx;
  color: #666;
}

.gift-content {
  display: flex;
  align-items: center;
  gap: 12rpx;
}

.gift-icon {
  font-size: 40rpx;
}

.gift-info {
  display: flex;
  flex-direction: column;
}

.gift-name {
  font-size: 28rpx;
  color: #fff;
}

.gift-points {
  font-size: 22rpx;
  color: rgba(255, 255, 255, 0.8);
}

.msg-time {
  font-size: 20rpx;
  color: #ccc;
  margin-top: 8rpx;
}

.input-bar {
  background: #fff;
  padding: 16rpx 24rpx;
  padding-bottom: calc(16rpx + constant(safe-area-inset-bottom));
  padding-bottom: calc(16rpx + env(safe-area-inset-bottom));
  display: flex;
  align-items: center;
  box-shadow: 0 -2rpx 12rpx rgba(0, 0, 0, 0.04);
}

.input-tools {
  display: flex;
  gap: 24rpx;
  margin-right: 20rpx;
}

.tool-icon {
  font-size: 40rpx;
}

.input-wrapper {
  flex: 1;
  display: flex;
  align-items: center;
  background: #f5f5f5;
  border-radius: 40rpx;
  padding: 0 24rpx;
}

.message-input {
  flex: 1;
  height: 72rpx;
  font-size: 30rpx;
}

.send-btn {
  font-size: 28rpx;
  color: #999;
  padding: 12rpx 24rpx;
  
  &.active {
    color: #f87c7c;
    font-weight: bold;
  }
}

.voice-wrapper {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-around;
}

.voice-hint {
  font-size: 26rpx;
  color: #999;
}

.voice-btn {
  width: 100rpx;
  height: 100rpx;
  background: #f87c7c;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.voice-mic {
  font-size: 40rpx;
}

.gift-panel {
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

.gift-panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 32rpx;
  border-bottom: 1rpx solid #eee;
}

.gift-title {
  font-size: 34rpx;
  font-weight: bold;
}

.gift-close {
  font-size: 36rpx;
  color: #999;
}

.gift-categories {
  white-space: nowrap;
  padding: 24rpx 32rpx;
  border-bottom: 1rpx solid #eee;
}

.category-item {
  display: inline-block;
  padding: 12rpx 28rpx;
  background: #f5f5f5;
  border-radius: 30rpx;
  font-size: 26rpx;
  margin-right: 16rpx;
  
  &.active {
    background: #f87c7c;
    color: #fff;
  }
}

.gift-list {
  max-height: 400rpx;
  padding: 24rpx;
}

.gift-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20rpx;
  float: left;
  width: 33.33%;
  
  &:active {
    background: #f9f9f9;
    border-radius: 16rpx;
  }
}

.gift-icon-large {
  font-size: 64rpx;
  margin-bottom: 12rpx;
}

.gift-name {
  font-size: 26rpx;
  color: #333;
  margin-bottom: 8rpx;
}

.gift-price {
  font-size: 22rpx;
  color: #f87c7c;
}

.gift-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24rpx 32rpx;
  border-top: 1rpx solid #eee;
}

.balance-info {
  font-size: 26rpx;
  color: #666;
}

.send-gift-btn {
  background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
  padding: 20rpx 48rpx;
  border-radius: 40rpx;
  
  text {
    color: #fff;
    font-size: 28rpx;
    font-weight: bold;
  }
}
</style>
