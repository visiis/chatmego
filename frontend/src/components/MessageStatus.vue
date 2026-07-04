<template>
  <view class="message-status" :class="status">
    <template v-if="status === 'sending'">
      <view class="loading-dots">
        <view class="dot"></view>
        <view class="dot"></view>
        <view class="dot"></view>
      </view>
    </template>
    <template v-else-if="status === 'sent'">
      <uni-icons type="checkmarkempty" :size="16" color="#999999"></uni-icons>
    </template>
    <template v-else-if="status === 'delivered'">
      <uni-icons type="checkmarkempty" :size="16" color="#999999"></uni-icons>
      <uni-icons type="checkmarkempty" :size="16" color="#999999"></uni-icons>
    </template>
    <template v-else-if="status === 'read'">
      <uni-icons type="checkmarkempty" :size="16" color="#ff6b9d"></uni-icons>
      <uni-icons type="checkmarkempty" :size="16" color="#ff6b9d"></uni-icons>
    </template>
    <template v-else-if="status === 'error'">
      <uni-icons type="closeempty" :size="16" color="#ff4d4f"></uni-icons>
    </template>
  </view>
</template>

<script setup lang="ts">
import type { MessageStatus } from '../utils/messageSync'

defineProps<{
  status: MessageStatus
}>()
</script>

<style lang="scss" scoped>
.message-status {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40rpx;
  height: 40rpx;
}

.loading-dots {
  display: flex;
  align-items: center;
  justify-content: center;

  .dot {
    width: 8rpx;
    height: 8rpx;
    border-radius: 50%;
    background: #999999;
    margin: 0 4rpx;
    animation: bounce 1.4s infinite ease-in-out both;

    &:nth-child(1) {
      animation-delay: -0.32s;
    }

    &:nth-child(2) {
      animation-delay: -0.16s;
    }
  }
}

@keyframes bounce {
  0%, 80%, 100% {
    transform: scale(0);
  }
  40% {
    transform: scale(1);
  }
}

.status-error {
  cursor: pointer;
}
</style>