<template>
  <view class="test-container">
    <text class="test-title">测试页面 - 版本 2026-07-04</text>
    <text class="test-time">{{ currentTime }}</text>
    <view class="test-box">
      <text class="test-text">上传测试成功！</text>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const currentTime = ref('')

onMounted(() => {
  const now = new Date()
  currentTime.value = now.toLocaleString('zh-CN')
})
</script>

<style lang="scss">
page {
  height: 100%;
  background: #f0f0f0;
}

.test-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
  padding: 40rpx;
}

.test-title {
  font-size: 48rpx;
  font-weight: bold;
  color: #ff6b9d;
  margin-bottom: 24rpx;
}

.test-time {
  font-size: 28rpx;
  color: #999;
  margin-bottom: 48rpx;
}

.test-box {
  background: linear-gradient(135deg, #ff6b9d, #c44569);
  padding: 60rpx 80rpx;
  border-radius: 24rpx;
  box-shadow: 0 12rpx 40rpx rgba(255, 107, 157, 0.4);
}

.test-text {
  font-size: 40rpx;
  font-weight: bold;
  color: #fff;
}
</style>