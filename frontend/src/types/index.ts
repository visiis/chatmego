export interface ApiResponse<T = any> {
  code: number
  message: string
  data: T
}

export interface User {
  id: number
  email: string
  nickname: string
  avatar: string
  phone?: string
  gender?: number
  birthday?: string
  bio?: string
  love_declaration?: string
}

export interface LoginData {
  user: User
  token: string
}

export interface LoginForm {
  email: string
  password: string
}

export interface RegisterForm {
  name: string
  email: string
  password: string
  password_confirmation: string
  gender: string
}