import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User, MembershipLevel } from '@/types'
import { getProfile } from '@/api/user'
import { getMembershipLevels } from '@/api/membership'

export const useUserStore = defineStore('user', () => {
  const user = ref<User | null>(null)
  const token = ref('')
  const imAccount = ref('')
  const imToken = ref('')
  const membershipLevels = ref<MembershipLevel[]>([])
  const isLoggedIn = computed(() => !!token.value)

  function setUser(data: User) {
    user.value = data
  }

  function setToken(newToken: string) {
    token.value = newToken
    uni.setStorageSync('token', newToken)
  }

  function setImInfo(account: string, token: string) {
    imAccount.value = account
    imToken.value = token
    uni.setStorageSync('im_account', account)
    uni.setStorageSync('im_token', token)
  }

  function loadUserInfo() {
    const savedToken = uni.getStorageSync('token')
    const savedUser = uni.getStorageSync('user')
    
    if (savedToken) {
      token.value = savedToken
    }
    
    if (savedUser) {
      try {
        user.value = JSON.parse(savedUser)
      } catch (e) {
        console.error('Failed to parse saved user')
      }
    }
    
    imAccount.value = uni.getStorageSync('im_account') || ''
    imToken.value = uni.getStorageSync('im_token') || ''
  }

  async function fetchUserProfile() {
    try {
      const response = await getProfile()
      user.value = response.data
      uni.setStorageSync('user', JSON.stringify(response.data))
    } catch (e) {
      console.error('Failed to fetch profile:', e)
    }
  }

  async function fetchMembershipLevels() {
    try {
      const response = await getMembershipLevels()
      membershipLevels.value = response.data.levels
    } catch (e) {
      console.error('Failed to fetch membership levels:', e)
    }
  }

  function logout() {
    user.value = null
    token.value = ''
    imAccount.value = ''
    imToken.value = ''
    uni.removeStorageSync('token')
    uni.removeStorageSync('user')
    uni.removeStorageSync('im_account')
    uni.removeStorageSync('im_token')
    uni.switchTab({ url: '/pages/discover/index' })
  }

  return {
    user,
    token,
    imAccount,
    imToken,
    membershipLevels,
    isLoggedIn,
    setUser,
    setToken,
    setImInfo,
    loadUserInfo,
    fetchUserProfile,
    fetchMembershipLevels,
    logout
  }
})
