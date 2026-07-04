<template>
  <view 
    class="swipe-card"
    :style="cardStyle"
    @touchstart="onTouchStart"
    @touchmove="onTouchMove"
    @touchend="onTouchEnd"
    @mousedown="onMouseDown"
  >
    <slot></slot>
    
    <view 
      class="swipe-indicator like"
      :style="{ opacity: likeOpacity, transform: `rotate(15deg) scale(${likeScale})` }"
    >
      <text>LIKE</text>
    </view>
    
    <view 
      class="swipe-indicator nope"
      :style="{ opacity: nopeOpacity, transform: `rotate(-15deg) scale(${nopeScale})` }"
    >
      <text>NOPE</text>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

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
const isMouseDown = ref(false)

const SWIPE_THRESHOLD = 150
const ROTATION_FACTOR = 0.05
const OUT_OF_SCREEN = 500

const cardStyle = computed(() => {
  if (!isDragging.value) {
    return {
      transform: `translateX(${deltaX.value}px) translateY(${deltaY.value * 0.3}px) rotate(${deltaX.value * ROTATION_FACTOR}deg)`,
      transition: deltaX.value !== 0 ? 'transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)' : 'transform 0.3s ease-out'
    }
  }
  
  const rotate = deltaX.value * ROTATION_FACTOR
  const scale = 1 + Math.min(Math.abs(deltaX.value) * 0.0005, 0.05)
  
  return {
    transform: `translateX(${deltaX.value}px) translateY(${deltaY.value * 0.3}px) rotate(${rotate}deg) scale(${scale})`,
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

const likeScale = computed(() => {
  if (deltaX.value > 0) {
    return 1 + Math.min(deltaX.value / 200, 0.3)
  }
  return 1
})

const nopeScale = computed(() => {
  if (deltaX.value < 0) {
    return 1 + Math.min(Math.abs(deltaX.value) / 200, 0.3)
  }
  return 1
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
  handleSwipeEnd()
}

function onMouseDown(e: MouseEvent) {
  if (props.disabled) return
  
  startX.value = e.clientX
  startY.value = e.clientY
  isDragging.value = true
  isMouseDown.value = true
  
  document.addEventListener('mousemove', onMouseMove)
  document.addEventListener('mouseup', onMouseUp)
}

function onMouseMove(e: MouseEvent) {
  if (!isDragging.value || !isMouseDown.value || props.disabled) return
  
  deltaX.value = e.clientX - startX.value
  deltaY.value = e.clientY - startY.value
}

function onMouseUp() {
  if (!isMouseDown.value) return
  
  document.removeEventListener('mousemove', onMouseMove)
  document.removeEventListener('mouseup', onMouseUp)
  isMouseDown.value = false
  
  handleSwipeEnd()
}

function handleSwipeEnd() {
  isDragging.value = false
  
  if (deltaX.value > SWIPE_THRESHOLD) {
    deltaX.value = OUT_OF_SCREEN
    setTimeout(() => {
      emit('swipeRight')
      resetPosition()
    }, 300)
  } else if (deltaX.value < -SWIPE_THRESHOLD) {
    deltaX.value = -OUT_OF_SCREEN
    setTimeout(() => {
      emit('swipeLeft')
      resetPosition()
    }, 300)
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
  deltaX.value = -OUT_OF_SCREEN
  setTimeout(() => {
    emit('swipeLeft')
    resetPosition()
  }, 300)
}

function swipeRight() {
  deltaX.value = OUT_OF_SCREEN
  setTimeout(() => {
    emit('swipeRight')
    resetPosition()
  }, 300)
}

defineExpose({
  swipeLeft,
  swipeRight
})

onUnmounted(() => {
  document.removeEventListener('mousemove', onMouseMove)
  document.removeEventListener('mouseup', onMouseUp)
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
  cursor: grab;
  user-select: none;
  
  &:active {
    cursor: grabbing;
  }
}

.swipe-indicator {
  position: absolute;
  top: 60rpx;
  padding: 20rpx 40rpx;
  border-radius: 16rpx;
  border: 6rpx solid;
  font-size: 48rpx;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 8rpx;
  pointer-events: none;
  transition: opacity 0.15s ease, transform 0.15s ease;
  z-index: 100;
}

.swipe-indicator.like {
  right: 40rpx;
  color: #52c41a;
  border-color: #52c41a;
  background: rgba(82, 196, 26, 0.15);
  box-shadow: 0 0 30rpx rgba(82, 196, 26, 0.4);
}

.swipe-indicator.nope {
  left: 40rpx;
  color: #ff4d4f;
  border-color: #ff4d4f;
  background: rgba(255, 77, 79, 0.15);
  box-shadow: 0 0 30rpx rgba(255, 77, 79, 0.4);
}
</style>