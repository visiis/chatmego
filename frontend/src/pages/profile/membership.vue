<template>
  <view class="membership-container">
    <view class="status-bar"></view>
    
    <view class="nav-bar">
      <view class="nav-left" @click="goBack">
        <FontAwesome name="arrow-left" size="24px" color="#fff" />
      </view>
      <view class="nav-center">
        <text class="nav-title">會員中心</text>
      </view>
      <view class="nav-right"></view>
    </view>
    
    <scroll-view class="content" scroll-y>
      <view class="current-level" v-if="user?.current_level">
        <view class="level-icon">
          <FontAwesome :name="user.current_level.icon || 'star'" size="48px" color="#ffd700" />
        </view>
        <text class="level-name">{{ user.current_level.name }}</text>
        <view class="progress-section">
          <view class="progress-bar">
            <view class="progress-fill" :style="{ width: progressPercent + '%' }"></view>
          </view>
          <text class="progress-text">{{ user.points || 0 }} / {{ nextLevelPoints || '∞' }} 積分</text>
        </view>
      </view>
      
      <view class="current-level" v-else>
        <view class="level-icon">
          <FontAwesome name="star" size="48px" color="#ccc" />
        </view>
        <text class="level-name">普通會員</text>
        <text class="progress-text">開始累積積分升級吧！</text>
      </view>
      
      <view class="membership-info" v-if="user?.has_membership && user.membership">
        <view class="membership-card">
          <view class="membership-badge">
            <FontAwesome name="crown" size="28px" color="#ffd700" />
          </view>
          <view class="membership-content">
            <text class="membership-name">{{ user.membership.name }}</text>
            <text class="membership-expire">有效期至：{{ formatDate(user.membership.expired_at) }}</text>
          </view>
        </view>
      </view>
      
      <view class="levels-section">
        <text class="section-title">會員等級</text>
        
        <view class="level-card" v-for="level in levels" :key="level.id" :class="{ current: isCurrentLevel(level) }">
          <view class="level-header">
            <FontAwesome :name="level.icon || 'star'" size="36px" :color="isCurrentLevel(level) ? '#ffd700' : '#ccc'" />
            <view class="level-info">
              <text class="level-name">{{ level.name }}</text>
              <text class="level-points">{{ level.min_points }} - {{ level.max_points || '∞' }} 積分</text>
            </view>
            <view class="level-badge" v-if="isCurrentLevel(level)">當前</view>
          </view>
          <view class="level-privileges">
            <view class="privilege-item" v-for="(privilege, idx) in level.privileges" :key="idx">
              <FontAwesome name="check" size="18px" :color="isCurrentLevel(level) ? '#2ed573' : '#999'" />
              <text>{{ privilege }}</text>
            </view>
          </view>
        </view>
        
        <view class="empty-state" v-if="levels.length === 0">
          <FontAwesome name="crown" size="60px" color="#ddd" />
          <text class="empty-text">暫無會員等級數據</text>
        </view>
      </view>
      
      <view class="points-section">
        <text class="section-title">積分詳情</text>
        <view class="points-grid">
          <view class="points-item">
            <text class="points-value">{{ user?.points || 0 }}</text>
            <text class="points-label">當前積分</text>
          </view>
          <view class="points-item">
            <text class="points-value">{{ user?.total_points_earned || 0 }}</text>
            <text class="points-label">累計獲得</text>
          </view>
          <view class="points-item">
            <text class="points-value">{{ user?.coins || 0 }}</text>
            <text class="points-label">金幣</text>
          </view>
        </view>
      </view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { getProfile, getMemberLevels, type UserProfile, type MemberLevel } from '../../api/user'

const user = ref<UserProfile | null>(null)
const levels = ref<MemberLevel[]>([])

const currentLevel = computed(() => {
  if (!user.value?.current_level) return null
  return levels.value.find(l => l.name === user.value?.current_level?.name) || null
})

const nextLevel = computed(() => {
  if (!currentLevel.value) return null
  const currentIndex = levels.value.findIndex(l => l.id === currentLevel.value?.id)
  return levels.value[currentIndex + 1] || null
})

const nextLevelPoints = computed(() => {
  return nextLevel.value?.min_points || null
})

const progressPercent = computed(() => {
  if (!currentLevel.value || !nextLevelPoints.value) return 100
  const total = nextLevelPoints.value - currentLevel.value.min_points
  const current = (user.value?.points || 0) - currentLevel.value.min_points
  return Math.min(100, Math.max(0, (current / total) * 100))
})

function isCurrentLevel(level: MemberLevel): boolean {
  if (!user.value?.current_level) return false
  return level.name === user.value.current_level.name
}

function formatDate(dateStr?: string): string {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`
}

onMounted(() => {
  loadProfile()
  loadLevels()
})

async function loadProfile() {
  try {
    user.value = await getProfile()
  } catch (error) {
    console.error('加載用戶資料失敗:', error)
  }
}

async function loadLevels() {
  try {
    levels.value = await getMemberLevels()
  } catch (error) {
    console.error('加載會員等級失敗:', error)
  }
}

function goBack() {
  uni.navigateBack()
}
</script>

<style lang="scss">
page {
  height: 100%;
}

.membership-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background: #f5f5f5;
}

.status-bar {
  height: var(--status-bar-height, 44px);
}

.nav-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
  padding: 16rpx 24rpx;
}

.nav-left, .nav-right {
  width: 80rpx;
  height: 80rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-title {
  font-size: 34rpx;
  color: #fff;
  font-weight: 600;
}

.content {
  flex: 1;
  padding: 24rpx;
}

.current-level {
  background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
  border-radius: 16rpx;
  padding: 32rpx;
  margin-bottom: 24rpx;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.level-icon {
  width: 120rpx;
  height: 120rpx;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16rpx;
}

.level-name {
  font-size: 36rpx;
  color: #fff;
  font-weight: bold;
  margin-bottom: 24rpx;
}

.progress-section {
  width: 100%;
}

.progress-bar {
  height: 16rpx;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 8rpx;
  overflow: hidden;
  margin-bottom: 12rpx;
}

.progress-fill {
  height: 100%;
  background: #fff;
  border-radius: 8rpx;
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 24rpx;
  color: rgba(255, 255, 255, 0.9);
  text-align: center;
  display: block;
}

.membership-info {
  margin-bottom: 24rpx;
}

.membership-card {
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  border-radius: 16rpx;
  padding: 24rpx;
  display: flex;
  align-items: center;
}

.membership-badge {
  width: 80rpx;
  height: 80rpx;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 20rpx;
}

.membership-content {
  flex: 1;
}

.membership-name {
  font-size: 32rpx;
  color: #fff;
  font-weight: bold;
  display: block;
  margin-bottom: 8rpx;
}

.membership-expire {
  font-size: 24rpx;
  color: rgba(255, 255, 255, 0.8);
}

.section-title {
  font-size: 32rpx;
  font-weight: 600;
  color: #333;
  margin-bottom: 20rpx;
  display: block;
}

.levels-section {
  margin-bottom: 32rpx;
}

.level-card {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
  margin-bottom: 20rpx;
  border: 2rpx solid #f0f0f0;
  
  &.current {
    border-color: #ffd700;
    background: linear-gradient(135deg, #fff 0%, #fffbf0 100%);
  }
}

.level-header {
  display: flex;
  align-items: center;
  margin-bottom: 16rpx;
}

.level-info {
  flex: 1;
  margin-left: 16rpx;
}

.level-name {
  font-size: 30rpx;
  font-weight: 600;
  color: #333;
  display: block;
}

.level-points {
  font-size: 24rpx;
  color: #999;
}

.level-badge {
  background: #ffd700;
  padding: 6rpx 20rpx;
  border-radius: 16rpx;
  
  text {
    font-size: 20rpx;
    color: #fff;
    font-weight: bold;
  }
}

.level-privileges {
  display: flex;
  flex-wrap: wrap;
  gap: 12rpx;
}

.privilege-item {
  display: flex;
  align-items: center;
  gap: 8rpx;
  padding: 8rpx 16rpx;
  background: #f5f5f5;
  border-radius: 8rpx;
  
  text {
    font-size: 22rpx;
    color: #666;
  }
  
  .current & {
    background: rgba(255, 215, 0, 0.1);
    
    text {
      color: #ff6b9d;
    }
  }
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 100rpx 0;
}

.empty-text {
  font-size: 28rpx;
  color: #999;
  margin-top: 20rpx;
}

.points-section {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
}

.points-grid {
  display: flex;
  justify-content: space-around;
}

.points-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.points-value {
  font-size: 40rpx;
  font-weight: bold;
  color: #ff6b9d;
}

.points-label {
  font-size: 24rpx;
  color: #999;
}
</style>
