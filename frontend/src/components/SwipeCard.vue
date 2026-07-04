<template>
  <view 
    class="swipe-card"
    :style="cardStyle"
    @touchstart="onTouchStart"
    @touchmove="onTouchMove"
    @touchend="onTouchEnd"
  >
    <slot></slot>
    
    <view 
      class="swipe-indicator like"
      :style="{ opacity: likeOpacity }"
    >
      <text>LIKE</text>
    </view>
    
    <view 
      class="swipe-indicator nope"
      :style="{ opacity: nopeOpacity }"
    >
      <text>NOPE</text>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  disabled?: boolean
}>()

const emit = defineEmits<{
  (e: 'swipeLeft'): void
  (e: 'swipeRight'): void
  (e: 'swipeEnd'): void
}>()

const startX = ref(0)
const startY = ref(0)
const deltaX = ref(0)
const deltaY = ref(0)
const isDragging = ref(false)

const SWIPE_THRESHOLD = 100
const ROTATION_FACTOR = 0.05

const cardStyle = computed(() => {
  if (!isDragging.value) {
    return {
      transform: 'translateX(0) rotate(0deg)',
      transition: 'transform 0.3s ease-out'
    }
  }
  
  const rotate = deltaX.value * ROTATION_FACTOR
  
  return {
    transform: `translateX(${deltaX.value}px) rotate(${rotate}deg)`,
    transition: 'none'
  }
})

const likeOpacity = computed(() => {
  if (deltaX.value > 0) {
    return Math.min(deltaX.value / 150, 1)
  }
  return 0
})

const nopeOpacity = computed(() => {
  if (deltaX.value < 0) {
    return Math.min(Math.abs(deltaX.value) / 150, 1)
  }
  return 0
})

function onTouchStart(e: TouchEvent) {
  if (props.disabled) return
  
  const touch = e.touches[0]
  startX.value = touch.clientX
  startY.value = touch.clientY
  isDragging.value = true
}

function onTouchMove(e: TouchEvent) {
  if (!isDragging.value || props.disabled) return
  
  const touch = e.touches[0]
  deltaX.value = touch.clientX - startX.value
  deltaY.value = touch.clientY - startY.value
}

function onTouchEnd() {
  if (!isDragging.value) return
  isDragging.value = false
  
  if (deltaX.value > SWIPE_THRESHOLD) {
    deltaX.value = 500
    setTimeout(() => {
      emit('swipeRight')
      resetPosition()
    }, 200)
  } else if (deltaX.value < -SWIPE_THRESHOLD) {
    deltaX.value = -500
    setTimeout(() => {
      emit('swipeLeft')
      resetPosition()
    }, 200)
  } else {
    resetPosition()
  }
  
  emit('swipeEnd')
}

function resetPosition() {
  deltaX.value = 0
  deltaY.value = 0
}

function swipeLeft() {
  deltaX.value = -500
  setTimeout(() => {
    emit('swipeLeft')
    resetPosition()
  }, 200)
}

function swipeRight() {
  deltaX.value = 500
  setTimeout(() => {
    emit('swipeRight')
    resetPosition()
  }, 200)
}

defineExpose({
  swipeLeft,
  swipeRight
})
</script>

<style lang="scss" scoped>
.swipe-card {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  touch-action: none;
  will-change: transform;
}

.swipe-indicator {
  position: absolute;
  top: 60rpx;
  padding: 16rpx 32rpx;
  border-radius: 16rpx;
  border: 4rpx solid;
  font-size: 40rpx;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 4rpx;
  pointer-events: none;
  transition: opacity 0.1s ease;
}

.swipe-indicator.like {
  right: 40rpx;
  color: #52c41a;
  border-color: #52c41a;
  background: rgba(82, 196, 26, 0.1);
  transform: rotate(15deg);
}

.swipe-indicator.nope {
  left: 40rpx;
  color: #ff4d4f;
  border-color: #ff4d4f;
  background: rgba(255, 77, 79, 0.1);
  transform: rotate(-15deg);
}
</style>
