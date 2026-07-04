<template>
  <text class="fa" :class="iconClass" :style="iconStyle"></text>
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
  'compass': 'compass',
  'users': 'users',
  'comment': 'comment',
  'comment-dots': 'comment-dots',
  'user': 'user',
  'heart': 'heart',
  'xmark': 'xmark',
  'user-circle': 'user-circle',
  'search': 'search',
  'filter': 'filter',
  'mars': 'mars',
  'venus': 'venus',
  'cog': 'cog',
  'star': 'star',
  'image': 'image',
  'gift': 'gift',
  'pencil': 'pencil',
  'ban': 'ban',
  'trash': 'trash',
  'check-circle': 'check-circle',
  'ruler': 'ruler',
  'weight': 'weight',
  'graduation-cap': 'graduation-cap',
  'gamepad': 'gamepad',
  'user-plus': 'user-plus',
  'crown': 'crown',
  'bell': 'bell',
  'arrow-left': 'arrow-left',
  'arrow-right': 'arrow-right',
  'plus': 'plus',
  'minus': 'minus',
  'exchange': 'exchange',
  'history': 'history',
  'credit-card': 'credit-card',
  'calendar-check': 'calendar-check',
  'calendar-alt': 'calendar-alt',
  'list-alt': 'list-alt',
  'info-circle': 'info-circle',
  'file-text': 'file-text',
  'file-contract': 'file-contract',
  'lock': 'lock',
  'lock-open': 'lock-open',
  'eye': 'eye',
  'eye-slash': 'eye-slash',
  'check': 'check',
  'google': 'google',
  'mobile': 'mobile',
  'camera': 'camera',
  'circle': 'circle',
  'shopping-cart': 'shopping-cart',
  'globe': 'globe',
  'box-open': 'box-open',
  'comment-slash': 'comment-slash',
  'chevron-right': 'chevron-right',
  'chevron-down': 'chevron-down',
  'ellipsis-v': 'ellipsis-v',
  'times': 'times',
}

const iconClass = computed(() => {
  const name = iconNameMap[props.name] || props.name
  const prefix = props.type === 'brands' ? 'fab' : props.type === 'regular' ? 'far' : 'fas'
  return `${prefix} fa-${name}`
})

const iconStyle = computed(() => ({
  fontSize: props.size || '24px',
  color: props.color || '#333'
}))
</script>