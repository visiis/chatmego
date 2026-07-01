<template>
  <view class="statuses-container">
    <view class="status-bar"></view>
    
    <view class="publish-area">
      <image 
        class="publish-avatar" 
        :src="userAvatar" 
        mode="aspectFill"
      />
      <textarea 
        class="publish-input" 
        placeholder="分享你的心情..." 
        v-model="publishContent"
        :auto-height="true"
        :maxlength="2000"
      />
      <view class="publish-images" v-if="publishImages.length > 0">
        <view class="publish-image-item" v-for="(img, index) in publishImages" :key="index">
          <image class="publish-image" :src="img" mode="aspectFill" />
          <view class="remove-image" @click="removePublishImage(index)">
            <FontAwesome name="times" size="20px" color="#fff" />
          </view>
        </view>
      </view>
      <view class="publish-footer">
        <view class="publish-action" @click="chooseImage">
          <FontAwesome name="image" size="24px" color="#ff6b9d" />
          <text>图片</text>
        </view>
        <view class="publish-action" @click="togglePrivate">
          <FontAwesome :name="isPrivate ? 'lock' : 'lock-open'" size="24px" :color="isPrivate ? '#999' : '#ff6b9d'" />
          <text>{{ isPrivate ? '仅自己可见' : '公开' }}</text>
        </view>
        <view class="publish-btn" :class="{ disabled: !publishContent.trim() }" @click="submitStatus">
          <text>发布</text>
        </view>
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
            <text class="status-nickname">{{ status.user?.name || status.user?.nickname || '用户' }}</text>
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
              mode="widthFix"
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
            <text class="comment-author">{{ comment.user?.name || comment.user?.nickname || '用户' }}</text>
            <text class="comment-text">{{ comment.content }}</text>
          </view>
        </view>
        
        <view class="comment-input-wrap">
          <input 
            class="comment-input" 
            :placeholder="'评论 ' + (status.user?.name || '用户')"
            :value="commentInputs[status.id]"
            @input="e => commentInputs[status.id] = e.detail.value"
            @confirm="submitComment(status)"
          />
          <view class="comment-btn" @click="submitComment(status)">
            <text>发送</text>
          </view>
        </view>
      </view>
      
      <view class="loading-more" v-if="loading">
        <text>加载中...</text>
      </view>
      <view class="no-more" v-if="!loading && !hasMore">
        <text>没有更多了</text>
      </view>
    </scroll-view>
    
    <view class="bottom-tab">
      <view class="bottom-tab-item" @click="goDiscover">
        <FontAwesome name="compass" size="24px" color="#999" />
        <text class="tab-text">发现</text>
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
        <text class="tab-text active">朋友圈</text>
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
import { getProfile, type UserProfile } from '../../api/user'

const statuses = ref<Status[]>([])
const loading = ref(false)
const refreshing = ref(false)
const hasMore = ref(true)
const page = ref(1)

const publishContent = ref('')
const publishImages = ref<string[]>([])
const isPrivate = ref(false)

const userAvatar = ref('')
const userName = ref('')
const userId = ref(0)
const defaultAvatar = 'https://chatmego.com/images/default-avatar.svg'

const commentInputs = reactive<Record<number, string>>({})

onMounted(() => {
  loadUserInfo()
  loadStatuses()
})

async function loadUserInfo() {
  try {
    const user = await getProfile()
    userAvatar.value = user.avatar || user.avatar_url || ''
    userName.value = user.name || user.nickname || ''
    userId.value = user.id || 0
    uni.setStorageSync('user', JSON.stringify(user))
  } catch (error) {
    console.error('加载用户信息失败:', error)
    const userStr = uni.getStorageSync('user')
    if (userStr) {
      const user = JSON.parse(userStr)
      userAvatar.value = user.avatar || user.avatar_url || ''
      userName.value = user.name || user.nickname || ''
      userId.value = user.id || 0
    }
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
    console.error('加载动态失败:', error)
    uni.showToast({ title: '加载失败', icon: 'none' })
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

function chooseImage() {
  uni.chooseImage({
    count: 9 - publishImages.value.length,
    success: (res) => {
      publishImages.value = [...publishImages.value, ...res.tempFilePaths]
    }
  })
}

function removePublishImage(index: number) {
  publishImages.value.splice(index, 1)
}

function togglePrivate() {
  isPrivate.value = !isPrivate.value
}

async function submitStatus() {
  if (!publishContent.value.trim()) {
    uni.showToast({ title: '请输入内容', icon: 'none' })
    return
  }
  
  try {
    await createStatus(publishContent.value, publishImages.value, isPrivate.value)
    
    uni.showToast({ title: '发布成功', icon: 'success' })
    publishContent.value = ''
    publishImages.value = []
    isPrivate.value = false
    loadStatuses(true)
  } catch (error) {
    console.error('发布失败:', error)
    uni.showToast({ title: '发布失败', icon: 'none' })
  }
}

async function toggleLike(status: Status) {
  try {
    await likeStatus(status.id)
    status.is_liked = !status.is_liked
    status.likes_count = status.is_liked ? (status.likes_count || 0) + 1 : Math.max(0, (status.likes_count || 0) - 1)
  } catch (error) {
    console.error('点赞失败:', error)
  }
}

function focusComment(status: Status) {
  commentInputs[status.id] = ''
}

async function submitComment(status: Status) {
  const content = commentInputs[status.id]
  if (!content?.trim()) {
    uni.showToast({ title: '请输入评论', icon: 'none' })
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
    uni.showToast({ title: '评论成功', icon: 'success' })
  } catch (error) {
    console.error('评论失败:', error)
    uni.showToast({ title: '评论失败', icon: 'none' })
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
  
  if (diff < minute) return '刚刚'
  if (diff < hour) return Math.floor(diff / minute) + '分钟前'
  if (diff < day) return Math.floor(diff / hour) + '小时前'
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

.status-bar {
  height: var(--status-bar-height, 44px);
}

.publish-area {
  background: #fff;
  padding: 24rpx;
  border-bottom: 1rpx solid #f0f0f0;
}

.publish-avatar {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
  background: #f0f0f0;
  margin-bottom: 16rpx;
}

.publish-input {
  width: 100%;
  font-size: 30rpx;
  color: #333;
  background: #f8f9fa;
  border-radius: 12rpx;
  padding: 16rpx;
  min-height: 120rpx;
  margin-bottom: 16rpx;
}

.publish-images {
  display: flex;
  flex-wrap: wrap;
  gap: 12rpx;
  margin-bottom: 16rpx;
}

.publish-image-item {
  width: calc(33.33% - 8rpx);
  aspect-ratio: 1;
  position: relative;
  border-radius: 8rpx;
  overflow: hidden;
}

.publish-image {
  width: 100%;
  height: 100%;
}

.remove-image {
  position: absolute;
  top: 8rpx;
  right: 8rpx;
  width: 40rpx;
  height: 40rpx;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.publish-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.publish-action {
  display: flex;
  align-items: center;
  gap: 8rpx;
  
  text {
    font-size: 26rpx;
    color: #ff6b9d;
  }
}

.publish-btn {
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 0 40rpx;
  border-radius: 24rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 72rpx;
  
  text {
    font-size: 28rpx;
    color: #fff;
    font-weight: 500;
    line-height: 1;
  }
  
  &.disabled {
    opacity: 0.5;
  }
}

.status-list {
  height: calc(100vh - 400rpx - 120rpx - env(safe-area-inset-bottom));
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
  gap: 8rpx;
  margin-top: 16rpx;
}

.status-image {
  width: calc(33.33% - 6rpx);
  border-radius: 8rpx;
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
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  border-radius: 36rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  
  text {
    color: #fff;
    font-size: 28rpx;
    line-height: 1;
  }
}

.loading-more, .no-more {
  text-align: center;
  padding: 24rpx;
  font-size: 26rpx;
  color: #999;
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