<template>
  <view class="user-profile-container">
    <scroll-view class="profile-content" scroll-y>
      <view class="profile-header">
        <image 
          v-if="userAvatar" 
          class="avatar" 
          :src="userAvatar" 
          mode="aspectFill"
          @error="onAvatarError"
        />
        <view class="avatar-placeholder" v-else>
          <text class="avatar-text">{{ user?.name?.charAt(0) || '?' }}</text>
        </view>
        
        <text class="nickname">{{ user?.name || '用戶' }}</text>
        
        <view class="badges">
          <view class="vip-badge" v-if="user?.has_membership">
            <FontAwesome name="star" size="14px" color="#ffd700" />
            <text class="badge-text">{{ user.membership?.name || 'VIP' }}</text>
          </view>
          <view class="level-badge" v-if="user?.current_level">
            <FontAwesome :name="user.current_level.icon || 'star'" size="14px" color="#ff6b9d" />
            <text class="badge-text">{{ user.current_level.name }}</text>
          </view>
        </view>
        
        <view class="points-info">
          <view class="point-item">
            <text class="points-value">{{ user?.points || 0 }}</text>
            <text class="points-label">積分</text>
          </view>
          <view class="point-divider"></view>
          <view class="point-item">
            <text class="points-value">{{ user?.coins || 0 }}</text>
            <text class="points-label">金幣</text>
          </view>
        </view>
        
        <view class="action-buttons">
          <view class="action-btn chat" @click="startChat">
            <FontAwesome name="comment" size="24px" color="#fff" />
            <text>聊天</text>
          </view>
          <view 
            class="action-btn attraction" 
            :class="{ liked: attractionStatus === 1, disliked: attractionStatus === 2 }"
            @click="handleAttraction"
          >
            <FontAwesome name="heart" size="24px" color="#fff" />
            <text>{{ attractionStatus === 1 ? '已讚' : attractionStatus === 2 ? '已踩' : '好感' }}</text>
          </view>
          <view class="action-btn add-friend" @click="sendFriendRequest">
            <FontAwesome name="user-plus" size="24px" color="#fff" />
            <text>加為好友</text>
          </view>
        </view>
      </view>
      
      <view class="detail-section">
        <view class="detail-item">
          <view class="detail-icon">
            <FontAwesome name="venus-mars" size="24px" color="#ff6b9d" />
          </view>
          <text class="detail-label">性別</text>
          <text class="detail-value">{{ genderText }}</text>
        </view>
        
        <view class="detail-item">
          <view class="detail-icon">
            <FontAwesome name="calendar" size="24px" color="#4a90d9" />
          </view>
          <text class="detail-label">年齡</text>
          <text class="detail-value">{{ user?.age ? user.age + '歲' : '未填' }}</text>
        </view>
        
        <view class="detail-item">
          <view class="detail-icon">
            <FontAwesome name="tree" size="24px" color="#ff9f43" />
          </view>
          <text class="detail-label">身高</text>
          <text class="detail-value">{{ user?.height ? user.height + 'cm' : '未填' }}</text>
        </view>
        
        <view class="detail-item">
          <view class="detail-icon">
            <FontAwesome name="weight" size="24px" color="#e74c3c" />
          </view>
          <text class="detail-label">體重</text>
          <text class="detail-value">{{ user?.weight ? user.weight + 'kg' : '未填' }}</text>
        </view>
        
        <view class="detail-item">
          <view class="detail-icon">
            <FontAwesome name="heart" size="24px" color="#ff6b9d" />
          </view>
          <text class="detail-label">愛情宣言</text>
          <text class="detail-value">{{ user?.love_declaration || '未填' }}</text>
        </view>
        
        <view class="detail-item">
          <view class="detail-icon">
            <FontAwesome name="gamepad" size="24px" color="#9b59b6" />
          </view>
          <text class="detail-label">興趣愛好</text>
          <text class="detail-value">{{ user?.hobbies || '未填' }}</text>
        </view>
        
        <view class="detail-item">
          <view class="detail-icon">
            <FontAwesome name="graduation-cap" size="24px" color="#3498db" />
          </view>
          <text class="detail-label">專長</text>
          <text class="detail-value">{{ user?.specialty || '未填' }}</text>
        </view>
      </view>
      
      <view class="member-section" v-if="user?.membership">
        <view class="section-header">
          <FontAwesome name="crown" size="28px" color="#ffd700" />
          <text class="section-title">會員信息</text>
        </view>
        <view class="member-card">
          <view class="member-icon">
            <FontAwesome name="star" size="48px" color="#ffd700" />
          </view>
          <view class="member-info">
            <text class="member-name">{{ user.membership.name }}</text>
            <text class="member-expire">到期時間: {{ formatExpireDate(user.membership.expired_at) }}</text>
          </view>
        </view>
      </view>
      
      <view class="created-section">
        <view class="detail-item">
          <view class="detail-icon">
            <FontAwesome name="user-plus" size="24px" color="#999" />
          </view>
          <text class="detail-label">註冊時間</text>
          <text class="detail-value">{{ formatDate(user?.created_at) }}</text>
        </view>
      </view>
    </scroll-view>
  </view>
</template>

<script lang="ts">
import { sendFriendRequest } from '../../api/friends'

export default {
  onNavigationBarButtonTap(e: any) {
    if (e.index === 0) {
      uni.showActionSheet({
        itemList: ['加為好友'],
        success: async (res) => {
          if (res.tapIndex === 0) {
            const pages = getCurrentPages()
            const currentPage = pages[pages.length - 1]
            const userId = (currentPage as any).$vm?.userId || (currentPage as any).userId || 0
            try {
              await sendFriendRequest(userId)
              uni.showToast({ title: '已發送好友請求', icon: 'success' })
            } catch (error) {
              console.error('發送好友請求失敗:', error)
              uni.showToast({ title: '發送失敗', icon: 'none' })
            }
          }
        }
      })
    }
  }
}
</script>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { getUserProfile, type UserProfile } from '../../api/user'
import { likeUser, dislikeUser, cancelAttraction, getAttractionStatus } from '../../api/attraction'
import { sendFriendRequest as apiSendFriendRequest } from '../../api/friends'

const user = ref<UserProfile | null>(null)
const userId = ref(0)
const userName = ref('')
const userAvatarUrl = ref('')
const avatarError = ref(false)
const attractionStatus = ref(0)

const userAvatar = computed(() => {
  if (avatarError.value) return ''
  return user.value?.avatar || user.value?.avatar_url || userAvatarUrl.value || ''
})

const genderText = computed(() => {
  const gender = user.value?.gender
  if (gender === 'male') return '男'
  if (gender === 'female') return '女'
  return '未填'
})

onMounted(() => {
  const pages = getCurrentPages()
  const currentPage = pages[pages.length - 1]
  const options = (currentPage as any).$page?.options || (currentPage as any).options || {}
  
  userId.value = parseInt(options.id || '0')
  userName.value = decodeURIComponent(options.name || options.nickname || '')
  userAvatarUrl.value = decodeURIComponent(options.avatar || '')
  
  if (userId.value > 0) {
    loadUserProfile()
  }
})

function onAvatarError() {
  avatarError.value = true
}

async function loadUserProfile() {
  try {
    user.value = await getUserProfile(userId.value)
    loadAttractionStatus()
  } catch (error) {
    console.error('加載用戶資料失敗:', error)
    uni.showToast({ title: '加載失敗', icon: 'none' })
  }
}

async function loadAttractionStatus() {
  try {
    const status = await getAttractionStatus(userId.value)
    attractionStatus.value = status.my_type || 0
  } catch (e) {
    console.error('获取好感度状态失败:', e)
  }
}

function goBack() {
  uni.navigateBack()
}

function startChat() {
  if (!userId.value) return
  const name = encodeURIComponent(user.value?.name || userName.value || '')
  const avatar = encodeURIComponent(user.value?.avatar || user.value?.avatar_url || userAvatarUrl.value || '')
  uni.navigateTo({ 
    url: `/pages/messages/detail?id=${userId.value}&name=${name}&avatar=${avatar}` 
  })
}

async function handleAttraction() {
  const options = ['讚', '踩']
  
  uni.showActionSheet({
    itemList: options,
    success: async (res) => {
      try {
        if (res.tapIndex === 0) {
          if (attractionStatus.value === 1) {
            await cancelAttraction(userId.value)
            attractionStatus.value = 0
            uni.showToast({ title: '已取消', icon: 'none' })
          } else {
            await likeUser(userId.value)
            attractionStatus.value = 1
            uni.showToast({ title: '已讚', icon: 'success' })
          }
        } else {
          if (attractionStatus.value === 2) {
            await cancelAttraction(userId.value)
            attractionStatus.value = 0
            uni.showToast({ title: '已取消', icon: 'none' })
          } else {
            await dislikeUser(userId.value)
            attractionStatus.value = 2
            uni.showToast({ title: '已踩', icon: 'none' })
          }
        }
      } catch (error) {
        console.error('操作失败:', error)
        uni.showToast({ title: '操作失敗', icon: 'none' })
      }
    }
  })
}

async function sendFriendRequest() {
  if (!userId.value) return
  
  try {
    await apiSendFriendRequest(userId.value)
    uni.showToast({ title: '已發送好友請求', icon: 'success' })
  } catch (error) {
    console.error('發送好友請求失敗:', error)
    uni.showToast({ title: (error as any)?.message || '發送失敗', icon: 'none' })
  }
}

function formatDate(dateStr?: string): string {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`
}

function formatExpireDate(dateStr?: string): string {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return `${date.getFullYear()}年${date.getMonth() + 1}月${date.getDate()}日`
}
</script>

<style lang="scss">
page {
  height: 100%;
  margin: 0;
  padding: 0;
  background: #ffffff;
}

.user-profile-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  height: 100dvh;
}

.profile-content {
  flex: 1;
  padding-bottom: calc(40rpx + env(safe-area-inset-bottom));
}

.profile-header {
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 60rpx 32rpx;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.avatar {
  width: 200rpx;
  height: 200rpx;
  border-radius: 50%;
  border: 6rpx solid rgba(255, 255, 255, 0.5);
}

.avatar-placeholder {
  width: 200rpx;
  height: 200rpx;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 6rpx solid rgba(255, 255, 255, 0.5);
}

.avatar-text {
  font-size: 64rpx;
  color: #fff;
  font-weight: bold;
}

.nickname {
  font-size: 40rpx;
  color: #fff;
  font-weight: 600;
  margin-top: 24rpx;
}

.badges {
  display: flex;
  gap: 16rpx;
  margin-top: 16rpx;
}

.vip-badge, .level-badge {
  display: flex;
  align-items: center;
  gap: 8rpx;
  background: rgba(255, 255, 255, 0.2);
  padding: 8rpx 16rpx;
  border-radius: 20rpx;
}

.badge-text {
  font-size: 24rpx;
  color: #fff;
}

.points-info {
  display: flex;
  align-items: center;
  gap: 48rpx;
  margin-top: 32rpx;
  background: rgba(255, 255, 255, 0.15);
  padding: 24rpx 60rpx;
  border-radius: 16rpx;
}

.point-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.points-value {
  font-size: 40rpx;
  color: #fff;
  font-weight: bold;
}

.points-label {
  font-size: 24rpx;
  color: rgba(255, 255, 255, 0.8);
}

.point-divider {
  width: 2rpx;
  height: 60rpx;
  background: rgba(255, 255, 255, 0.3);
}

.action-buttons {
  display: flex;
  gap: 24rpx;
  margin-top: 40rpx;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 8rpx;
  padding: 16rpx 32rpx;
  border-radius: 40rpx;
  font-size: 26rpx;
  color: #fff;
}

.action-btn.chat {
  background: rgba(255, 255, 255, 0.2);
}

.action-btn.attraction {
  background: rgba(255, 255, 255, 0.2);
}

.action-btn.attraction.liked {
  background: #ff6b9d;
}

.action-btn.attraction.disliked {
  background: #ff4757;
}

.action-btn.add-friend {
  background: rgba(255, 255, 255, 0.2);
}

.detail-section, .member-section, .created-section {
  background: #fff;
  margin: 20rpx;
  padding: 24rpx;
  border-radius: 16rpx;
}

.detail-item {
  display: flex;
  align-items: center;
  padding: 20rpx 0;
  border-bottom: 1rpx solid #eee;
}

.detail-item:last-child {
  border-bottom: none;
}

.detail-icon {
  width: 60rpx;
  height: 60rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 20rpx;
}

.detail-label {
  flex: 1;
  font-size: 28rpx;
  color: #999;
}

.detail-value {
  font-size: 28rpx;
  color: #333;
  font-weight: 500;
  flex: 2;
  text-align: right;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 12rpx;
  margin-bottom: 20rpx;
}

.section-title {
  font-size: 30rpx;
  color: #333;
  font-weight: 600;
}

.member-card {
  display: flex;
  align-items: center;
  gap: 24rpx;
  background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
  padding: 24rpx;
  border-radius: 16rpx;
}

.member-icon {
  width: 80rpx;
  height: 80rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.member-info {
  flex: 1;
}

.member-name {
  display: block;
  font-size: 32rpx;
  color: #333;
  font-weight: 600;
  margin-bottom: 8rpx;
}

.member-expire {
  font-size: 26rpx;
  color: #666;
}
</style>