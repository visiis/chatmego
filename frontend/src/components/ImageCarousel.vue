<template>
  <view class="image-carousel" @touchstart="onTouchStart" @touchmove="onTouchMove" @touchend="onTouchEnd">
    <scroll-view 
      class="carousel-scroll"
      scroll-x
      :scroll-left="scrollLeft"
      scroll-with-animation
      :show-scrollbar="false"
    >
      <view class="carousel-content">
        <image 
          v-for="(photo, index) in photos" 
          :key="photo.id || index"
          class="carousel-image"
          :src="formatUrl(photo.url)"
          mode="aspectFill"
          @load="onImageLoad"
        />
      </view>
    </scroll-view>
    
    <view class="photo-indicators" v-if="photos.length > 1">
      <view 
        v-for="(_, index) in photos" 
        :key="index"
        class="indicator-dot"
        :class="{ active: currentIndex === index }"
      ></view>
    </view>
    
    <view class="photo-count" v-if="photos.length > 1">
      <text>{{ currentIndex + 1 }}/{{ photos.length }}</text>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'

interface Photo {
  id?: number
  url: string
  blur_url?: string
  is_main?: number
  is_premium?: boolean
  points_price?: number
}

const props = defineProps<{
  photos: Photo[]
}>()

const currentIndex = ref(0)
const scrollLeft = ref(0)
const startX = ref(0)
const containerWidth = ref(0)

const photoCount = computed(() => props.photos.length)

watch(() => props.photos, () => {
  currentIndex.value = 0
  scrollLeft.value = 0
})

function formatUrl(url: string): string {
  if (!url) return ''
  
  if (url.startsWith('http://') || url.startsWith('https://')) {
    return url
  }
  
  if (url.startsWith('/storage/')) {
    return 'https://chatmego.com' + url
  } else if (url.startsWith('storage/')) {
    return 'https://chatmego.com/' + url
  } else if (!url.startsWith('/')) {
    return 'https://chatmego.com/storage/' + url
  } else {
    return 'https://chatmego.com' + url
  }
}

function onTouchStart(e: TouchEvent) {
  startX.value = e.touches[0].clientX
}

function onTouchMove() {
}

function onTouchEnd(e: TouchEvent) {
  const deltaX = e.changedTouches[0].clientX - startX.value
  const threshold = 50
  
  if (deltaX > threshold && currentIndex.value > 0) {
    currentIndex.value--
  } else if (deltaX < -threshold && currentIndex.value < photoCount.value - 1) {
    currentIndex.value++
  }
  
  scrollLeft.value = currentIndex.value * (containerWidth.value || 375)
}

function onImageLoad() {
}

defineExpose({
  currentIndex
})
</script>

<style lang="scss" scoped>
.image-carousel {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.carousel-scroll {
  width: 100%;
  height: 100%;
  white-space: nowrap;
}

.carousel-content {
  display: inline-flex;
  height: 100%;
}

.carousel-image {
  width: 100vw;
  height: 100%;
  flex-shrink: 0;
}

.photo-indicators {
  position: absolute;
  bottom: 100rpx;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 12rpx;
  padding: 12rpx 20rpx;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 24rpx;
}

.indicator-dot {
  width: 12rpx;
  height: 12rpx;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.4);
  transition: all 0.3s ease;
  
  &.active {
    width: 24rpx;
    border-radius: 6rpx;
    background: #fff;
  }
}

.photo-count {
  position: absolute;
  top: 24rpx;
  right: 24rpx;
  padding: 8rpx 16rpx;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 16rpx;
  font-size: 24rpx;
  color: #fff;
}
</style>
