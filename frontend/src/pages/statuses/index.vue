<template>
  <view class="statuses-container">
    <view class="publish-section" @click="showPublishModal">
      <image 
        v-if="userAvatar" 
        class="publish-avatar" 
        :src="userAvatar" 
        mode="aspectFill"
      />
      <view class="publish-input-wrap">
        <text class="publish-placeholder">發表你的心情...</text>
      </view>
    </view>
    
    <scroll-view 
      class="status-list" 
      scroll-y 
      @scrolltolower="loadMore"
      :refresher-enabled="true"
      :refresher-triggered="refreshing"
      @refresherrefresh="onRefresh"
    >
      <view 
        v-for="status in statuses" 
        :key="status.id" 
        class="status-card"
      >
        <view class="status-header">
          <image 
            class="status-avatar" 
            :src="status.user?.avatar || status.user?.avatar_url || defaultAvatar" 
            mode="aspectFill"
          />
          <view class="status-user-info">
            <text class="status-nickname">{{ status.user?.name || status.user?.nickname || '用戶' }}</text>
            <text class="status-time">{{ formatTime(status.created_at) }}</text>
          </view>
        </view>
        
        <view class="status-content">
          <text class="status-text">{{ status.content }}</text>
          <view class="status-images" v-if="status.images && status.images.length > 0">
            <image 
              v-for="(img, index) in status.images" 
              :key="index" 
              class="status-image" 
              :src="img" 
              mode="aspectFill"
              @click="previewImage(index, status.images)"
            />
          </view>
        </view>
        
        <view class="status-stats">
          <view class="stat-item" @click="toggleLike(status)">
            <FontAwesome 
              name="heart" 
              size="28px" 
              :color="status.is_liked ? '#ff6b9d' : '#999'" 
            />
            <text class="stat-text" :class="{ liked: status.is_liked }">{{ status.likes_count }}</text>
          </view>
          <view class="stat-item" @click="focusComment(status)">
            <FontAwesome name="comment" size="28px" color="#999" />
            <text class="stat-text">{{ status.comments_count }}</text>
          </view>
          <view class="stat-item">
            <FontAwesome name="share-alt" size="28px" color="#999" />
            <text class="stat-text">分享</text>
          </view>
        </view>
        
        <view class="status-comments" v-if="status.comments && status.comments.length > 0">
          <view 
            v-for="comment in status.comments" 
            :key="comment.id" 
            class="comment-item"
          >
            <text class="comment-author">{{ comment.user?.name || comment.user?.nickname || '用戶' }}</text>
            <text class="comment-text">{{ comment.content }}</text>
          </view>
        </view>
        
        <view class="comment-input-wrap">
          <input 
            class="comment-input" 
            :placeholder="'評論 ' + (status.user?.name || '用戶')"
            :value="commentInputs[status.id]"
            @input="e => commentInputs[status.id] = e.detail.value"
            @confirm="submitComment(status)"
          />
          <button class="comment-btn" @click="submitComment(status)">
            <text>發送</text>
          </button>
        </view>
      </view>
      
      <view class="loading-more" v-if="loading">
        <text>加載中...</text>
      </view>
      <view class="no-more" v-if="!loading && !hasMore">
        <text>沒有更多了</text>
      </view>
    </scroll-view>
    
    <view class="publish-modal" v-if="showPublish" @click="closePublishModal">
      <view class="publish-modal-content" @click.stop>
        <view class="publish-modal-header">
          <text class="publish-modal-title">發表說說</text>
          <text class="publish-modal-close" @click="closePublishModal">×</text>
        </view>
        <textarea 
          class="publish-textarea" 
          v-model="publishContent" 
          placeholder="分享你的心情..."
          :maxlength="500"
        />
        <view class="publish-image-section">
          <view class="publish-image-btn" @click="chooseImage">
            <FontAwesome name="plus" size="32px" color="#999" />
          </view>
          <image 
            v-for="(img, index) in publishImages" 
            :key="index" 
            class="publish-image-preview" 
            :src="img" 
            mode="aspectFill"
          />
        </view>
        <view class="publish-footer">
          <text class="publish-count">{{ publishContent.length }}/500</text>
          <button class="publish-submit-btn" :disabled="!publishContent.trim()" @click="submitStatus">
            <text>發表</text>
          </button>
        </view>
      </view>
    </view>
    
    <view class="bottom-tab">
      <view class="bottom-tab-item" @click="goDiscover">
        <FontAwesome name="compass" size="24px" color="#999" />
        <text class="tab-text">發現</text>
      </view>
      <view class="bottom-tab-item" @click="goFriends">
        <FontAwesome name="users" size="24px" color="#999" />
        <text class="tab-text">好友</text>
      </view>
      <view class="bottom-tab-item" @click="goChats">
        <FontAwesome name="comment" size="24px" color="#999" />
        <text class="tab-text">聊天</text>
      </view>
      <view class="bottom-tab-item active">
        <FontAwesome name="comment-dots" size="24px" color="#ff6b9d" />
        <text class="tab-text active">說說</text>
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
import { 
  getStatuses, 
  createStatus, 
  likeStatus, 
  commentStatus,
  type Status
} from '../../api/status'

const statuses = ref<Status[]>([])
const loading = ref(false)
const refreshing = ref(false)
const hasMore = ref(true)
const page = ref(1)

const showPublish = ref(false)
const publishContent = ref('')
const publishImages = ref<string[]>([])

const userAvatar = ref('')
const defaultAvatar = 'https://chatmego.com/images/default-avatar.svg'

const commentInputs = reactive<Record<number, string>>({})

onMounted(() => {
  loadUserInfo()
  loadStatuses()
})

function loadUserInfo() {
  const userStr = uni.getStorageSync('user')
  if (userStr) {
    const user = JSON.parse(userStr)
    userAvatar.value = user.avatar || user.avatar_url || ''
  }
}

async function loadStatuses(isRefresh = false) {
  if (isRefresh) {
    page.value = 1
    hasMore.value = true
    refreshing.value = true
  } else if (loading.value || !hasMore.value) {
    return
  }
  
  loading.value = true
  
  try {
    const result = await getStatuses(page.value, 10)
    const newStatuses = result.data || []
    
    if (isRefresh) {
      statuses.value = newStatuses
    } else {
      statuses.value = [...statuses.value, ...newStatuses]
    }
    
    hasMore.value = result.has_more || false
    
    page.value++
  } catch (error) {
    console.error('加載動態失敗:', error)
    uni.showToast({ title: '加載失敗', icon: 'none' })
  } finally {
    loading.value = false
    refreshing.value = false
  }
}

function loadMore() {
  loadStatuses()
}

function onRefresh() {
  loadStatuses(true)
}

function showPublishModal() {
  showPublish.value = true
}

function closePublishModal() {
  showPublish.value = false
  publishContent.value = ''
  publishImages.value = []
}

function chooseImage() {
  uni.chooseImage({
    count: 9 - publishImages.value.length,
    success: (res) => {
      publishImages.value = [...publishImages.value, ...res.tempFilePaths]
    }
  })
}

async function submitStatus() {
  if (!publishContent.value.trim()) {
    uni.showToast({ title: '請輸入內容', icon: 'none' })
    return
  }
  
  try {
    await createStatus({
      content: publishContent.value,
      images: publishImages.value
    })
    
    uni.showToast({ title: '發表成功', icon: 'success' })
    closePublishModal()
    loadStatuses(true)
  } catch (error) {
    console.error('發表失敗:', error)
    uni.showToast({ title: '發表失敗', icon: 'none' })
  }
}

async function toggleLike(status: Status) {
  try {
    await likeStatus(status.id)
    status.is_liked = !status.is_liked
    status.likes_count = status.is_liked ? (status.likes_count || 0) + 1 : Math.max(0, (status.likes_count || 0) - 1)
  } catch (error) {
    console.error('點贊失敗:', error)
  }
}

function focusComment(status: Status) {
  commentInputs[status.id] = ''
}

async function submitComment(status: Status) {
  const content = commentInputs[status.id]
  if (!content?.trim()) {
    uni.showToast({ title: '請輸入評論', icon: 'none' })
    return
  }
  
  try {
    const comment = await commentStatus(status.id, content)
    if (status.comments) {
      status.comments.push(comment)
    } else {
      status.comments = [comment]
    }
    status.comments_count = (status.comments_count || 0) + 1
    commentInputs[status.id] = ''
    uni.showToast({ title: '評論成功', icon: 'success' })
  } catch (error) {
    console.error('評論失敗:', error)
    uni.showToast({ title: '評論失敗', icon: 'none' })
  }
}

function previewImage(index: number, images: string[]) {
  uni.previewImage({
    current: images[index],
    urls: images
  })
}

function formatTime(dateStr: string | undefined): string {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const now = new Date()
  const diff = now.getTime() - date.getTime()
  
  const minute = 60 * 1000
  const hour = 60 * minute
  const day = 24 * hour
  const week = 7 * day
  
  if (diff < minute) return '剛才'
  if (diff < hour) return Math.floor(diff / minute) + '分鐘前'
  if (diff < day) return Math.floor(diff / hour) + '小時前'
  if (diff < week) return Math.floor(diff / day) + '天前'
  
  return `${date.getMonth() + 1}月${date.getDate()}日`
}

function goDiscover() {
  uni.switchTab({ url: '/pages/discover/cards' })
}

function goFriends() {
  uni.switchTab({ url: '/pages/friends/index' })
}

function goChats() {
  uni.switchTab({ url: '/pages/messages/index' })
}

function goProfile() {
  uni.switchTab({ url: '/pages/profile/index' })
}
</script>

<style lang="scss">
page {
  height: 100%;
  margin: 0;
  padding: 0;
}

.statuses-container {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: calc(120rpx + env(safe-area-inset-bottom));
}

.publish-section {
  background: #fff;
  margin: 20rpx;
  padding: 24rpx;
  border-radius: 16rpx;
  display: flex;
  align-items: center;
  
  &:active {
    background: #f8f9fa;
  }
}

.publish-avatar {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
  margin-right: 20rpx;
  background: #eee;
}

.publish-input-wrap {
  flex: 1;
}

.publish-placeholder {
  font-size: 30rpx;
  color: #999;
}

.status-list {
  height: calc(100vh - 280rpx - 120rpx - env(safe-area-inset-bottom));
}

.status-card {
  background: #fff;
  margin: 0 20rpx 20rpx;
  padding: 24rpx;
  border-radius: 16rpx;
}

.status-header {
  display: flex;
  align-items: center;
  margin-bottom: 20rpx;
}

.status-avatar {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
  margin-right: 16rpx;
  background: #eee;
}

.status-user-info {
  flex: 1;
}

.status-nickname {
  display: block;
  font-size: 30rpx;
  color: #333;
  font-weight: 500;
  margin-bottom: 4rpx;
}

.status-time {
  font-size: 24rpx;
  color: #999;
}

.status-content {
  margin-bottom: 20rpx;
}

.status-text {
  font-size: 32rpx;
  color: #333;
  line-height: 1.6;
}

.status-images {
  display: flex;
  flex-wrap: wrap;
  gap: 12rpx;
  margin-top: 16rpx;
}

.status-image {
  width: calc(33.33% - 8rpx);
  height: 200rpx;
  border-radius: 12rpx;
}

.status-stats {
  display: flex;
  gap: 48rpx;
  padding-bottom: 16rpx;
  border-bottom: 1rpx solid #f0f0f0;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 8rpx;
  
  &:active {
    opacity: 0.7;
  }
}

.stat-text {
  font-size: 26rpx;
  color: #999;
  
  &.liked {
    color: #ff6b9d;
  }
}

.status-comments {
  padding: 16rpx 0;
}

.comment-item {
  margin-bottom: 12rpx;
  
  &:last-child {
    margin-bottom: 0;
  }
}

.comment-author {
  font-size: 26rpx;
  color: #4a90d9;
  font-weight: 500;
  margin-right: 8rpx;
}

.comment-text {
  font-size: 26rpx;
  color: #333;
}

.comment-input-wrap {
  display: flex;
  align-items: center;
  gap: 16rpx;
  padding-top: 16rpx;
}

.comment-input {
  flex: 1;
  height: 72rpx;
  background: #f5f5f5;
  border-radius: 36rpx;
  padding: 0 24rpx;
  font-size: 28rpx;
}

.comment-btn {
  height: 72rpx;
  padding: 0 32rpx;
  background: #ff6b9d;
  border-radius: 36rpx;
  border: none;
  
  &::after {
    border: none;
  }
  
  text {
    color: #fff;
    font-size: 28rpx;
  }
}

.loading-more, .no-more {
  text-align: center;
  padding: 24rpx;
  font-size: 26rpx;
  color: #999;
}

.publish-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: flex-end;
  z-index: 1000;
}

.publish-modal-content {
  width: 100%;
  background: #fff;
  border-radius: 24rpx 24rpx 0 0;
  padding: 24rpx;
  padding-bottom: calc(24rpx + env(safe-area-inset-bottom));
}

.publish-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24rpx;
}

.publish-modal-title {
  font-size: 34rpx;
  color: #333;
  font-weight: bold;
}

.publish-modal-close {
  font-size: 48rpx;
  color: #999;
}

.publish-textarea {
  width: 100%;
  height: 200rpx;
  font-size: 32rpx;
  line-height: 1.6;
  border: 1rpx solid #eee;
  border-radius: 12rpx;
  padding: 16rpx;
  box-sizing: border-box;
}

.publish-image-section {
  display: flex;
  flex-wrap: wrap;
  gap: 16rpx;
  margin-top: 20rpx;
}

.publish-image-btn {
  width: 120rpx;
  height: 120rpx;
  border: 2rpx dashed #ddd;
  border-radius: 12rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.publish-image-preview {
  width: 120rpx;
  height: 120rpx;
  border-radius: 12rpx;
}

.publish-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 24rpx;
}

.publish-count {
  font-size: 26rpx;
  color: #999;
}

.publish-submit-btn {
  height: 80rpx;
  padding: 0 48rpx;
  background: #ff6b9d;
  border-radius: 40rpx;
  border: none;
  
  &::after {
    border: none;
  }
  
  &[disabled] {
    background: #ccc;
  }
  
  text {
    color: #fff;
    font-size: 30rpx;
    font-weight: 500;
  }
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
