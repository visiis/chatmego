import type { ApiResponse, User } from '../types'
import { request } from '../utils/request'

export interface UserProfile {
  id: number
  phone?: string
  name: string
  avatar?: string
  avatar_url?: string
  gender?: string
  age?: number
  height?: string
  weight?: string
  hobbies?: string
  specialty?: string
  love_declaration?: string
  points?: number
  total_points_earned?: number
  coins?: number
  current_level?: {
    name: string
    icon: string
    level_order: number
  }
  has_membership?: boolean
  membership?: {
    name: string
    code: string
    expired_at: string
  }
  created_at?: string
}

export interface PointsInfo {
  points: number
  total_points_earned: number
  current_level?: {
    name: string
    icon: string
    level_order: number
  }
}

export interface CheckInResult {
  points_earned: number
  consecutive_days: number
  bonus_points: number
  balance_after: number
}

export function getProfile(): Promise<UserProfile> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/user/profile', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as UserProfile)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function updateProfile(data: Partial<UserProfile>): Promise<UserProfile> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/user/profile', 'PUT', data, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as UserProfile)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getPoints(): Promise<PointsInfo> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/user/points', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as PointsInfo)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function checkIn(): Promise<CheckInResult> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/user/sign-in', 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as CheckInResult)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getUserProfile(userId: number): Promise<UserProfile> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/user/${userId}/profile`, 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as UserProfile)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export interface MemberLevel {
  id: number
  name: string
  icon: string
  level_order: number
  min_points: number
  max_points: number
  privileges: string[]
}

export function getMemberLevels(): Promise<MemberLevel[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/membership/levels', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as MemberLevel[])
      })
      .catch(error => {
        reject(error)
      })
  })
}

export interface AlbumResult {
  photos: Photo[]
}

export interface Photo {
  id: number
  url: string
  thumbnail_url?: string
  title?: string
  is_main: number
  is_premium: number
  points_price: number
}

export function getAlbum(): Promise<AlbumResult> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/user/album', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as AlbumResult)
      })
      .catch(error => {
        reject(error)
      })
  })
}