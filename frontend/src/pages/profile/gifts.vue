<template>
  <view class="gifts-container">
    <view class="status-bar"></view>
    
    <view class="header">
      <view class="header-content">
        <view class="back-btn" @click="goBack">
          <FontAwesome name="chevron-left" size="36px" color="#fff" />
        </view>
        <text class="header-title">我的禮物</text>
        <view class="header-right"></view>
      </view>
    </view>
    
    <view class="tab-bar">
      <view 
        class="tab-item" 
        :class="{ active: activeTab === 'received' }" 
        @click="activeTab = 'received'"
      >
        <text>收到的禮物</text>
        <view class="tab-indicator" v-if="activeTab === 'received'"></view>
      </view>
      <view 
        class="tab-item" 
        :class="{ active: activeTab === 'sent' }" 
        @click="activeTab = 'sent'"
      >
        <text>送出的禮物</text>
        <view class="tab-indicator" v-if="activeTab === 'sent'"></view>
      </view>
    </view>
    
    <scroll-view class="content" scroll-y>
      <view v-if="activeTab === 'received'">
        <view class="gift-section" v-if="receivedGifts.length > 0">
          <view class="gift-list">
            <view class="gift-item" v-for="gift in receivedGifts" :key="gift.id">
              <image v-if="gift.image" class="gift-image" :src="gift.image" mode="aspectFill" />
              <view class="gift-image-placeholder" v-else>
                <FontAwesome name="gift" size="48px" color="#ccc" />
              </view>
              <view class="gift-info">
                <text class="gift-name">{{ gift.name }}</text>
                <text class="gift-desc">{{ gift.description || '精美禮物' }}</text>
                <view class="gift-meta">
                  <text class="gift-price">{{ getPriceTypeLabel(gift.price_type) }}: {{ gift.price }}</text>
                  <text class="gift-quantity" v-if="gift.quantity && gift.quantity > 1">×{{ gift.quantity }}</text>
                </view>
                <text class="gift-date">{{ formatDate(gift.created_at) }}</text>
                <text class="gift-from" v-if="gift.from_user_name">來自: {{ gift.from_user_name }}</text>
              </view>
            </view>
          </view>
        </view>
        
        <view class="empty-section" v-else>
          <view class="empty-icon">
            <FontAwesome name="gift" size="64px" color="#ccc" />
          </view>
          <text class="empty-text">暫未收到禮物</text>
        </view>
      </view>
      
      <view v-else>
        <view class="gift-section" v-if="sentGifts.length > 0">
          <view class="gift-list">
            <view class="gift-item" v-for="gift in sentGifts" :key="gift.id">
              <image v-if="gift.image" class="gift-image" :src="gift.image" mode="aspectFill" />
              <view class="gift-image-placeholder" v-else>
                <FontAwesome name="gift" size="48px" color="#ccc" />
              </view>
              <view class="gift-info">
                <text class="gift-name">{{ gift.name }}</text>
                <text class="gift-desc">{{ gift.description || '精美禮物' }}</text>
                <view class="gift-meta">
                  <text class="gift-price">{{ getPriceTypeLabel(gift.price_type) }}: {{ gift.price }}</text>
                  <text class="gift-quantity" v-if="gift.quantity && gift.quantity > 1">×{{ gift.quantity }}</text>
                </view>
                <text class="gift-date">{{ formatDate(gift.created_at) }}</text>
                <text class="gift-to" v-if="gift.to_user_name">送給: {{ gift.to_user_name }}</text>
              </view>
            </view>
          </view>
        </view>
        
        <view class="empty-section" v-else>
          <view class="empty-icon">
            <FontAwesome name="gift" size="64px" color="#ccc" />
          </view>
          <text class="empty-text">暫未送出禮物</text>
        </view>
      </view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { getUserGifts, getGiftHistory, type UserGift, type GiftHistory } from '../../api/gift'

interface ExtendedGift extends UserGift {
  from_user_name?: string
  to_user_name?: string
}

const activeTab = ref<'received' | 'sent'>('received')
const receivedGifts = ref<ExtendedGift[]>([])
const sentGifts = ref<ExtendedGift[]>([])

onMounted(() => {
  loadGifts()
})

watch(activeTab, () => {
  loadGifts()
})

async function loadGifts() {
  try {
    const history = await getGiftHistory()
    
    receivedGifts.value = history.received_gifts || []
    sentGifts.value = history.sent_gifts || []
  } catch (error) {
    console.error('加載禮物失敗:', error)
    uni.showToast({ title: '加載失敗', icon: 'none' })
  }
}

function getPriceTypeLabel(type: string): string {
  const labels: Record<string, string> = {
    points: '積分',
    coins: '金幣',
    money: '元',
    activity_points: '活動積分'
  }
  return labels[type] || type
}

function formatDate(dateStr: string): string {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const month = date.getMonth() + 1
  const day = date.getDate()
  return `${month}月${day}日`
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

.gifts-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.status-bar {
  height: var(--status-bar-height, 44px);
}

.header {
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 16rpx 24rpx;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.back-btn {
  width: 72rpx;
  height: 72rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.header-title {
  font-size: 36rpx;
  color: #fff;
  font-weight: bold;
}

.header-right {
  width: 72rpx;
  height: 72rpx;
}

.tab-bar {
  display: flex;
  background: #fff;
  padding: 0 32rpx;
  border-bottom: 1rpx solid #f0f0f0;
}

.tab-item {
  flex: 1;
  height: 100rpx;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: relative;
  
  text {
    font-size: 28rpx;
    color: #999;
    transition: color 0.3s;
  }
  
  &.active {
    text {
      color: #ff6b9d;
      font-weight: 500;
    }
  }
}

.tab-indicator {
  position: absolute;
  bottom: 0;
  width: 48rpx;
  height: 6rpx;
  background: #ff6b9d;
  border-radius: 3rpx;
}

.content {
  flex: 1;
  padding: 24rpx;
}

.gift-section {
  background: #fff;
  border-radius: 12rpx;
  overflow: hidden;
}

.gift-list {
  display: flex;
  flex-direction: column;
}

.gift-item {
  display: flex;
  padding: 20rpx 24rpx;
  border-bottom: 1rpx solid #f0f0f0;
  
  &:last-child {
    border-bottom: none;
  }
  
  &:active {
    background: #f8f9fa;
  }
}

.gift-image {
  width: 120rpx;
  height: 120rpx;
  border-radius: 12rpx;
  background: #f8f9fa;
}

.gift-image-placeholder {
  width: 120rpx;
  height: 120rpx;
  background: #f8f9fa;
  border-radius: 12rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.gift-info {
  flex: 1;
  margin-left: 20rpx;
}

.gift-name {
  display: block;
  font-size: 30rpx;
  color: #333;
  font-weight: 500;
  margin-bottom: 8rpx;
}

.gift-desc {
  display: block;
  font-size: 24rpx;
  color: #999;
  margin-bottom: 8rpx;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.gift-meta {
  display: flex;
  align-items: center;
  gap: 16rpx;
  margin-bottom: 8rpx;
}

.gift-price {
  font-size: 24rpx;
  color: #27ae60;
}

.gift-quantity {
  font-size: 24rpx;
  color: #3498db;
}

.gift-date {
  font-size: 22rpx;
  color: #ccc;
}

.gift-from, .gift-to {
  font-size: 22rpx;
  color: #666;
}

.empty-section {
  background: #fff;
  border-radius: 12rpx;
  padding: 80rpx 0;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.empty-icon {
  margin-bottom: 16rpx;
}

.empty-text {
  font-size: 28rpx;
  color: #999;
}
</style>