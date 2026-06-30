<template>
  <view class="custom-tab-bar">
    <view 
      class="tab-item" 
      v-for="(item, index) in tabs" 
      :key="index"
      :class="{ active: currentIndex === index }"
      @click="switchTab(index)"
    >
      <FontAwesome :name="item.icon" size="28px" :color="currentIndex === index ? '#ff6b9d' : '#999'" />
      <text class="tab-text" :class="{ active: currentIndex === index }">{{ item.text }}</text>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import FontAwesome from './FontAwesome.vue'

interface TabItem {
  text: string
  icon: string
  path: string
}

const tabs: TabItem[] = [
  { text: '发现', icon: 'compass', path: '/pages/discover/cards' },
  { text: '好友', icon: 'users', path: '/pages/friends/index' },
  { text: '聊天', icon: 'comment', path: '/pages/messages/index' },
  { text: '说说', icon: 'pencil', path: '/pages/statuses/index' },
  { text: '我的', icon: 'user', path: '/pages/profile/index' }
]

const currentIndex = ref(0)

onMounted(() => {
  const pages = getCurrentPages()
  if (pages.length > 0) {
    const currentPage = pages[pages.length - 1]
    const route = '/' + currentPage.route
    const index = tabs.findIndex(t => t.path === route)
    if (index !== -1) {
      currentIndex.value = index
    }
  }
})

function switchTab(index: number) {
  if (currentIndex.value === index) return
  
  currentIndex.value = index
  uni.switchTab({
    url: tabs[index].path
  })
}
</script>

<style lang="scss">
.custom-tab-bar {
  display: flex;
  background: #fff;
  border-top: 1rpx solid #eee;
  padding: 12rpx 0;
  padding-bottom: calc(12rpx + env(safe-area-inset-bottom));
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 999;
}

.tab-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4rpx;
  
  &:active {
    opacity: 0.7;
  }
  
  &.active {
    .tab-text {
      color: #ff6b9d;
    }
  }
}

.tab-text {
  font-size: 22rpx;
  color: #999;
}
</style>