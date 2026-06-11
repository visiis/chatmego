import { post } from '@/utils/request'
import type { LoginResponse } from '@/types'

export function sendCode(phone: string, type: 'register' | 'login') {
  return post('/api/auth/send-code', { phone, type })
}

export function register(data: {
  phone: string
  code: string
  password: string
  nickname: string
  gender: number
  birthday: string
  invite_code?: string
}) {
  return post<LoginResponse>('/api/auth/register', data)
}

export function login(phone: string, password: string) {
  return post<LoginResponse>('/api/auth/login', { phone, password })
}

export function loginByCode(phone: string, code: string) {
  return post<LoginResponse>('/api/auth/login-by-code', { phone, code })
}

export function refreshToken(refreshToken: string) {
  return post('/api/auth/refresh', {}, { Authorization: `Bearer ${refreshToken}` })
}
