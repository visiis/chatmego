<template>
  <text class="iconfont" :class="className" :style="iconStyle">
    {{ icon }}
  </text>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
  icon: string
  size?: number | string
  color?: string
  className?: string
}>(), {
  size: 24,
  color: '#333',
  className: ''
})

const iconStyle = computed(() => ({
  fontSize: typeof props.size === 'number' ? `${props.size}rpx` : props.size,
  color: props.color
}))
</script>

<style lang="scss">
.iconfont {
  font-family: 'iconfont' !important;
  font-size: 24rpx;
  font-style: normal;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
</style>