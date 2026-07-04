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
  'coins': 'coins',
  'calendar-check': 'calendar-check',
  'comment-dots': 'comment-dots',
  'user-circle': 'user-circle',
  'pencil': 'pencil',
  'image': 'image',
  'gift': 'gift',
  'heart': 'heart',
  'star': 'star',
  'cog': 'gear',
  'compass': 'compass',
  'users': 'users',
  'comment': 'comment',
  'user': 'user',
  'chevron-right': 'chevron-right',
  'ellipsis-v': 'ellipsis-vertical',
  'ban': 'ban',
  'trash': 'trash',
  'check-circle': 'check-circle',
  'mars': 'mars',
  'venus': 'venus',
  'ruler': 'ruler',
  'weight': 'weight',
  'graduation-cap': 'graduation-cap',
  'gamepad': 'gamepad',
  'user-plus': 'user-plus',
  'crown': 'crown',
  'bell': 'bell',
  'arrow-left': 'arrow-left',
  'plus': 'plus',
  'minus': 'minus',
  'exchange': 'arrow-right-left',
  'history': 'history',
  'credit-card': 'credit-card',
  'calendar-alt': 'calendar-days',
  'list-alt': 'list-alt',
  'info-circle': 'info-circle',
  'file-text': 'file-text',
  'file-contract': 'file-contract',
  'heart-o': 'heart',
  'star-o': 'star',
  'bell-o': 'bell',
  'check-circle-o': 'check-circle',
  'comment-o': 'comment',
  'comments-o': 'comment-dots',
  'user-circle-o': 'user-circle',
  'picture-o': 'image',
  'trash-o': 'trash',
  'pencil-square-o': 'pencil',
  'calendar-check-o': 'calendar-check',
  'lock': 'lock',
  'lock-open': 'lock-open',
  'eye': 'eye',
  'eye-slash': 'eye-slash',
  'check': 'check',
  'arrow-right': 'arrow-right',
  'google': 'google',
  'mobile': 'mobile-screen',
  'male': 'person-standing',
  'female': 'person-dress',
  'search': 'search',
  'times': 'xmark',
  'camera': 'camera',
  'circle': 'circle',
  'shopping-cart': 'shopping-cart',
  'globe': 'globe',
  'box-open': 'box-open',
  'comment-slash': 'comment-slash',
  'chevron-down': 'chevron-down',
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
