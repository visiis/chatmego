import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { User } from '@/types'
import { getRecommendUsers, likeUser, dislikeUser } from '@/api/discover'

export const useDiscoverStore = defineStore('discover', () => {
  const users = ref<User[]>([])
  const currentIndex = ref(0)
  const dailyLikesLeft = ref(50)
  const isLoading = ref(false)
  const filter = ref({
    minAge: 18,
    maxAge: 50,
    gender: 0,
    location: ''
  })

  async function fetchUsers() {
    isLoading.value = true
    try {
      const response = await getRecommendUsers({
        page: 1,
        per_page: 20,
        min_age: filter.value.minAge,
        max_age: filter.value.maxAge,
        gender: filter.value.gender,
        location: filter.value.location
      })
      users.value = response.data.users
      currentIndex.value = 0
    } catch (e) {
      console.error('Failed to fetch users:', e)
    } finally {
      isLoading.value = false
    }
  }

  async function like(userId: number) {
    if (dailyLikesLeft.value <= 0) {
      uni.showToast({ title: '今日喜欢次数已用完', icon: 'none' })
      return false
    }

    try {
      const response = await likeUser(userId)
      dailyLikesLeft.value--
      
      if (response.data.is_match && response.data.match) {
        uni.showModal({
          title: '匹配成功!',
          content: '恭喜你与对方互相喜欢了!',
          confirmText: '立即聊天',
          cancelText: '继续浏览',
          success: (res) => {
            if (res.confirm) {
              uni.navigateTo({ url: `/pages/chats/detail?userId=${userId}` })
            }
          }
        })
      } else {
        uni.showToast({ title: '喜欢成功', icon: 'success' })
      }
      
      removeCurrentUser()
      return true
    } catch (e: any) {
      uni.showToast({ title: e.message || '操作失败', icon: 'none' })
      return false
    }
  }

  async function dislike(userId: number) {
    try {
      await dislikeUser(userId)
      removeCurrentUser()
      return true
    } catch (e: any) {
      uni.showToast({ title: e.message || '操作失败', icon: 'none' })
      return false
    }
  }

  function removeCurrentUser() {
    if (users.value.length > 0) {
      users.value.splice(currentIndex.value, 1)
      if (currentIndex.value >= users.value.length) {
        currentIndex.value = users.value.length - 1
      }
    }
  }

  function getCurrentUser() {
    return users.value[currentIndex.value]
  }

  function hasMoreUsers() {
    return users.value.length > 0
  }

  function updateFilter(newFilter: Partial<typeof filter.value>) {
    filter.value = { ...filter.value, ...newFilter }
  }

  return {
    users,
    currentIndex,
    dailyLikesLeft,
    isLoading,
    filter,
    fetchUsers,
    like,
    dislike,
    getCurrentUser,
    hasMoreUsers,
    updateFilter
  }
})
