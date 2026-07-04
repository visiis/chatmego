<template>
  <view class="image-carousel" @touchstart="onTouchStart" @touchmove="onTouchMove" @touchend="onTouchEnd">
    <scroll-view 
      class="carousel-scroll"
      scroll-x
      :scroll-left="scrollLeft"
      scroll-with-animation
      :show-scrollbar="false"
      @scroll="onScroll"
    >
      <view class="carousel-content">
        <image 
          v-for="(photo, index) in displayPhotos" 
          :key="photo.id || index"
          class="carousel-image"
          :src="formatUrl(photo.url)"
          mode="aspectFill"
          @load="onImageLoad(index)"
          @error="onImageError(index)"
        />
      </view>
    </scroll-view>
    
    <view class="photo-indicators" v-if="displayPhotos.length > 1">
      <view 
        v-for="(_, index) in displayPhotos" 
        :key="index"
        class="indicator-dot"
        :class="{ active: currentIndex === index }"
        @click="goToIndex(index)"
      ></view>
    </view>
    
    <view class="photo-count" v-if="displayPhotos.length > 1">
      <text>{{ currentIndex + 1 }}/{{ displayPhotos.length }}</text>
    </view>
    
    <view class="default-placeholder" v-if="displayPhotos.length === 0">
      <FontAwesome name="image" size="80px" color="#ccc" />
      <text>暂无照片</text>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import FontAwesome from './FontAwesome.vue'

interface Photo {
  id?: number
  url: string
  thumbnail_url?: string
  is_main?: number
  is_premium?: boolean
  points_price?: number
}

const props = defineProps<{
  photos: Photo[]
  autoPlay?: boolean
  interval?: number
}>()

const emit = defineEmits<{
  (e: 'change', index: number): void
}>()

const currentIndex = ref(0)
const scrollLeft = ref(0)
const startX = ref(0)
const containerWidth = ref(0)
const isAutoPlaying = ref(false)
const timer = ref<number | null>(null)
const loadedPhotos = ref<Set<number>>(new Set())

const defaultPhoto = {
  id: -1,
  url: 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"%3E%3Crect fill="%23f5f5f5" width="200" height="200"/%3E%3Ctext fill="%23ccc" font-family="sans-serif" font-size="14" x="50%25" y="50%25" text-anchor="middle" dominant-baseline="middle"%3E暂无照片%3C/text%3E%3C/svg%3E'
}

const displayPhotos = computed(() => {
  if (props.photos && props.photos.length > 0) {
    return props.photos.slice(0, 4)
  }
  return [defaultPhoto]
})

const photoCount = computed(() => displayPhotos.value.length)

watch(() => props.photos, () => {
  currentIndex.value = 0
  scrollLeft.value = 0
  loadedPhotos.value.clear()
}, { deep: true })

watch(currentIndex, (newIndex) => {
  emit('change', newIndex)
})

function formatUrl(url: string): string {
  if (!url) return defaultPhoto.url
  
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
  stopAutoPlay()
  startX.value = e.touches[0].clientX
}

function onTouchMove() {}

function onTouchEnd(e: TouchEvent) {
  const deltaX = e.changedTouches[0].clientX - startX.value
  const threshold = 50
  
  if (deltaX > threshold && currentIndex.value > 0) {
    currentIndex.value--
  } else if (deltaX < -threshold && currentIndex.value < photoCount.value - 1) {
    currentIndex.value++
  }
  
  scrollLeft.value = currentIndex.value * (containerWidth.value || 375)
  
  if (props.autoPlay !== false) {
    startAutoPlay()
  }
}

function onScroll(e: any) {
  const scrollLeft = e.detail.scrollLeft
  const width = containerWidth.value || 375
  currentIndex.value = Math.round(scrollLeft / width)
}

function onImageLoad(index: number) {
  loadedPhotos.value.add(index)
}

function onImageError(index: number) {
  console.error('Image load failed for index:', index)
}

function goToIndex(index: number) {
  currentIndex.value = index
  scrollLeft.value = index * (containerWidth.value || 375)
}

function startAutoPlay() {
  if (photoCount.value <= 1) return
  
  stopAutoPlay()
  timer.value = setTimeout(function loop() {
    currentIndex.value = (currentIndex.value + 1) % photoCount.value
    scrollLeft.value = currentIndex.value * (containerWidth.value || 375)
    timer.value = setTimeout(loop, props.interval || 4000)
  }, props.interval || 4000) as unknown as number
}

function stopAutoPlay() {
  if (timer.value) {
    clearTimeout(timer.value)
    timer.value = null
  }
}

onMounted(() => {
  const query = uni.createSelectorQuery()
  query.select('.image-carousel').boundingClientRect((rect: any) => {
    if (rect) {
      containerWidth.value = rect.width
    }
  }).exec()
  
  if (props.autoPlay !== false) {
    startAutoPlay()
  }
})

onUnmounted(() => {
  stopAutoPlay()
})

defineExpose({
  currentIndex,
  photoCount,
  goToIndex,
  startAutoPlay,
  stopAutoPlay
})
</script>

<style lang="scss" scoped>
.image-carousel {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
  background: #f5f5f5;
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
  backdrop-filter: blur(10px);
}

.indicator-dot {
  width: 12rpx;
  height: 12rpx;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.4);
  transition: all 0.3s ease;
  cursor: pointer;
  
  &.active {
    width: 32rpx;
    border-radius: 6rpx;
    background: #fff;
    box-shadow: 0 0 10rpx rgba(255, 255, 255, 0.5);
  }
  
  &:active {
    transform: scale(0.8);
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
  backdrop-filter: blur(10px);
}

.default-placeholder {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16rpx;
  
  text {
    font-size: 28rpx;
    color: #999;
  }
}
</style>
