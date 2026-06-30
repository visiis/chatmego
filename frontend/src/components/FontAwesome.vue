<template>
  <text class="fa fa-lg" :class="iconClass" :style="iconStyle"></text>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  name: string
  size?: string
  color?: string
  type?: 'solid' | 'regular' | 'brands'
}>()

const iconNameMap: Record<string, string> = {
  'coins': 'bitcoin',
  'calendar-check': 'calendar-check-o',
  'comment-dots': 'comments-o',
  'user-circle': 'user-circle-o',
  'pencil': 'pencil-square-o',
  'image': 'picture-o',
  'gift': 'gift',
  'heart': 'heart-o',
  'star': 'star-o',
  'cog': 'gear',
  'compass': 'compass',
  'users': 'users',
  'comment': 'comment-o',
  'user': 'user',
  'chevron-right': 'chevron-right',
  'ellipsis-v': 'ellipsis-v',
  'ban': 'ban',
  'trash': 'trash-o',
  'check-circle': 'check-circle-o',
  'mars': 'mars',
  'venus': 'venus',
  'ruler': 'ruler',
  'weight': 'balance-scale',
  'graduation-cap': 'graduation-cap',
  'gamepad': 'gamepad',
  'user-plus': 'user-plus',
  'crown': 'crown',
  'bell': 'bell-o'
}

const iconClass = computed(() => {
  const name = iconNameMap[props.name] || props.name
  const prefix = props.type === 'brands' ? 'fa' : 'fa'
  return `${prefix} fa-${name}`
})

const iconStyle = computed(() => ({
  fontSize: props.size || '24px',
  color: props.color || '#333'
}))
</script>
