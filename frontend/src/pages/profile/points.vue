<template>
  <view class="points-container">
    <view class="status-bar"></view>
    
    <view class="nav-bar">
      <view class="nav-left" @click="goBack">
        <FontAwesome name="arrow-left" size="24px" color="#fff" />
      </view>
      <view class="nav-center">
        <text class="nav-title">積分中心</text>
      </view>
      <view class="nav-right"></view>
    </view>
    
    <scroll-view class="content" scroll-y>
      <view class="balance-section">
        <view class="balance-card points-card">
          <view class="balance-icon">
            <FontAwesome name="star" size="36px" color="#ff6b9d" />
          </view>
          <view class="balance-info">
            <text class="balance-value">{{ user?.points || 0 }}</text>
            <text class="balance-label">當前積分</text>
          </view>
        </view>
        
        <view class="balance-card coins-card">
          <view class="balance-icon">
            <FontAwesome name="coins" size="36px" color="#ffd700" />
          </view>
          <view class="balance-info">
            <text class="balance-value">{{ user?.coins || 0 }}</text>
            <text class="balance-label">當前金幣</text>
          </view>
        </view>
      </view>
      
      <view class="convert-section">
        <text class="section-title">積分兌換</text>
        <view class="convert-card">
          <view class="convert-info">
            <text class="convert-desc">100 積分 = 1 金幣</text>
            <text class="convert-tip">* 兑换积分必须是100的倍数</text>
          </view>
          
          <view class="convert-input-group">
            <view class="input-wrapper">
              <FontAwesome name="star" size="24px" color="#ff6b9d" />
              <input type="number" class="convert-input" v-model="convertPoints" placeholder="請輸入兌換積分" />
            </view>
            <text class="convert-rate">→ {{ convertCoins }} 金幣</text>
          </view>
          
          <view class="quick-select">
            <view class="quick-btn" @click="selectPoints(100)">100</view>
            <view class="quick-btn" @click="selectPoints(500)">500</view>
            <view class="quick-btn" @click="selectPoints(1000)">1000</view>
            <view class="quick-btn" @click="selectPoints(5000)">5000</view>
          </view>
          
          <view class="convert-btn" :class="{ disabled: !canConvert }" @click="handleConvert">
            <FontAwesome name="exchange" size="24px" color="#fff" />
            <text>立即兌換</text>
          </view>
        </view>
      </view>
      
      <view class="tabs">
        <view class="tab-item" :class="{ active: activeTab === 'points' }" @click="activeTab = 'points'">
          <FontAwesome name="star" size="20px" :color="activeTab === 'points' ? '#ff6b9d' : '#999'" />
          <text>積分記錄</text>
        </view>
        <view class="tab-item" :class="{ active: activeTab === 'coins' }" @click="activeTab = 'coins'">
          <FontAwesome name="coins" size="20px" :color="activeTab === 'coins' ? '#ffd700' : '#999'" />
          <text>金幣記錄</text>
        </view>
      </view>
      
      <view class="history-section">
        <view class="history-item" v-for="record in currentRecords" :key="record.id">
          <view class="history-icon" :class="record.amount > 0 ? 'income' : 'expense'">
            <FontAwesome :name="record.amount > 0 ? 'plus' : 'minus'" size="20px" :color="record.amount > 0 ? '#27ae60' : '#e74c3c'" />
          </view>
          <view class="history-info">
            <text class="history-reason">{{ record.reason }}</text>
            <text class="history-time">{{ formatDate(record.created_at) }}</text>
          </view>
          <text class="history-amount" :class="record.amount > 0 ? 'income' : 'expense'">
            {{ record.amount > 0 ? '+' : '' }}{{ record.amount }}
          </text>
        </view>
        
        <view class="empty-state" v-if="currentRecords.length === 0">
          <FontAwesome name="history" size="60px" color="#ddd" />
          <text class="empty-text">暫無記錄</text>
        </view>
      </view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { getProfile, type UserProfile } from '../../api/user'
import { getPointsHistory, getCoinsHistory, convertToCoins, type PointsLog, type CoinsLog } from '../../api/points'

const user = ref<UserProfile | null>(null)
const activeTab = ref<'points' | 'coins'>('points')
const convertPoints = ref('')
const pointsRecords = ref<PointsLog[]>([])
const coinsRecords = ref<CoinsLog[]>([])

const convertCoins = computed(() => {
  const points = parseInt(convertPoints.value) || 0
  return Math.floor(points / 100)
})

const canConvert = computed(() => {
  const points = parseInt(convertPoints.value) || 0
  return points >= 100 && points % 100 === 0 && points <= (user.value?.points || 0)
})

const currentRecords = computed(() => {
  return activeTab.value === 'points' ? pointsRecords.value : coinsRecords.value
})

function selectPoints(points: number) {
  convertPoints.value = points.toString()
}

async function handleConvert() {
  if (!canConvert.value) return
  
  const points = parseInt(convertPoints.value)
  
  uni.showModal({
    title: '確認兌換',
    content: `將 ${points} 積分兌換為 ${convertCoins.value} 金幣`,
    success: async (res) => {
      if (res.confirm) {
        try {
          await convertToCoins(points)
          uni.showToast({ title: '兌換成功', icon: 'success' })
          convertPoints.value = ''
          loadProfile()
          loadHistory()
        } catch (error) {
          console.error('兌換失敗:', error)
          const errMsg = (error as any)?.message || ''
          uni.showToast({ title: errMsg || '兌換失敗', icon: 'none' })
        }
      }
    }
  })
}

function formatDate(dateStr?: string): string {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`
}

onMounted(() => {
  loadProfile()
  loadHistory()
})

async function loadProfile() {
  try {
    user.value = await getProfile()
  } catch (error) {
    console.error('加載用戶資料失敗:', error)
  }
}

async function loadHistory() {
  try {
    const pointsRes = await getPointsHistory()
    pointsRecords.value = pointsRes.records
    
    const coinsRes = await getCoinsHistory()
    coinsRecords.value = coinsRes.records
  } catch (error) {
    console.error('加載記錄失敗:', error)
  }
}

function goBack() {
  uni.navigateBack()
}
</script>

<style lang="scss">
page {
  height: 100%;
  margin: 0;
  padding: 0;
  background: #ffffff;
}

.points-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.status-bar {
  height: var(--status-bar-height, 44px);
}

.nav-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 16rpx 24rpx;
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

.nav-title {
  font-size: 34rpx;
  color: #fff;
  font-weight: 600;
}

.content {
  flex: 1;
  padding: 24rpx;
  width: 100%;
  box-sizing: border-box;
  overflow-x: hidden;
}

.balance-section {
  display: flex;
  gap: 16rpx;
  margin-bottom: 24rpx;
}

.balance-card {
  flex: 1;
  border-radius: 16rpx;
  padding: 24rpx;
  display: flex;
  align-items: center;
}

.points-card {
  background: linear-gradient(135deg, #fff 0%, #fff0f5 100%);
  border: 2rpx solid #ffb6c1;
}

.coins-card {
  background: linear-gradient(135deg, #fff 0%, #fffbf0 100%);
  border: 2rpx solid #ffd700;
}

.balance-icon {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
  background: rgba(255, 107, 157, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 16rpx;
}

.coins-card .balance-icon {
  background: rgba(255, 215, 0, 0.1);
}

.balance-info {
  flex: 1;
}

.balance-value {
  display: block;
  font-size: 40rpx;
  font-weight: bold;
  color: #ff6b9d;
}

.coins-card .balance-value {
  color: #ffd700;
}

.balance-label {
  display: block;
  font-size: 24rpx;
  color: #999;
}

.section-title {
  font-size: 32rpx;
  font-weight: 600;
  color: #333;
  margin-bottom: 16rpx;
  display: block;
}

.convert-section {
  margin-bottom: 24rpx;
}

.convert-card {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
}

.convert-info {
  margin-bottom: 24rpx;
}

.convert-desc {
  display: block;
  font-size: 30rpx;
  color: #333;
  font-weight: 500;
  margin-bottom: 8rpx;
}

.convert-tip {
  display: block;
  font-size: 24rpx;
  color: #999;
}

.convert-input-group {
  display: flex;
  align-items: center;
  gap: 16rpx;
  margin-bottom: 24rpx;
}

.input-wrapper {
  flex: 1;
  display: flex;
  align-items: center;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  border-radius: 12rpx;
  padding: 0 20rpx;
  height: 88rpx;
}

.convert-input {
  flex: 1;
  font-size: 30rpx;
  margin-left: 12rpx;
}

.convert-rate {
  font-size: 28rpx;
  color: #ff6b9d;
  font-weight: 500;
}

.quick-select {
  display: flex;
  gap: 16rpx;
  margin-bottom: 24rpx;
}

.quick-btn {
  flex: 1;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  border-radius: 12rpx;
  padding: 16rpx;
  text-align: center;
  font-size: 28rpx;
  color: #666;
  
  &:active {
    background: #ff6b9d;
    color: #fff;
  }
}

.convert-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12rpx;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 24rpx;
  border-radius: 12rpx;
  
  text {
    font-size: 32rpx;
    color: #fff;
    font-weight: 500;
  }
  
  &.disabled {
    background: #ccc;
    
    text {
      color: #999;
    }
  }
}

.tabs {
  display: flex;
  background: #fff;
  border-radius: 12rpx;
  padding: 8rpx;
  margin-bottom: 24rpx;
}

.tab-item {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8rpx;
  padding: 20rpx;
  border-radius: 8rpx;
  font-size: 28rpx;
  color: #999;
  
  &.active {
    background: rgba(255, 107, 157, 0.1);
    color: #ff6b9d;
    font-weight: 500;
  }
}

.history-section {
  background: #fff;
  border-radius: 16rpx;
  padding: 16rpx;
}

.history-item {
  display: flex;
  align-items: center;
  padding: 20rpx 0;
  border-bottom: 1rpx solid #eee;
  
  &:last-child {
    border-bottom: none;
  }
}

.history-icon {
  width: 64rpx;
  height: 64rpx;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 16rpx;
  
  &.income {
    background: rgba(39, 174, 96, 0.1);
  }
  
  &.expense {
    background: rgba(231, 76, 60, 0.1);
  }
}

.history-info {
  flex: 1;
}

.history-reason {
  display: block;
  font-size: 28rpx;
  color: #333;
  margin-bottom: 8rpx;
}

.history-time {
  display: block;
  font-size: 22rpx;
  color: #999;
}

.history-amount {
  font-size: 30rpx;
  font-weight: 500;
  
  &.income {
    color: #27ae60;
  }
  
  &.expense {
    color: #e74c3c;
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
  margin-top: 24rpx;
}
</style>
