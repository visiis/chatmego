<template>
  <view class="loading-progress" v-if="visible">
    <view class="progress-bar">
      <view class="progress-fill" :style="{ width: progress + '%' }"></view>
      <view class="progress-glow"></view>
    </view>
    <view class="progress-text" v-if="showText">{{ progress }}%</view>
    <view class="progress-dots">
      <view class="dot" v-for="i in 3" :key="i" :style="{ animationDelay: i * 0.2 + 's' }"></view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
  visible: boolean
  progress?: number
  showText?: boolean
}>()

const progress = ref(0)

watch(() => props.progress, (newVal) => {
  progress.value = newVal || 0
}, { immediate: true })
</script>

<style lang="scss" scoped>
.loading-progress {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 9999;
  background: rgba(255, 255, 255, 0.95);
  padding: 20rpx;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16rpx;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    transform: translateY(-100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.progress-bar {
  width: 100%;
  max-width: 600rpx;
  height: 12rpx;
  background: rgba(255, 107, 157, 0.1);
  border-radius: 6rpx;
  overflow: hidden;
  position: relative;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #ff6b9d 0%, #ff9fbd 100%);
  border-radius: 6rpx;
  transition: width 0.3s ease-out;
  position: relative;
}

.progress-fill::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 20rpx;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5));
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% {
    transform: translateX(-20rpx);
  }
  100% {
    transform: translateX(100rpx);
  }
}

.progress-glow {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 4rpx;
  background: linear-gradient(90deg, transparent, rgba(255, 107, 157, 0.5), transparent);
  transform: translateY(-50%);
  animation: glow 2s infinite;
}

@keyframes glow {
  0%, 100% {
    opacity: 0;
    transform: translateY(-50%) translateX(-100%);
  }
  50% {
    opacity: 1;
    transform: translateY(-50%) translateX(100%);
  }
}

.progress-text {
  font-size: 24rpx;
  color: #ff6b9d;
  font-weight: 500;
}

.progress-dots {
  display: flex;
  gap: 12rpx;
}

.dot {
  width: 12rpx;
  height: 12rpx;
  background: #ff6b9d;
  border-radius: 50%;
  animation: bounce 1s infinite ease-in-out;
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
    opacity: 0.5;
  }
  50% {
    transform: translateY(-16rpx);
    opacity: 1;
  }
}
</style>
