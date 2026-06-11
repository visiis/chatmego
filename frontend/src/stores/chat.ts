import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Message, Match } from '@/types'

export const useChatStore = defineStore('chat', () => {
  const messages = ref<Record<number, Message[]>>({})
  const matches = ref<Match[]>([])
  const currentChatUserId = ref<number | null>(null)
  const unreadCounts = ref<Record<number, number>>({})

  function addMessage(userId: number, message: Message) {
    if (!messages.value[userId]) {
      messages.value[userId] = []
    }
    messages.value[userId].push(message)
  }

  function setMessages(userId: number, msgs: Message[]) {
    messages.value[userId] = msgs
  }

  function getMessages(userId: number) {
    return messages.value[userId] || []
  }

  function setMatches(data: Match[]) {
    matches.value = data
  }

  function setCurrentChatUserId(userId: number | null) {
    currentChatUserId.value = userId
    if (userId !== null) {
      unreadCounts.value[userId] = 0
    }
  }

  function incrementUnread(userId: number) {
    if (!unreadCounts.value[userId]) {
      unreadCounts.value[userId] = 0
    }
    unreadCounts.value[userId]++
  }

  function getUnreadCount(userId: number) {
    return unreadCounts.value[userId] || 0
  }

  function markAllAsRead(userId: number) {
    unreadCounts.value[userId] = 0
  }

  return {
    messages,
    matches,
    currentChatUserId,
    unreadCounts,
    addMessage,
    setMessages,
    getMessages,
    setMatches,
    setCurrentChatUserId,
    incrementUnread,
    getUnreadCount,
    markAllAsRead
  }
})
