<template>
  <uni-icons :type="iconType" :size="iconSize" :color="props.color || '#333'"></uni-icons>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  name: string
  size?: string
  color?: string
}>()

const iconSize = computed(() => {
  const size = props.size || '24px'
  const match = size.match(/(\d+)/)
  return match ? parseInt(match[1]) : 24
})

const iconMap: Record<string, string> = {
  'compass': 'search',
  'users': 'contact',
  'comment': 'chat',
  'comment-dots': 'chat-filled',
  'user': 'person',
  'user-circle': 'person',
  'heart': 'heart',
  'heart-o': 'heart',
  'star': 'star',
  'star-o': 'star',
  'cog': 'settings',
  'image': 'photo',
  'gift': 'gift',
  'trash': 'trash',
  'trash-o': 'trash',
  'calendar-check': 'calendar',
  'check-circle': 'checkmarkempty',
  'check-circle-o': 'checkmarkempty',
  'chevron-right': 'right',
  'ellipsis-v': 'more',
  'bell': 'notification',
  'bell-o': 'notification',
  'ban': 'closeempty',
  'pencil': 'edit',
  'arrow-left': 'back',
  'plus': 'plus',
  'minus': 'minus',
  'search': 'search',
  'times': 'close',
  'mars': 'man',
  'venus': 'woman',
  'coins': 'coins',
  'credit-card': 'creditcard',
  'history': 'refresh',
  'file-text': 'file',
}

const iconType = computed(() => {
  return iconMap[props.name] || 'circle'
})
</script>