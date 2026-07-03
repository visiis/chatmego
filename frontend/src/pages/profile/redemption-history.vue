<template>
  <view class="history-container">
    <view class="status-bar"></view>
    
    <view class="header">
      <view class="header-content">
        <view class="back-btn" @click="goBack">
          <FontAwesome name="chevron-left" size="36px" color="#fff" />
        </view>
        <text class="header-title">兌換記錄</text>
        <view class="header-right"></view>
      </view>
    </view>
    
    <scroll-view class="content" scroll-y>
      <view class="redemption-list" v-if="redemptions.length > 0">
        <view class="redemption-item" v-for="item in redemptions" :key="item.id">
          <view class="item-header">
            <text class="item-date">{{ formatDate(item.created_at) }}</text>
            <view class="status-badge" :class="getStatusClass(item.status)">
              <text>{{ item.status_label }}</text>
            </view>
          </view>
          
          <view class="item-body">
            <image v-if="item.gift_image" class="gift-image" :src="item.gift_image" mode="aspectFill" />
            <view class="gift-image-placeholder" v-else>
              <FontAwesome name="gift" size="48px" color="#ccc" />
            </view>
            
            <view class="gift-info">
              <text class="gift-name">{{ item.gift_name }}</text>
              <text class="gift-quantity">數量：{{ item.quantity }}</text>
              <text class="recipient">收件人：{{ item.recipient_name }}</text>
              <text class="phone">{{ item.phone }}</text>
              <text class="address">{{ item.address }}</text>
            </view>
          </view>
        </view>
      </view>
      
      <view class="empty-section" v-else>
        <view class="empty-icon">
          <FontAwesome name="box-open" size="64px" color="#ccc" />
        </view>
        <text class="empty-text">暫無兌換記錄</text>
      </view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { getRedemptionHistory, type Redemption } from '../../api/gift'

const redemptions = ref<Redemption[]>([])

onMounted(() => {
  loadHistory()
})

async function loadHistory() {
  try {
    redemptions.value = await getRedemptionHistory()
  } catch (error) {
    console.error('加載兌換記錄失敗:', error)
    uni.showToast({ title: '加載失敗', icon: 'none' })
  }
}

function getStatusClass(status: string): string {
  const classes: Record<string, string> = {
    pending: 'status-pending',
    processing: 'status-processing',
    shipped: 'status-shipped',
    completed: 'status-completed',
    cancelled: 'status-cancelled'
  }
  return classes[status] || 'status-pending'
}

function formatDate(dateStr: string): string {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const year = date.getFullYear()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const hour = date.getHours().toString().padStart(2, '0')
  const minute = date.getMinutes().toString().padStart(2, '0')
  return `${year}-${month}-${day} ${hour}:${minute}`
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
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
}

.history-container {
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
}

.content {
  flex: 1;
  padding: 24rpx;
}

.redemption-list {
  display: flex;
  flex-direction: column;
  gap: 16rpx;
}

.redemption-item {
  background: #fff;
  border-radius: 12rpx;
  overflow: hidden;
}

.item-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16rpx 24rpx;
  background: #f8f9fa;
}

.item-date {
  font-size: 24rpx;
  color: #999;
}

.status-badge {
  padding: 8rpx 20rpx;
  border-radius: 12rpx;
  
  text {
    font-size: 24rpx;
    color: #fff;
  }
  
  &.status-pending {
    background: #f39c12;
  }
  
  &.status-processing {
    background: #3498db;
  }
  
  &.status-shipped {
    background: #9b59b6;
  }
  
  &.status-completed {
    background: #27ae60;
  }
  
  &.status-cancelled {
    background: #95a5a6;
  }
}

.item-body {
  display: flex;
  padding: 20rpx 24rpx;
}

.gift-image {
  width: 120rpx;
  height: 120rpx;
  border-radius: 12rpx;
  background: #f8f9fa;
  flex-shrink: 0;
}

.gift-image-placeholder {
  width: 120rpx;
  height: 120rpx;
  background: #f8f9fa;
  border-radius: 12rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
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

.gift-quantity {
  display: block;
  font-size: 24rpx;
  color: #3498db;
  margin-bottom: 8rpx;
}

.recipient {
  display: block;
  font-size: 24rpx;
  color: #666;
  margin-bottom: 4rpx;
}

.phone {
  display: block;
  font-size: 24rpx;
  color: #666;
  margin-bottom: 4rpx;
}

.address {
  display: block;
  font-size: 24rpx;
  color: #999;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.empty-section {
  background: #fff;
  border-radius: 12rpx;
  padding: 100rpx 0;
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
