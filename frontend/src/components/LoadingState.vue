<template>
  <view class="loading-container" v-if="show">
    <template v-if="type === 'spin'">
      <view class="spinner">
        <view class="spinner-ring"></view>
        <view class="spinner-ring spinner-ring-2"></view>
        <view class="spinner-ring spinner-ring-3"></view>
      </view>
      <text class="loading-text" v-if="text">{{ text }}</text>
    </template>
    <template v-else-if="type === 'dots'">
      <view class="dots-loader">
        <view class="dot"></view>
        <view class="dot"></view>
        <view class="dot"></view>
      </view>
      <text class="loading-text" v-if="text">{{ text }}</text>
    </template>
    <template v-else-if="type === 'pull'">
      <view class="pull-loader">
        <uni-icons :type="pullIcon" :size="32" :color="pullColor" :class="{ 'rotate': isRefreshing }"></uni-icons>
        <text class="pull-text">{{ pullText }}</text>
      </view>
    </template>
    <template v-else-if="type === 'skeleton'">
      <view class="skeleton-loader">
        <template v-for="(item, index) in skeletonItems" :key="index">
          <view class="skeleton-item" :style="item.style">
            <view class="skeleton-line" v-for="i in (item.lines || 3)" :key="i" :style="{ width: `${60 + Math.random() * 40}%` }"></view>
          </view>
        </template>
      </view>
    </template>
  </view>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  show: boolean
  type?: 'spin' | 'dots' | 'pull' | 'skeleton'
  text?: string
  isRefreshing?: boolean
  pullStatus?: 'pulling' | 'refreshing' | 'complete'
  skeletonItems?: Array<{ lines?: number; style?: Record<string, string> }>
}>()

const pullIcon = computed(() => {
  switch (props.pullStatus) {
    case 'refreshing':
      return 'refresh'
    case 'complete':
      return 'checkmarkempty'
    default:
      return 'arrowdown'
  }
})

const pullColor = computed(() => {
  switch (props.pullStatus) {
    case 'refreshing':
      return '#ff6b9d'
    case 'complete':
      return '#52c41a'
    default:
      return '#999999'
  }
})

const pullText = computed(() => {
  switch (props.pullStatus) {
    case 'refreshing':
      return '刷新中...'
    case 'complete':
      return '刷新完成'
    default:
      return '下拉刷新'
  }
})
</script>

<style lang="scss" scoped>
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40rpx;
}

.spinner {
  position: relative;
  width: 60rpx;
  height: 60rpx;

  .spinner-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border: 4rpx solid transparent;
    border-top-color: #ff6b9d;
    border-radius: 50%;
    animation: spin 1s linear infinite;

    &.spinner-ring-2 {
      border-top-color: #ff9fbd;
      animation-duration: 1.2s;
    }

    &.spinner-ring-3 {
      border-top-color: #ffc7d9;
      animation-duration: 1.4s;
    }
  }
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.dots-loader {
  display: flex;
  align-items: center;
  justify-content: center;

  .dot {
    width: 12rpx;
    height: 12rpx;
    border-radius: 50%;
    background: #ff6b9d;
    margin: 0 8rpx;
    animation: dotBounce 1.4s infinite ease-in-out both;

    &:nth-child(1) {
      animation-delay: -0.32s;
    }

    &:nth-child(2) {
      animation-delay: -0.16s;
    }
  }
}

@keyframes dotBounce {
  0%, 80%, 100% {
    transform: scale(0);
    opacity: 0.5;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

.loading-text {
  font-size: 24rpx;
  color: #999999;
  margin-top: 20rpx;
}

.pull-loader {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20rpx;

  .rotate {
    animation: rotate 0.5s linear infinite;
  }

  .pull-text {
    font-size: 24rpx;
    color: #999999;
    margin-top: 10rpx;
  }
}

@keyframes rotate {
  to {
    transform: rotate(360deg);
  }
}

.skeleton-loader {
  width: 100%;

  .skeleton-item {
    display: flex;
    flex-direction: column;
    padding: 20rpx;
    margin-bottom: 20rpx;
    background: #ffffff;
    border-radius: 16rpx;

    .skeleton-line {
      height: 32rpx;
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      border-radius: 8rpx;
      margin-bottom: 12rpx;
      animation: shimmer 1.5s infinite;

      &:last-child {
        margin-bottom: 0;
      }
    }
  }
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}
</style>