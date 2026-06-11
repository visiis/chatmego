<template>
  <view class="gifts-page">
    <view class="header">
      <text class="back-btn" @click="goBack">←</text>
      <text class="page-title">礼物中心</text>
      <view class="placeholder"></view>
    </view>
    
    <view class="tabs">
      <view 
        class="tab-item" 
        :class="{ active: activeTab === 'received' }"
        @click="activeTab = 'received'"
      >
        <text>收到的礼物</text>
        <view class="tab-badge" v-if="receivedCount > 0">
          <text>{{ receivedCount }}</text>
        </view>
      </view>
      <view 
        class="tab-item" 
        :class="{ active: activeTab === 'sent' }"
        @click="activeTab = 'sent'"
      >
        <text>送出的礼物</text>
      </view>
      <view 
        class="tab-item" 
        :class="{ active: activeTab === 'redeem' }"
        @click="activeTab = 'redeem'"
      >
        <text>积分兑换</text>
      </view>
    </view>
    
    <view class="content" v-if="activeTab === 'received'">
      <view class="total-points">
        <text class="points-label">累计收到积分</text>
        <text class="points-value">{{ totalPointsReceived }}</text>
        <view class="convert-btn" @click="convertAllPoints">
          <text>全部兑换</text>
        </view>
      </view>
      
      <view class="gifts-list">
        <view 
          class="gift-item" 
          v-for="gift in receivedGifts" 
          :key="gift.id"
        >
          <image 
            class="sender-avatar" 
            :src="gift.sender.avatar || 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20person&image_size=square'" 
            mode="aspectFill" 
          />
          <view class="gift-info">
            <view class="gift-header">
              <text class="sender-name">{{ gift.sender.nickname }}</text>
              <text class="gift-time">{{ formatTime(gift.created_at) }}</text>
            </view>
            <view class="gift-content">
              <text class="gift-icon">{{ getGiftIcon(gift.gift_id) }}</text>
              <text class="gift-name">{{ getGiftName(gift.gift_id) }}</text>
              <text class="gift-points">+{{ gift.points }}积分</text>
            </view>
          </view>
          <view 
            class="convert-icon" 
            v-if="!gift.converted"
            @click="convertGift(gift.id)"
          >
            <text>🔄</text>
          </view>
        </view>
        
        <view class="empty-state" v-if="receivedGifts.length === 0">
          <text class="empty-icon">🎁</text>
          <text class="empty-text">暂无收到的礼物</text>
        </view>
      </view>
    </view>
    
    <view class="content" v-if="activeTab === 'sent'">
      <view class="gifts-list">
        <view 
          class="gift-item" 
          v-for="gift in sentGifts" 
          :key="gift.id"
        >
          <image 
            class="receiver-avatar" 
            :src="gift.receiver.avatar || 'https://neeko-copilot.bytedance.net/api/text_to_image?prompt=avatar%20person&image_size=square'" 
            mode="aspectFill" 
          />
          <view class="gift-info">
            <view class="gift-header">
              <text class="receiver-name">{{ gift.receiver.nickname }}</text>
              <text class="gift-time">{{ formatTime(gift.created_at) }}</text>
            </view>
            <view class="gift-content">
              <text class="gift-icon">{{ getGiftIcon(gift.gift_id) }}</text>
              <text class="gift-name">{{ getGiftName(gift.gift_id) }}</text>
              <text class="gift-points">-{{ gift.points }}积分</text>
            </view>
          </view>
        </view>
        
        <view class="empty-state" v-if="sentGifts.length === 0">
          <text class="empty-icon">💝</text>
          <text class="empty-text">暂无送出的礼物</text>
        </view>
      </view>
    </view>
    
    <view class="content" v-if="activeTab === 'redeem'">
      <view class="balance-info">
        <text class="balance-label">当前积分</text>
        <text class="balance-value">{{ currentPoints }}</text>
      </view>
      
      <view class="redeem-options">
        <view 
          class="redeem-item" 
          v-for="option in redeemOptions" 
          :key="option.id"
          @click="redeemPoints(option)"
        >
          <view class="option-icon">{{ option.icon }}</view>
          <view class="option-info">
            <text class="option-name">{{ option.name }}</text>
            <text class="option-desc">{{ option.desc }}</text>
          </view>
          <view class="option-cost">
            <text class="cost-value">{{ option.cost }}</text>
            <text class="cost-unit">积分</text>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const activeTab = ref('received')
const currentPoints = ref(2580)

const receivedGifts = ref([
  { id: 1, sender: { nickname: '阳光男孩', avatar: '' }, gift_id: 1, points: 10, created_at: '2025-01-15 14:30', converted: false },
  { id: 2, sender: { nickname: '小美', avatar: '' }, gift_id: 4, points: 100, created_at: '2025-01-14 20:15', converted: false },
  { id: 3, sender: { nickname: '小仙女', avatar: '' }, gift_id: 2, points: 5, created_at: '2025-01-13 10:00', converted: true }
])

const sentGifts = ref([
  { id: 1, receiver: { nickname: '新朋友', avatar: '' }, gift_id: 3, points: 8, created_at: '2025-01-15 16:00' },
  { id: 2, receiver: { nickname: '小美', avatar: '' }, gift_id: 1, points: 10, created_at: '2025-01-14 18:30' }
])

const redeemOptions = ref([
  { id: 1, name: '兑换会员', icon: '👑', desc: '兑换7天VIP体验', cost: 500 },
  { id: 2, name: '兑换喜欢次数', icon: '❤️', desc: '兑换10次喜欢机会', cost: 50 },
  { id: 3, name: '兑换超级喜欢', icon: '💎', desc: '兑换3次超级喜欢', cost: 100 },
  { id: 4, name: '兑换礼物', icon: '🎁', desc: '兑换随机礼物一个', cost: 200 }
])

const giftList = {
  1: { name: '玫瑰', icon: '🌹' },
  2: { name: '爱心', icon: '❤️' },
  3: { name: '星星', icon: '⭐' },
  4: { name: '钻石', icon: '💎' },
  5: { name: '皇冠', icon: '👑' }
}

const receivedCount = computed(() => receivedGifts.value.filter(g => !g.converted).length)

const totalPointsReceived = computed(() => {
  return receivedGifts.value.reduce((sum, gift) => sum + gift.points, 0)
})

function getGiftIcon(giftId: number) {
  return giftList[giftId]?.icon || '🎁'
}

function getGiftName(giftId: number) {
  return giftList[giftId]?.name || '礼物'
}

function formatTime(timeStr: string) {
  return timeStr.split(' ')[0]
}

function convertGift(giftId: number) {
  const gift = receivedGifts.value.find(g => g.id === giftId)
  if (gift) {
    gift.converted = true
    currentPoints.value += gift.points
    uni.showToast({ title: `兑换成功 +${gift.points}积分`, icon: 'success' })
  }
}

function convertAllPoints() {
  const unconverted = receivedGifts.value.filter(g => !g.converted)
  if (unconverted.length === 0) {
    uni.showToast({ title: '没有可兑换的礼物', icon: 'none' })
    return
  }
  
  const total = unconverted.reduce((sum, g) => sum + g.points, 0)
  receivedGifts.value.forEach(g => g.converted = true)
  currentPoints.value += total
  
  uni.showToast({ title: `兑换成功 +${total}积分`, icon: 'success' })
}

function redeemPoints(option: any) {
  if (currentPoints.value < option.cost) {
    uni.showToast({ title: '积分不足', icon: 'none' })
    return
  }
  
  uni.showModal({
    title: '兑换确认',
    content: `确认兑换 ${option.name} (消耗${option.cost}积分)?`,
    success: (res) => {
      if (res.confirm) {
        currentPoints.value -= option.cost
        uni.showToast({ title: '兑换成功', icon: 'success' })
      }
    }
  })
}

function goBack() {
  uni.navigateBack()
}
</script>

<style lang="scss" scoped>
.gifts-page {
  min-height: 100vh;
  background: #f5f5f5;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 60rpx 32rpx 24rpx;
  background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
}

.back-btn {
  font-size: 44rpx;
  color: #fff;
}

.page-title {
  font-size: 36rpx;
  font-weight: bold;
  color: #fff;
}

.placeholder {
  width: 80rpx;
}

.tabs {
  display: flex;
  background: #fff;
  padding: 0 24rpx;
  border-bottom: 1rpx solid #f0f0f0;
}

.tab-item {
  flex: 1;
  padding: 28rpx 0;
  text-align: center;
  position: relative;
  
  text {
    font-size: 28rpx;
    color: #666;
  }
  
  &.active {
    text {
      color: #f87c7c;
      font-weight: bold;
    }
    
    &::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 60rpx;
      height: 6rpx;
      background: #f87c7c;
      border-radius: 3rpx;
    }
  }
}

.tab-badge {
  position: absolute;
  top: 16rpx;
  right: 50%;
  transform: translateX(40rpx);
  background: #ff4d4f;
  border-radius: 20rpx;
  min-width: 32rpx;
  height: 32rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 8rpx;
  
  text {
    font-size: 20rpx;
    color: #fff;
  }
}

.content {
  padding: 24rpx;
}

.total-points {
  background: linear-gradient(135deg, #f87c7c 0%, #e56b6b 100%);
  border-radius: 16rpx;
  padding: 32rpx;
  margin-bottom: 24rpx;
  color: #fff;
}

.points-label {
  display: block;
  font-size: 26rpx;
  opacity: 0.9;
  margin-bottom: 8rpx;
}

.points-value {
  display: block;
  font-size: 56rpx;
  font-weight: bold;
  margin-bottom: 20rpx;
}

.convert-btn {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 30rpx;
  padding: 16rpx;
  text-align: center;
  
  text {
    font-size: 26rpx;
    font-weight: bold;
  }
}

.gifts-list {
  background: #fff;
  border-radius: 16rpx;
  overflow: hidden;
}

.gift-item {
  display: flex;
  align-items: center;
  padding: 24rpx;
  border-bottom: 1rpx solid #f8f8f8;
  
  &:last-child {
    border-bottom: none;
  }
}

.sender-avatar, .receiver-avatar {
  width: 80rpx;
  height: 80rpx;
  border-radius: 50%;
  margin-right: 20rpx;
}

.gift-info {
  flex: 1;
}

.gift-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12rpx;
}

.sender-name, .receiver-name {
  font-size: 28rpx;
  font-weight: bold;
  color: #333;
}

.gift-time {
  font-size: 22rpx;
  color: #999;
}

.gift-content {
  display: flex;
  align-items: center;
  gap: 12rpx;
}

.gift-icon {
  font-size: 36rpx;
}

.gift-name {
  font-size: 26rpx;
  color: #666;
}

.gift-points {
  font-size: 24rpx;
  color: #f87c7c;
  font-weight: bold;
}

.convert-icon {
  width: 64rpx;
  height: 64rpx;
  background: #f5f5f5;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-state {
  padding: 60rpx;
  text-align: center;
}

.empty-icon {
  font-size: 80rpx;
  margin-bottom: 20rpx;
}

.empty-text {
  font-size: 28rpx;
  color: #999;
}

.balance-info {
  background: #fff;
  border-radius: 16rpx;
  padding: 32rpx;
  margin-bottom: 24rpx;
  text-align: center;
}

.balance-label {
  display: block;
  font-size: 26rpx;
  color: #999;
  margin-bottom: 8rpx;
}

.balance-value {
  font-size: 56rpx;
  font-weight: bold;
  color: #f87c7c;
}

.redeem-options {
  background: #fff;
  border-radius: 16rpx;
  overflow: hidden;
}

.redeem-item {
  display: flex;
  align-items: center;
  padding: 24rpx;
  border-bottom: 1rpx solid #f8f8f8;
  
  &:last-child {
    border-bottom: none;
  }
  
  &:active {
    background: #f9f9f9;
  }
}

.option-icon {
  width: 80rpx;
  height: 80rpx;
  background: #f5f5f5;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40rpx;
  margin-right: 20rpx;
}

.option-info {
  flex: 1;
}

.option-name {
  display: block;
  font-size: 30rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 8rpx;
}

.option-desc {
  font-size: 24rpx;
  color: #999;
}

.option-cost {
  text-align: right;
}

.cost-value {
  display: block;
  font-size: 32rpx;
  font-weight: bold;
  color: #f87c7c;
}

.cost-unit {
  font-size: 22rpx;
  color: #999;
}
</style>
