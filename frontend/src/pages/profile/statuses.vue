<template>
  <view class="statuses-container">
    <view class="publish-area">
      <image class="publish-avatar" :src="userAvatar" mode="aspectFill" />
      <textarea 
        class="publish-input" 
        placeholder="分享你的心情..." 
        v-model="publishContent"
        :auto-height="true"
        :maxlength="2000"
      />
      <view class="publish-images" v-if="uploadImages.length > 0">
        <view class="publish-image-item" v-for="(img, index) in uploadImages" :key="index">
          <image class="publish-image" :src="img" mode="aspectFill" />
          <view class="remove-image" @click="removeUploadImage(index)">
            <FontAwesome name="times" size="20px" color="#fff" />
          </view>
        </view>
      </view>
      <view class="publish-footer">
        <view class="publish-action" @click="chooseImages">
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
    
    <scroll-view class="content" scroll-y>
      <view class="status-list">
        <view class="status-card" v-for="status in statuses" :key="status.id">
          <view class="status-header">
            <image 
              class="status-avatar" 
              :src="getStatusAvatar(status)" 
              mode="aspectFill" 
              @error="onAvatarError($event, status)"
            />
            <view class="status-user-info">
              <text class="status-user-name">{{ status.user?.name || status.user?.nickname || userName }}</text>
              <text class="status-time">{{ formatTime(status.created_at) }}</text>
            </view>
            <view class="status-options" v-if="isOwnStatus(status)" @click="showStatusOptions(status)">
              <FontAwesome name="ellipsis-h" size="20px" color="#999" />
            </view>
          </view>
          
          <text class="status-content" v-if="status.content">{{ status.content }}</text>
          
          <view class="status-images" v-if="status.images && status.images.length > 0">
            <image 
              class="status-image" 
              :src="img" 
              mode="widthFix" 
              v-for="(img, index) in status.images" 
              :key="index"
              @click="previewImage(index, status.images)"
            />
          </view>
          
          <view class="status-stats">
            <view class="stat-item" @click="toggleLike(status)">
              <FontAwesome 
                :name="status.liked ? 'heart' : 'heart-o'" 
                size="20px" 
                :color="status.liked ? '#ff6b9d' : '#999'" 
              />
              <text>{{ status.likes_count }}</text>
            </view>
            <view class="stat-item" @click="toggleComments(status)">
              <FontAwesome name="comment-o" size="20px" color="#999" />
              <text>{{ status.comments_count }}</text>
            </view>
          </view>
          
          <view class="status-comments" v-if="status.showComments">
            <view class="comment-item" v-for="comment in status.comments" :key="comment.id">
              <image class="comment-avatar" :src="getCommentAvatar(comment)" mode="aspectFill" />
              <view class="comment-content">
                <text class="comment-user">{{ comment.user.name }}</text>
                <text class="comment-text">{{ comment.content }}</text>
              </view>
              <text class="comment-time">{{ formatTime(comment.created_at) }}</text>
            </view>
            
            <view class="comment-input-area">
              <input 
                class="comment-input" 
                placeholder="写下你的评论..." 
                :value="commentInputs[status.id]"
                @input="e => commentInputs[status.id] = e.detail.value"
                @confirm="submitComment(status)"
              />
              <view class="send-comment-btn" @click="submitComment(status)">
                <text>发送</text>
              </view>
            </view>
          </view>
        </view>
        
        <view class="empty-state" v-if="statuses.length === 0 && !loading">
          <FontAwesome name="comment-dots" size="80px" color="#ddd" />
          <text class="empty-text">暂无朋友圈</text>
      <text class="empty-hint">在上方输入框发布第一条朋友圈吧</text>
        </view>
        
        <view class="loading-more" v-if="loading">
          <view class="loading-spinner-small"></view>
          <text>加载中...</text>
        </view>
      </view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { 
  getUserStatuses, 
  createStatus, 
  likeStatus, 
  commentStatus, 
  deleteStatus,
  type Status 
} from '../../api/status'
import { getProfile } from '../../api/user'

const statuses = ref<Status[]>([])
const publishContent = ref('')
const uploadImages = ref<string[]>([])
const isPrivate = ref(false)
const commentInputs = reactive<Record<number, string>>({})
const loading = ref(false)

const userAvatar = ref('')
const userName = ref('')
const userId = ref(0)
const defaultAvatar = 'https://chatmego.com/images/default-avatar.svg'

onMounted(() => {
  loadUserInfo()
})

function getStatusAvatar(status: Status): string {
  const user = status.user
  if (!user) return defaultAvatar
  
  let avatarUrl = user.avatar_url || user.avatar || ''
  
  if (!avatarUrl) {
    return defaultAvatar
  }
  
  if (avatarUrl.startsWith('http://') || avatarUrl.startsWith('https://')) {
    return avatarUrl
  }
  
  if (avatarUrl.startsWith('/storage/')) {
    avatarUrl = 'https://chatmego.com' + avatarUrl
  } else if (avatarUrl.startsWith('storage/')) {
    avatarUrl = 'https://chatmego.com/' + avatarUrl
  } else if (!avatarUrl.startsWith('/')) {
    avatarUrl = 'https://chatmego.com/storage/' + avatarUrl
  } else {
    avatarUrl = 'https://chatmego.com' + avatarUrl
  }
  
  return avatarUrl
}

function getCommentAvatar(comment: any): string {
  const user = comment.user
  if (!user) return defaultAvatar
  
  let avatarUrl = user.avatar_url || user.avatar || ''
  
  if (!avatarUrl) {
    return defaultAvatar
  }
  
  if (!avatarUrl.startsWith('http://') && !avatarUrl.startsWith('https://')) {
    if (avatarUrl.startsWith('/storage/')) {
      avatarUrl = 'https://chatmego.com' + avatarUrl
    } else if (avatarUrl.startsWith('storage/')) {
      avatarUrl = 'https://chatmego.com/' + avatarUrl
    } else if (!avatarUrl.startsWith('/')) {
      avatarUrl = 'https://chatmego.com/storage/' + avatarUrl
    } else {
      avatarUrl = 'https://chatmego.com' + avatarUrl
    }
  }
  
  return avatarUrl
}

function onAvatarError(e: any, status: Status) {
  if (status.user) {
    status.user.avatar = defaultAvatar
    status.user.avatar_url = defaultAvatar
  }
}

async function loadUserInfo() {
  try {
    const user = await getProfile()
    userAvatar.value = user.avatar || user.avatar_url || ''
    userName.value = user.name || user.nickname || ''
    userId.value = user.id || 0
    uni.setStorageSync('user', JSON.stringify(user))
    
    if (userId.value) {
      loadStatuses()
    }
  } catch (error) {
    console.error('加载用户信息失败:', error)
    const userStr = uni.getStorageSync('user')
    if (userStr) {
      const user = JSON.parse(userStr)
      userAvatar.value = user.avatar || user.avatar_url || ''
      userName.value = user.name || user.nickname || ''
      userId.value = user.id || 0
      if (userId.value) {
        loadStatuses()
      }
    }
  }
}

async function loadStatuses() {
  if (!userId.value) return
  
  loading.value = true
  
  try {
    const data = await getUserStatuses(userId.value)
    statuses.value = data || []
    
    statuses.value.forEach(s => {
      s.showComments = false
      commentInputs[s.id] = ''
    })
  } catch (error) {
    console.error('加载朋友圈失败:', error)
  } finally {
    loading.value = false
  }
}

function isOwnStatus(status: Status): boolean {
  return status.user_id === userId.value
}

function formatTime(dateStr: string): string {
  const date = new Date(dateStr)
  const now = new Date()
  const diff = now.getTime() - date.getTime()
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor(diff / (1000 * 60 * 60))
  const minutes = Math.floor(diff / (1000 * 60))
  
  if (days > 0) return `${days}天前`
  if (hours > 0) return `${hours}小时前`
  if (minutes > 0) return `${minutes}分钟前`
  return '刚刚'
}

function chooseImages() {
  uni.chooseImage({
    count: 9,
    success: (res) => {
      uploadImages.value = [...uploadImages.value, ...res.tempFilePaths].slice(0, 9)
    }
  })
}

function removeUploadImage(index: number) {
  uploadImages.value.splice(index, 1)
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
    await createStatus(publishContent.value, uploadImages.value, isPrivate.value)
    uni.showToast({ title: '发布成功', icon: 'success' })
    publishContent.value = ''
    uploadImages.value = []
    isPrivate.value = false
    loadStatuses()
  } catch (error) {
    console.error('发布失败:', error)
    uni.showToast({ title: '发布失败', icon: 'none' })
  }
}

function toggleLike(status: Status) {
  status.liked = !status.liked
  status.likes_count += status.liked ? 1 : -1
  likeStatus(status.id).catch(() => {
    status.liked = !status.liked
    status.likes_count += status.liked ? 1 : -1
  })
}

function toggleComments(status: Status) {
  status.showComments = !status.showComments
}

async function submitComment(status: Status) {
  const content = commentInputs[status.id] || ''
  if (!content.trim()) {
    uni.showToast({ title: '请输入评论内容', icon: 'none' })
    return
  }
  
  try {
    await commentStatus(status.id, content)
    commentInputs[status.id] = ''
    status.comments_count += 1
    loadStatuses()
    uni.showToast({ title: '评论成功', icon: 'success' })
  } catch (error) {
    console.error('评论失败:', error)
    uni.showToast({ title: '评论失败', icon: 'none' })
  }
}

function showStatusOptions(status: Status) {
  uni.showActionSheet({
    itemList: ['删除'],
    success: async (res) => {
      if (res.tapIndex === 0) {
        uni.showModal({
          title: '确认删除',
          content: '确定要删除这条朋友圈吗？',
          success: async (modalRes) => {
            if (modalRes.confirm) {
              try {
                await deleteStatus(status.id)
                statuses.value = statuses.value.filter(s => s.id !== status.id)
                uni.showToast({ title: '删除成功', icon: 'success' })
              } catch (error) {
                uni.showToast({ title: '删除失败', icon: 'none' })
              }
            }
          }
        })
      }
    }
  })
}

function previewImage(index: number, images: string[]) {
  uni.previewImage({
    current: images[index],
    urls: images
  })
}


</script>

<style lang="scss">
* {
  box-sizing: border-box;
}

.page {
  height: 100%;
  margin: 0;
  padding: 0;
}

.statuses-container {
  min-height: 100vh;
  background: #ffffff;
  display: flex;
  flex-direction: column;
}

.publish-area {
  background: #fff;
  padding: 24rpx;
  border-bottom: 1rpx solid #f0f0f0;
}

.publish-input {
  width: 100%;
  font-size: 28rpx;
  color: #333;
  background: #f8f9fa;
  border-radius: 12rpx;
  padding: 16rpx;
  min-height: 80rpx;
  height: 80rpx;
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

.content {
  flex: 1;
  padding: 24rpx;
  height: calc(100vh - 400rpx);
}

.status-list {
  display: flex;
  flex-direction: column;
  gap: 24rpx;
}

.status-card {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
}

.status-header {
  display: flex;
  align-items: center;
  margin-bottom: 16rpx;
}

.publish-avatar {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
  background: #fff;
  border: 4rpx solid #fff;
  box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.15);
  flex-shrink: 0;
}

.status-avatar {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
  background: #f0f0f0;
  flex-shrink: 0;
}

.status-user-info {
  flex: 1;
  margin-left: 16rpx;
}

.status-user-name {
  display: block;
  font-size: 30rpx;
  color: #333;
  font-weight: 500;
}

.status-time {
  font-size: 24rpx;
  color: #999;
}

.status-options {
  width: 60rpx;
  height: 60rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.status-content {
  font-size: 30rpx;
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
  margin-top: 20rpx;
  padding-top: 16rpx;
  border-top: 1rpx solid #f0f0f0;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 8rpx;
  
  text {
    font-size: 26rpx;
    color: #999;
  }
  
  &:active {
    opacity: 0.7;
  }
}

.status-comments {
  margin-top: 16rpx;
  padding-top: 16rpx;
  border-top: 1rpx solid #f0f0f0;
}

.comment-item {
  display: flex;
  gap: 12rpx;
  margin-bottom: 16rpx;
  
  &:last-of-type {
    margin-bottom: 0;
  }
}

.comment-avatar {
  width: 56rpx;
  height: 56rpx;
  border-radius: 50%;
  background: #f0f0f0;
  flex-shrink: 0;
}

.comment-content {
  flex: 1;
  background: #f8f9fa;
  border-radius: 16rpx;
  padding: 12rpx 16rpx;
}

.comment-user {
  font-size: 26rpx;
  color: #333;
  font-weight: 500;
}

.comment-text {
  font-size: 26rpx;
  color: #666;
  margin-left: 8rpx;
}

.comment-time {
  font-size: 22rpx;
  color: #ccc;
  flex-shrink: 0;
}

.comment-input-area {
  display: flex;
  gap: 12rpx;
  margin-top: 16rpx;
}

.comment-input {
  flex: 1;
  height: 72rpx;
  background: #f0f0f0;
  border-radius: 36rpx;
  padding: 0 24rpx;
  font-size: 28rpx;
}

.send-comment-btn {
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 0 32rpx;
  border-radius: 36rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 72rpx;
  
  text {
    font-size: 28rpx;
    color: #fff;
    line-height: 1;
  }
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 150rpx 60rpx;
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

.loading-more {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12rpx;
  padding: 24rpx;
  font-size: 26rpx;
  color: #999;
}

.loading-spinner-small {
  width: 32rpx;
  height: 32rpx;
  border: 3rpx solid #f0f0f0;
  border-top-color: #ff6b9d;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>