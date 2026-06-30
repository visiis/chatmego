<template>
  <view class="friends-page">
    <view class="content">
      <view class="tab-bar">
        <view 
          class="tab-item" 
          :class="{ active: activeTab === 'friends' }"
          @click="activeTab = 'friends'"
        >
          <text>好友列表</text>
        </view>
        <view 
          class="tab-item" 
          :class="{ active: activeTab === 'requests' }"
          @click="activeTab = 'requests'"
        >
          <text>好友請求</text>
          <view class="badge" v-if="requestCount > 0">{{ requestCount }}</view>
        </view>
        <view 
          class="tab-item" 
          :class="{ active: activeTab === 'blocked' }"
          @click="activeTab = 'blocked'"
        >
          <text>黑名單</text>
        </view>
      </view>
      
      <scroll-view class="list-content" scroll-y v-if="activeTab === 'friends'">
        <view class="friend-item" 
          v-for="friend in friends" 
          :key="friend.id" 
          @click="showFriendActions(friend)"
        >
          <image class="friend-avatar" :src="friend.avatar || 'https://chatmego.com/images/default-avatar.svg'" mode="aspectFill" />
          <view class="friend-info">
            <text class="friend-name">{{ friend.name || friend.nickname }}</text>
            <text class="friend-desc">{{ friend.love_declaration || '暂无签名' }}</text>
          </view>
          <view class="friend-actions">
            <view class="action-btn chat" @click.stop="startChat(friend)">
              <FontAwesome name="comment" size="24px" color="#4a90d9" />
            </view>
            <view class="action-btn more" @click.stop="showFriendActions(friend)">
              <FontAwesome name="ellipsis-v" size="24px" color="#999" />
            </view>
          </view>
        </view>
        
        <view class="empty-state" v-if="friends.length === 0">
          <FontAwesome name="users" size="80px" color="#ccc" />
          <text class="empty-text">暫時沒有好友</text>
          <text class="empty-hint">去發現頁面認識新朋友吧</text>
        </view>
      </scroll-view>
      
      <scroll-view class="list-content" scroll-y v-if="activeTab === 'requests'">
        <view class="request-item" v-for="request in requests" :key="request.id">
          <image class="request-avatar" :src="request.user.avatar || 'https://chatmego.com/images/default-avatar.svg'" mode="aspectFill" />
          <view class="request-info">
            <text class="request-name">{{ request.user.name || request.user.nickname }}</text>
            <text class="request-message">{{ request.message || '想加你為好友' }}</text>
          </view>
          <view class="request-actions">
            <view class="action-btn reject" @click="rejectRequest(request)">
              <text>拒絕</text>
            </view>
            <view class="action-btn accept" @click="acceptRequest(request)">
              <text>接受</text>
            </view>
          </view>
        </view>
        
        <view class="empty-state" v-if="requests.length === 0">
          <FontAwesome name="bell" size="80px" color="#ccc" />
          <text class="empty-text">暫時沒有好友請求</text>
        </view>
      </scroll-view>
      
      <scroll-view class="list-content" scroll-y v-if="activeTab === 'blocked'">
        <view 
          class="friend-item" 
          v-for="friend in blocked" 
          :key="friend.id" 
          @click="showUnblockAction(friend)"
        >
          <image class="friend-avatar" :src="friend.avatar || 'https://chatmego.com/images/default-avatar.svg'" mode="aspectFill" />
          <view class="friend-info">
            <text class="friend-name">{{ friend.name || friend.nickname }}</text>
            <text class="friend-desc">已被拉黑</text>
          </view>
          <view class="unblock-btn">
            <text>解除</text>
          </view>
        </view>
        
        <view class="empty-state" v-if="blocked.length === 0">
          <FontAwesome name="ban" size="80px" color="#ccc" />
          <text class="empty-text">黑名單是空的</text>
        </view>
      </scroll-view>
    </view>
    
    <view class="bottom-tab">
      <view class="bottom-tab-item" @click="goDiscover">
        <FontAwesome name="compass" size="24px" color="#999" />
        <text class="tab-text">發現</text>
      </view>
      <view class="bottom-tab-item active">
        <FontAwesome name="users" size="24px" color="#ff6b9d" />
        <text class="tab-text active">好友</text>
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
    
    <view class="action-modal" v-if="showModal" @click="showModal = false">
      <view class="modal-content" @click.stop>
        <view class="modal-item" @click="viewFriendProfile">
          <FontAwesome name="user-circle" size="28px" color="#4a90d9" />
          <text>查看資料</text>
        </view>
        <view class="modal-item" @click="startChatFromModal">
          <FontAwesome name="comment" size="28px" color="#2ed573" />
          <text>發送消息</text>
        </view>
        <view class="modal-item" @click="handleBlock">
          <FontAwesome name="ban" size="28px" color="#ffa502" />
          <text>加入黑名單</text>
        </view>
        <view class="modal-item danger" @click="handleDelete">
          <FontAwesome name="trash" size="28px" color="#ff4757" />
          <text>刪除好友</text>
        </view>
        <view class="modal-item cancel" @click="showModal = false">
          <text>取消</text>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { 
  getFriends, 
  getFriendRequests, 
  getBlocked,
  acceptFriendRequest, 
  rejectFriendRequest,
  blockFriend,
  unblockFriend,
  deleteFriend,
  type Friend, 
  type FriendRequest 
} from '../../api/friends'

const activeTab = ref('friends')
const requestCount = ref(0)
const showModal = ref(false)
const selectedFriend = ref<Friend | null>(null)

const friends = ref<Friend[]>([])
const requests = ref<FriendRequest[]>([])
const blocked = ref<Friend[]>([])

onMounted(() => {
  loadFriends()
  loadFriendRequests()
})

watch(activeTab, (newTab) => {
  if (newTab === 'blocked') {
    loadBlocked()
  }
})

async function loadFriends() {
  try {
    friends.value = await getFriends()
  } catch (error) {
    console.error('載入好友列表失敗:', error)
  }
}

async function loadFriendRequests() {
  try {
    const data = await getFriendRequests()
    requests.value = data
    requestCount.value = data.length
  } catch (error) {
    console.error('載入好友請求失敗:', error)
  }
}

async function loadBlocked() {
  try {
    blocked.value = await getBlocked()
  } catch (error) {
    console.error('載入黑名單失敗:', error)
  }
}

function viewFriend(friend: Friend) {
  const name = encodeURIComponent(friend.name || friend.nickname || '')
  const avatar = encodeURIComponent(friend.avatar || '')
  uni.navigateTo({ 
    url: `/pages/messages/detail?id=${friend.id}&name=${name}&avatar=${avatar}` 
  })
}

function viewFriendProfile() {
  if (!selectedFriend.value) return
  const name = encodeURIComponent(selectedFriend.value.name || selectedFriend.value.nickname || '')
  const avatar = encodeURIComponent(selectedFriend.value.avatar || '')
  uni.navigateTo({ 
    url: `/pages/profile/user-profile?id=${selectedFriend.value.id}&name=${name}&avatar=${avatar}` 
  })
  showModal.value = false
}

function startChat(friend: Friend) {
  const name = encodeURIComponent(friend.name || friend.nickname || '')
  const avatar = encodeURIComponent(friend.avatar || '')
  uni.navigateTo({ 
    url: `/pages/messages/detail?id=${friend.id}&name=${name}&avatar=${avatar}` 
  })
}

function startChatFromModal() {
  if (!selectedFriend.value) return
  startChat(selectedFriend.value)
  showModal.value = false
}

async function acceptRequest(request: FriendRequest) {
  try {
    await acceptFriendRequest(request.user.id)
    uni.showToast({ title: '已接受', icon: 'success' })
    requests.value = requests.value.filter(r => r.user.id !== request.user.id)
    requestCount.value = requests.value.length
    loadFriends()
  } catch (error) {
    console.error('接受好友請求失敗:', error)
    uni.showToast({ title: '操作失敗', icon: 'none' })
  }
}

async function rejectRequest(request: FriendRequest) {
  try {
    await rejectFriendRequest(request.user.id)
    uni.showToast({ title: '已拒絕', icon: 'none' })
    requests.value = requests.value.filter(r => r.user.id !== request.user.id)
    requestCount.value = requests.value.length
  } catch (error) {
    console.error('拒絕好友請求失敗:', error)
    uni.showToast({ title: '操作失敗', icon: 'none' })
  }
}

function showFriendActions(friend: Friend) {
  selectedFriend.value = friend
  showModal.value = true
}

async function handleBlock() {
  if (!selectedFriend.value) return
  
  uni.showModal({
    title: '加入黑名單',
    content: `確定要將 ${selectedFriend.value.name || selectedFriend.value.nickname} 加入黑名單嗎？`,
    success: async (res) => {
      if (res.confirm) {
        try {
          await blockFriend(selectedFriend.value!.id)
          uni.showToast({ title: '已拉黑', icon: 'success' })
          showModal.value = false
          loadFriends()
          loadBlocked()
        } catch (error) {
          console.error('拉黑失敗:', error)
          uni.showToast({ title: '操作失敗', icon: 'none' })
        }
      }
    }
  })
}

async function handleDelete() {
  if (!selectedFriend.value) return
  
  uni.showModal({
    title: '刪除好友',
    content: `確定要刪除 ${selectedFriend.value.name || selectedFriend.value.nickname} 嗎？`,
    success: async (res) => {
      if (res.confirm) {
        try {
          await deleteFriend(selectedFriend.value!.id)
          uni.showToast({ title: '已刪除', icon: 'success' })
          showModal.value = false
          loadFriends()
        } catch (error) {
          console.error('刪除失敗:', error)
          uni.showToast({ title: '操作失敗', icon: 'none' })
        }
      }
    }
  })
}

function showUnblockAction(friend: Friend) {
  uni.showModal({
    title: '解除拉黑',
    content: `確定要解除對 ${friend.name || friend.nickname} 的拉黑嗎？`,
    success: async (res) => {
      if (res.confirm) {
        try {
          await unblockFriend(friend.id)
          uni.showToast({ title: '已解除拉黑', icon: 'success' })
          loadBlocked()
        } catch (error) {
          uni.showToast({ title: '操作失敗', icon: 'none' })
        }
      }
    }
  })
}

function addFriend() {
  uni.showToast({ title: '添加好友功能開發中', icon: 'none' })
}

function goDiscover() {
  uni.switchTab({ url: '/pages/discover/cards' })
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
}

.friends-page {
  display: flex;
  flex-direction: column;
  height: 100vh;
  height: 100dvh;
  background: #f5f5f5;
}

.content {
  flex: 1;
  padding-bottom: calc(160rpx + env(safe-area-inset-bottom));
  overflow: hidden;
}

.tab-bar {
  display: flex;
  background: #fff;
  border-radius: 16rpx;
  padding: 8rpx;
  margin: 24rpx;
  box-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.03);
}

.tab-item {
  flex: 1;
  padding: 20rpx;
  text-align: center;
  border-radius: 12rpx;
  position: relative;
  transition: background 0.2s;
  
  text {
    font-size: 28rpx;
    color: #666;
  }
  
  &.active {
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
    
    text {
      color: #fff;
    }
  }
}

.badge {
  position: absolute;
  top: 8rpx;
  right: 20rpx;
  min-width: 32rpx;
  height: 32rpx;
  background: #ff4757;
  border-radius: 16rpx;
  font-size: 20rpx;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 8rpx;
}

.list-content {
  flex: 1;
  height: calc(100vh - 340rpx);
}

.friend-item {
  display: flex;
  align-items: center;
  background: #fff;
  padding: 24rpx 32rpx;
  border-bottom: 1rpx solid #f0f0f0;
  transition: background 0.2s;
  
  &:active {
    background: #f9f9f9;
  }
  
  &:last-child {
    border-bottom: none;
  }
}

.friend-avatar {
  width: 104rpx;
  height: 104rpx;
  border-radius: 50%;
  background: #f0f0f0;
  flex-shrink: 0;
}

.friend-info {
  flex: 1;
  margin-left: 24rpx;
  min-width: 0;
}

.friend-name {
  display: block;
  font-size: 32rpx;
  color: #333;
  font-weight: 500;
  margin-bottom: 8rpx;
}

.friend-desc {
  font-size: 26rpx;
  color: #999;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.friend-actions {
  display: flex;
  align-items: center;
  gap: 20rpx;
}

.action-btn {
  width: 64rpx;
  height: 64rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  border-radius: 50%;
  
  &.chat {
    background: rgba(74, 144, 217, 0.1);
  }
  
  &.more {
    background: rgba(153, 153, 153, 0.1);
  }
  
  &:active {
    opacity: 0.7;
  }
}

.unblock-btn {
  padding: 12rpx 24rpx;
  border: 1rpx solid #ff6b9d;
  border-radius: 24rpx;
  
  text {
    font-size: 26rpx;
    color: #ff6b9d;
  }
}

.request-item {
  display: flex;
  align-items: center;
  background: #fff;
  padding: 24rpx 32rpx;
  border-bottom: 1rpx solid #f0f0f0;
  
  &:last-child {
    border-bottom: none;
  }
}

.request-avatar {
  width: 104rpx;
  height: 104rpx;
  border-radius: 50%;
  background: #f0f0f0;
  flex-shrink: 0;
}

.request-info {
  flex: 1;
  margin-left: 24rpx;
}

.request-name {
  display: block;
  font-size: 32rpx;
  color: #333;
  font-weight: 500;
  margin-bottom: 8rpx;
}

.request-message {
  font-size: 26rpx;
  color: #999;
}

.request-actions {
  display: flex;
  gap: 16rpx;
}

.action-btn {
  padding: 16rpx 32rpx;
  border-radius: 24rpx;
  transition: opacity 0.2s;
  
  text {
    font-size: 26rpx;
  }
  
  &:active {
    opacity: 0.7;
  }
  
  &.accept {
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
    
    text {
      color: #fff;
    }
  }
  
  &.reject {
    background: #f0f0f0;
    
    text {
      color: #666;
    }
  }
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

.action-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  width: 500rpx;
  background: #fff;
  border-radius: 16rpx;
  overflow: hidden;
}

.modal-item {
  padding: 32rpx;
  text-align: center;
  border-bottom: 1rpx solid #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16rpx;
  
  text {
    font-size: 32rpx;
    color: #333;
  }
  
  &:last-child {
    border-bottom: none;
  }
  
  &:active {
    background: #f5f5f5;
  }
  
  &.cancel {
    text {
      color: #999;
    }
  }
  
  &.danger {
    text {
      color: #ff4757;
    }
  }
}
</style>
