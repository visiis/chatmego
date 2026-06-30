<template>
  <view class="page-header">
    <view class="status-bar"></view>
    <view class="nav-bar">
      <view class="nav-left" v-if="showBack" @click="goBack">
        <FontAwesome name="chevron-left" size="32px" color="#fff" />
      </view>
      <view class="nav-left" v-else></view>
      <view class="nav-center">
        <text class="nav-title">{{ title }}</text>
      </view>
      <view class="nav-right">
        <slot name="right"></slot>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import FontAwesome from './FontAwesome.vue'

defineProps<{
  title: string
  showBack?: boolean
}>()

function goBack() {
  uni.navigateBack()
}
</script>

<style lang="scss" scoped>
.page-header {
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
}

.status-bar {
  height: var(--status-bar-height, 44px);
}

.nav-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20rpx 24rpx;
  height: 88rpx;
}

.nav-left {
  width: 80rpx;
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.nav-center {
  flex: 1;
  text-align: center;
}

.nav-title {
  font-size: 34rpx;
  font-weight: 600;
  color: #fff;
}

.nav-right {
  width: 80rpx;
  display: flex;
  align-items: center;
  justify-content: flex-end;
}
</style>
