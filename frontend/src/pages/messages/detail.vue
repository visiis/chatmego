<template>
  <view class="chat-container">
    <view class="status-bar"></view>
    
    <view class="nav-bar">
      <view class="nav-left" @click="goBack">
        <text class="nav-icon">‹</text>
      </view>
      <view class="nav-center">
        <text class="nav-title">{{ friendName }}</text>
      </view>
      <view class="nav-right">
        <text class="nav-icon">⋯</text>
      </view>
    </view>
    
    <scroll-view 
      class="chat-content"
      scroll-y
      :scroll-top="scrollTop"
      enhanced
      :show-scrollbar="false"
    >
      <view class="messages-wrapper">
        <view v-if="messages.length === 0 && !isLoaded" class="loading-state">
          <view class="loading-spinner"></view>
          <text class="loading-text">加載中...</text>
        </view>
        
        <template v-else>
          <view 
            v-if="hasMoreHistory" 
            class="history-btn-wrapper"
          >
            <view class="history-btn" @click="loadMoreHistory">
              <FontAwesome name="history" size="20px" color="#ff6b9d" />
              <text>查看歷史記錄</text>
            </view>
          </view>
          
          <view 
            class="message-item" 
            :class="{ 'me': item.isMe }"
            v-for="(item, index) in messages"
            :key="item.id"
            :id="'msg-' + index"
          >
            <image 
              class="message-avatar" 
              :src="item.isMe ? myAvatar : friendAvatar" 
              mode="aspectFill"
            />
            <view class="message-bubble" :class="{ 'me': item.isMe, 'gift': item.type === 'gift' }">
              <view v-if="item.type === 'gift'" class="gift-message">
                <image 
                  v-if="item.giftImage" 
                  class="gift-image" 
                  :src="item.giftImage" 
                  mode="aspectFit" 
                />
                <view class="gift-name-bg" :class="{ 'me': item.isMe }">
                  <FontAwesome class="gift-icon" name="gift" size="16px" color="#ff6b9d" />
                  <text class="gift-name">{{ item.giftName }}</text>
                </view>
              </view>
              <view v-else-if="item.type === 'image'" class="image-message">
                <image 
                  class="message-image" 
                  :src="item.content" 
                  mode="widthFix" 
                  @click="previewImage(item.content)" 
                />
              </view>
              <text v-else class="message-content">{{ item.content }}</text>
              <view class="message-footer">
                <text class="message-time">{{ item.time }}</text>
                <view v-if="item.isMe && item.isRead" class="read-status">
                  <FontAwesome name="check" size="10px" color="#4fc3f7" />
                  <FontAwesome name="check" size="10px" color="#4fc3f7" class="read-check-second" />
                </view>
                <view v-else-if="item.isMe" class="read-status">
                  <FontAwesome name="check" size="10px" color="#999" />
                </view>
              </view>
            </view>
          </view>
          
          <view class="empty-state" v-if="messages.length === 0 && !loading">
            <FontAwesome name="comment-slash" size="48px" color="#ccc" />
            <text class="empty-text">還沒有消息，發送第一條消息開始聊天吧！</text>
          </view>
        </template>
      </view>
    </scroll-view>
    
    <view class="gift-panel" v-if="showGiftPanel">
      <view class="gift-header">
        <text class="gift-title">選擇禮物</text>
        <view class="gift-close" @click="showGiftPanel = false">
          <FontAwesome name="times" size="28px" color="#999" />
        </view>
      </view>
      <scroll-view scroll-y class="gift-list">
        <view 
          class="gift-item" 
          v-for="gift in gifts" 
          :key="gift.id"
          @click="selectGift(gift)"
        >
          <image 
            class="gift-thumb" 
            :src="gift.image_thumbnail || gift.image" 
            mode="aspectFill" 
          />
          <text class="gift-item-name">{{ gift.name }}</text>
          <view class="gift-item-price">
            <text v-if="gift.price_type === 'activity_points'" class="price-icon">💎</text>
            <text v-else class="price-icon">💰</text>
            <text class="price-text">{{ gift.price }}</text>
          </view>
        </view>
        <view class="no-gifts" v-if="gifts.length === 0">
          <FontAwesome name="gift" size="48px" color="#ccc" />
          <text>暫無可用禮物</text>
        </view>
      </scroll-view>
    </view>
    
    <view class="input-bar">
      <view class="input-actions">
        <view class="action-btn" @click="toggleGift">
          <FontAwesome name="gift" size="36px" color="#666" />
        </view>
      </view>
      <view class="input-wrapper">
        <input 
          v-model="inputText" 
          class="input-field" 
          type="text" 
          placeholder="輸入消息..."
          placeholder-class="input-placeholder"
          @confirm="sendMessage"
        />
      </view>
      <view 
        class="send-btn" 
        :class="{ active: inputText.trim() }"
        @click="sendMessage"
      >
        <FontAwesome name="paper-plane" size="24px" color="#fff" />
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick, onUnmounted } from 'vue'
import { 
  getMessages, 
  sendMessage as apiSendMessage, 
  getGifts, 
  sendGift as apiSendGift,
  fetchHistoryMessages
} from '../../api/chat'
import { request } from '../../utils/request'
import FontAwesome from '../../components/FontAwesome.vue'

const userId = ref(0)
const friendId = ref(0)
const friendName = ref('')
const friendAvatar = ref('')
const myAvatar = ref('')
const myId = ref(0)

const inputText = ref('')
const scrollTop = ref(0)
const showGiftPanel = ref(false)
const gifts = ref<any[]>([])
const loading = ref(false)
const isSendingMessage = ref(false)
const hasMoreHistory = ref(true)
const isLoadingHistory = ref(false)
const isLoaded = ref(false)

interface ChatMessage {
  id: number
  content: string
  time: string
  isMe: boolean
  isRead: boolean
  type: string
  giftName?: string
  giftImage?: string
  from_user_id?: number
  to_user_id?: number
}

const messages = ref<ChatMessage[]>([])

onMounted(() => {
  initPage()
})

onUnmounted(() => {
  showGiftPanel.value = false
  saveCache()
})

const DEFAULT_AVATAR = 'https://chatmego.com/images/default-avatar.svg'

function getCacheKey(): string {
  return `chat_messages_${myId.value}_${friendId.value}`
}

function loadCache(): ChatMessage[] {
  try {
    const cached = uni.getStorageSync(getCacheKey())
    if (cached) {
      return JSON.parse(cached)
    }
  } catch (e) {
    console.error('读取缓存失败:', e)
  }
  return []
}

function saveCache() {
  try {
    uni.setStorageSync(getCacheKey(), JSON.stringify(messages.value))
  } catch (e) {
    console.error('保存缓存失败:', e)
  }
}

async function initPage() {
  const pages = getCurrentPages()
  const currentPage = pages[pages.length - 1]
  const options = (currentPage as any).$page?.options || (currentPage as any).options || {}
  
  friendId.value = parseInt(options.id || '0')
  userId.value = friendId.value
  friendName.value = decodeURIComponent(options.name || options.nickname || '好友')
  friendAvatar.value = decodeURIComponent(options.avatar || '') || DEFAULT_AVATAR
  
  await loadUserInfo()
  
  loadMessages()
}

async function loadUserInfo() {
  const userStr = uni.getStorageSync('user')
  if (userStr) {
    try {
      const user = JSON.parse(userStr)
      myAvatar.value = user.avatar || user.avatar_url || DEFAULT_AVATAR
      myId.value = parseInt(user.id) || 0
      return
    } catch (e) {
      console.error('解析用户信息失败:', e)
    }
  }
  
  myAvatar.value = DEFAULT_AVATAR
  
  try {
    const token = uni.getStorageSync('token')
    if (token) {
      const response = await request('/api/user/profile', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      if (response.data) {
        myId.value = parseInt(response.data.id) || 0
        myAvatar.value = response.data.avatar || response.data.avatar_url || DEFAULT_AVATAR
        uni.setStorageSync('user', JSON.stringify(response.data))
      }
    }
  } catch (e) {
    console.error('获取用户信息失败:', e)
  }
}

async function loadMessages() {
  const cachedMessages = loadCache()
  
  if (cachedMessages.length > 0) {
    messages.value = cachedMessages.map(msg => {
      return {
        ...msg,
        isMe: parseInt(msg.from_user_id) === myId.value
      }
    })
    hasMoreHistory.value = messages.value.length >= 20
    isLoaded.value = true
    
    nextTick(() => {
      scrollTop.value = 999999
    })
  }
  
  try {
    const data = await getMessages(friendId.value)
    const messageList = Array.isArray(data) ? data : (data?.messages || [])
    
    console.log('服务器返回消息:', messageList)
    
    if (messageList.length > 0) {
      const serverMessages = messageList.map(msg => formatMessage(msg))
      
      if (cachedMessages.length === 0) {
        messages.value = serverMessages
      } else {
        const cachedIds = new Set(messages.value.map(m => m.id))
        for (const msg of serverMessages) {
          if (!cachedIds.has(msg.id)) {
            messages.value.push(msg)
          }
        }
        
        messages.value = [...new Map(messages.value.map(m => [m.id, m])).values()]
        messages.value.sort((a, b) => a.id - b.id)
      }
      
      hasMoreHistory.value = messageList.length >= 20
      isLoaded.value = true
      
      nextTick(() => {
        scrollTop.value = 999999
      })
      
      saveCache()
    } else {
      if (cachedMessages.length === 0) {
        isLoaded.value = true
      }
    }
  } catch (error) {
    console.error('載入消息失敗:', error)
    if (cachedMessages.length === 0) {
      isLoaded.value = true
    }
  } finally {
    loading.value = false
  }
}

async function loadMoreHistory() {
  if (isLoadingHistory.value || !hasMoreHistory.value || messages.value.length === 0) {
    return
  }
  
  isLoadingHistory.value = true
  
  try {
    const oldestMessage = messages.value[0]
    const historyMessages = await fetchHistoryMessages(friendId.value, oldestMessage.id, 20)
    
    if (historyMessages.length > 0) {
      const formattedMessages = historyMessages.map(msg => formatMessage(msg))
      const existingIds = new Set(messages.value.map(m => m.id))
      
      const filteredMessages = formattedMessages.filter(m => !existingIds.has(m.id))
      
      messages.value = [...filteredMessages, ...messages.value]
      
      if (historyMessages.length < 20) {
        hasMoreHistory.value = false
      }
    } else {
      hasMoreHistory.value = false
    }
    
    saveCache()
  } catch (error) {
    console.error('載入歷史消息失敗:', error)
  } finally {
    isLoadingHistory.value = false
  }
}

function formatMessage(msg: any): ChatMessage {
  let content = msg.message
  let giftName = ''
  let giftImage = ''
  
  if (msg.type === 'gift') {
    try {
      const giftData = JSON.parse(msg.message)
      giftName = giftData.gift_name || ''
      giftImage = giftData.gift_image || msg.attachment_url || ''
      content = ''
    } catch (e) {
      content = '[禮物]'
    }
  } else if (msg.type === 'image') {
    content = msg.attachment_url || msg.message
  }
  
  const fromUserId = msg.from_user_id != null ? parseInt(msg.from_user_id) : 0
  const toUserId = msg.to_user_id != null ? parseInt(msg.to_user_id) : 0
  
  return {
    id: msg.id,
    content,
    time: formatTime(msg.created_at),
    isMe: fromUserId === myId.value,
    isRead: msg.is_read,
    type: msg.type,
    giftName,
    giftImage,
    from_user_id: fromUserId,
    to_user_id: toUserId
  }
}

function formatTime(dateStr: string): string {
  const date = new Date(dateStr)
  const hours = date.getHours().toString().padStart(2, '0')
  const minutes = date.getMinutes().toString().padStart(2, '0')
  return `${hours}:${minutes}`
}

function scrollToBottom() {
  setTimeout(() => {
    scrollTop.value = 999999
  }, 50)
}

async function sendMessage() {
  const content = inputText.value.trim()
  if (!content) return
  
  try {
    isSendingMessage.value = true
    
    const tempId = Date.now()
    const now = new Date()
    const timeStr = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`
    
    const tempMsg: ChatMessage = {
      id: tempId,
      content,
      time: timeStr,
      isMe: true,
      isRead: false,
      type: 'text',
      from_user_id: myId.value,
      to_user_id: friendId.value
    }
    
    messages.value.push(tempMsg)
    inputText.value = ''
    
    nextTick(() => {
      scrollToBottom()
    })
    
    const result = await apiSendMessage(friendId.value, content)
    
    console.log('发送消息结果:', result)
    
    const index = messages.value.findIndex(m => m.id === tempId)
    if (index !== -1) {
      messages.value[index].id = result.id
      messages.value[index].isRead = result.is_read
      messages.value[index].time = formatTime(result.created_at)
      messages.value[index].from_user_id = parseInt(result.from_user_id) || myId.value
      messages.value[index].to_user_id = parseInt(result.to_user_id) || friendId.value
    }
    
    nextTick(() => {
      saveCache()
    })
  } catch (error) {
    console.error('發送消息失敗:', error)
    uni.showToast({ title: '發送失敗', icon: 'none' })
    
    const index = messages.value.findIndex(m => m.id === tempId)
    if (index !== -1) {
      messages.value.splice(index, 1)
    }
  } finally {
    isSendingMessage.value = false
  }
}

function goBack() {
  uni.navigateBack({
    fail: () => {
      uni.switchTab({ url: '/pages/messages/index' })
    }
  })
}

function toggleGift() {
  showGiftPanel.value = !showGiftPanel.value
  
  if (showGiftPanel.value && gifts.value.length === 0) {
    loadGifts()
  }
}

async function loadGifts() {
  try {
    const data = await getGifts()
    gifts.value = Array.isArray(data) ? data : (data?.gifts || [])
  } catch (error) {
    console.error('載入禮物失敗:', error)
  }
}

async function selectGift(gift: any) {
  try {
    const result = await apiSendGift(friendId.value, gift.id)
    
    showGiftPanel.value = false
    
    const msg = formatMessage(result)
    messages.value.push(msg)
    
    nextTick(() => {
      scrollToBottom()
    })
    
    saveCache()
    
    uni.showToast({ title: '禮物發送成功', icon: 'success' })
  } catch (error) {
    console.error('發送禮物失敗:', error)
    uni.showToast({ title: '發送失敗', icon: 'none' })
  }
}

function previewImage(url: string) {
  uni.previewImage({
    urls: [url],
    current: url
  })
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
  background: #f5f5f5;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  width: 100%;
  overflow-x: hidden;
}

.chat-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  height: 100dvh;
  background: #f5f5f5;
  width: 100%;
  overflow: hidden;
}

.status-bar {
  height: var(--status-bar-height, 44px);
  flex-shrink: 0;
}

.nav-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 16rpx 24rpx;
  flex-shrink: 0;
  position: relative;
  z-index: 100;
}

.nav-left, .nav-right {
  width: 80rpx;
  height: 80rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-icon {
  font-size: 48rpx;
  color: #fff;
  font-weight: bold;
}

.nav-center {
  flex: 1;
  text-align: center;
}

.nav-title {
  font-size: 34rpx;
  color: #fff;
  font-weight: 500;
}

.chat-content {
  flex: 1;
  width: 100%;
  overflow: hidden;
}

.messages-wrapper {
  padding: 24rpx 0;
  padding-bottom: 180rpx;
  min-height: 100%;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 200rpx 0;
}

.history-btn-wrapper {
  text-align: center;
  padding: 16rpx 0;
}

.history-btn {
  display: inline-flex;
  align-items: center;
  gap: 8rpx;
  padding: 12rpx 24rpx;
  background: rgba(255, 107, 157, 0.1);
  border-radius: 32rpx;
  font-size: 24rpx;
  color: #ff6b9d;
  
  &:active {
    background: rgba(255, 107, 157, 0.2);
  }
}

.loading-spinner {
  width: 60rpx;
  height: 60rpx;
  border: 4rpx solid #f0f0f0;
  border-top-color: #ff6b9d;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 20rpx;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.loading-text {
  font-size: 28rpx;
  color: #999;
}

.message-item {
  display: flex;
  align-items: flex-end;
  margin-bottom: 24rpx;
  padding: 0 24rpx;
  
  &.me {
    flex-direction: row-reverse;
    
    .message-bubble {
      background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
      
      .message-content {
        color: #fff;
      }
      
      .message-time {
        color: rgba(255, 255, 255, 0.7);
      }
      
      .read-status {
        color: rgba(255, 255, 255, 0.7);
      }
      
      &.gift {
        .gift-name-bg {
          background: rgba(255, 255, 255, 0.25);
          color: #fff;
        }
      }
    }
  }
}

.message-avatar {
  width: 72rpx;
  height: 72rpx;
  border-radius: 50%;
  background: #f0f0f0;
  flex-shrink: 0;
}

.message-bubble {
  max-width: 75%;
  background: #fff;
  border-radius: 24rpx;
  padding: 20rpx 24rpx;
  margin: 0 16rpx;
  box-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.05);
  word-break: break-all;
  overflow: hidden;
  
  &.me {
    border-bottom-right-radius: 8rpx;
  }
  
  &:not(.me) {
    border-bottom-left-radius: 8rpx;
  }
  
  &.gift {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 24rpx;
  }
}

.message-content {
  font-size: 30rpx;
  color: #333;
  line-height: 1.5;
  word-break: break-all;
}

.message-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8rpx;
  margin-top: 8rpx;
}

.message-time {
  font-size: 22rpx;
  color: #999;
}

.read-status {
  display: flex;
  align-items: center;
}

.read-check-second {
  margin-left: -6rpx;
}

.gift-message {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.gift-image {
  width: 160rpx;
  height: 160rpx;
  border-radius: 16rpx;
  margin-bottom: 12rpx;
}

.gift-name-bg {
  display: flex;
  align-items: center;
  gap: 8rpx;
  background: #f5f5f5;
  border-radius: 8rpx;
  padding: 8rpx 16rpx;
  
  &.me {
    background: rgba(255, 255, 255, 0.25);
  }
}

.gift-icon {
  font-size: 24rpx;
}

.gift-name {
  font-size: 26rpx;
  color: #333;
  font-weight: 500;
  
  .me & {
    color: #fff;
  }
}

.image-message {
  max-width: 400rpx;
  overflow: hidden;
}

.message-image {
  width: 100%;
  border-radius: 12rpx;
  max-width: 400rpx;
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

.gift-panel {
  position: fixed;
  bottom: 140rpx;
  left: 0;
  right: 0;
  background: #fff;
  border-radius: 32rpx 32rpx 0 0;
  box-shadow: 0 -4rpx 20rpx rgba(0, 0, 0, 0.1);
  z-index: 200;
  max-height: 60vh;
  display: flex;
  flex-direction: column;
}

.gift-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24rpx 32rpx;
  border-bottom: 1rpx solid #eee;
}

.gift-title {
  font-size: 32rpx;
  color: #333;
  font-weight: 500;
}

.gift-close {
  width: 56rpx;
  height: 56rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.gift-list {
  flex: 1;
  padding: 24rpx;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20rpx;
}

.gift-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16rpx;
  border-radius: 16rpx;
  background: #f9f9f9;
  transition: background 0.2s;
  
  &:active {
    background: #f0f0f0;
  }
}

.gift-thumb {
  width: 100rpx;
  height: 100rpx;
  border-radius: 12rpx;
  margin-bottom: 12rpx;
}

.gift-item-name {
  font-size: 22rpx;
  color: #333;
  text-align: center;
  margin-bottom: 8rpx;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 100%;
}

.gift-item-price {
  display: flex;
  align-items: center;
  gap: 4rpx;
  font-size: 20rpx;
  color: #ff6b9d;
}

.price-icon {
  font-size: 20rpx;
}

.price-text {
  font-size: 20rpx;
  color: #ff6b9d;
}

.no-gifts {
  grid-column: 1/-1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 60rpx;
  color: #999;
  font-size: 26rpx;
}

.input-bar {
  display: flex;
  align-items: center;
  background: #fff;
  padding: 16rpx 24rpx;
  padding-bottom: calc(16rpx + env(safe-area-inset-bottom));
  border-top: 1rpx solid #eee;
  flex-shrink: 0;
  width: 100%;
}

.input-actions {
  display: flex;
  gap: 24rpx;
  flex-shrink: 0;
}

.action-btn {
  width: 64rpx;
  height: 64rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.input-wrapper {
  flex: 1;
  background: #f5f5f5;
  border-radius: 32rpx;
  margin: 0 16rpx;
}

.input-field {
  width: 100%;
  height: 72rpx;
  padding: 0 28rpx;
  font-size: 30rpx;
}

.input-placeholder {
  color: #ccc;
}

.send-btn {
  width: 72rpx;
  height: 72rpx;
  border-radius: 50%;
  background: #ddd;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.3s ease;
  
  &.active {
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  }
}
</style>