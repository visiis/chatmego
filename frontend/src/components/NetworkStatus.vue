<template>
  <view class="network-status" :class="statusClass" v-if="show">
    <uni-icons :type="iconType" :size="20" :color="iconColor"></uni-icons>
    <text class="status-text">{{ statusText }}</text>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

type NetworkStatus = 'online' | 'offline' | 'slow' | 'ws-connecting' | 'ws-connected' | 'ws-disconnected'

const status = ref<NetworkStatus>('online')
const show = ref(false)

const statusClass = computed(() => {
  return `status-${status.value}`
})

const iconType = computed(() => {
  switch (status.value) {
    case 'online':
    case 'ws-connected':
      return 'wifi'
    case 'offline':
    case 'ws-disconnected':
      return 'closeempty'
    case 'slow':
      return 'alert'
    case 'ws-connecting':
      return 'refresh'
    default:
      return 'wifi'
  }
})

const iconColor = computed(() => {
  switch (status.value) {
    case 'online':
    case 'ws-connected':
      return '#52c41a'
    case 'offline':
    case 'ws-disconnected':
      return '#ff4d4f'
    case 'slow':
      return '#faad14'
    case 'ws-connecting':
      return '#1890ff'
    default:
      return '#52c41a'
  }
})

const statusText = computed(() => {
  switch (status.value) {
    case 'online':
      return '网络正常'
    case 'offline':
      return '网络已断开'
    case 'slow':
      return '网络较慢'
    case 'ws-connecting':
      return '正在连接...'
    case 'ws-connected':
      return '实时连接已建立'
    case 'ws-disconnected':
      return '实时连接已断开'
    default:
      return '网络正常'
  }
})

function updateNetworkStatus(res: UniApp.OnNetworkStatusChangeResult) {
  if (!res.isConnected) {
    status.value = 'offline'
    show.value = true
  } else if (res.networkType === '2g') {
    status.value = 'slow'
    show.value = true
  } else {
    status.value = 'online'
    show.value = false
  }
}

function updateWebSocketStatus(connected: boolean) {
  if (connected) {
    status.value = 'ws-connected'
    show.value = true
    setTimeout(() => {
      if (status.value === 'ws-connected') {
        show.value = false
      }
    }, 3000)
  } else {
    status.value = 'ws-disconnected'
    show.value = true
  }
}

onMounted(() => {
  uni.onNetworkStatusChange(updateNetworkStatus)
  
  const wsManager = require('../utils/websocket').wsManager
  wsManager.onConnection(updateWebSocketStatus)
})

onUnmounted(() => {
  uni.offNetworkStatusChange(updateNetworkStatus)
  
  const wsManager = require('../utils/websocket').wsManager
  wsManager.offConnection(() => {})
})
</script>

<style lang="scss" scoped>
.network-status {
  position: fixed;
  top: 100rpx;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  align-items: center;
  padding: 12rpx 24rpx;
  border-radius: 32rpx;
  background: rgba(0, 0, 0, 0.8);
  z-index: 9999;
  animation: slideDown 0.3s ease;

  .status-text {
    font-size: 24rpx;
    color: #ffffff;
    margin-left: 12rpx;
  }
}

.status-online,
.status-ws-connected {
  background: rgba(82, 196, 26, 0.9);
}

.status-offline,
.status-ws-disconnected {
  background: rgba(255, 77, 79, 0.9);
}

.status-slow {
  background: rgba(250, 173, 20, 0.9);
}

.status-ws-connecting {
  background: rgba(24, 144, 255, 0.9);
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateX(-50%) translateY(-20rpx);
  }
  to {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
  }
}
</style>