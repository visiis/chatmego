export function isLoggedIn(): boolean {
  const token = uni.getStorageSync('token')
  const user = uni.getStorageSync('user')
  return !!token && !!user
}

export function getToken(): string {
  return uni.getStorageSync('token') || ''
}

export function requireLogin(redirectUrl?: string): boolean {
  if (!isLoggedIn()) {
    uni.redirectTo({
      url: redirectUrl || '/pages/auth/login'
    })
    return false
  }
  return true
}

export function loginSuccess(token: string, user: any) {
  uni.setStorageSync('token', token)
  uni.setStorageSync('user', JSON.stringify(user))
}

export function logout() {
  uni.removeStorageSync('token')
  uni.removeStorageSync('user')
  uni.redirectTo({ url: '/pages/auth/login' })
}
