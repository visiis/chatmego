import type { ApiResponse } from '../types'
import { request } from '../utils/request'

export interface UserCard {
  id: number
  nickname: string
  avatar: string
  gender: string
  age: number
  height: string
  weight: string
  hobbies: string
  love_declaration: string
  is_vip: number
  distance?: string
  location?: string
  main_photo?: string
  photo_count?: number
}

export interface MatchInfo {
  id: number
  user: {
    id: number
    nickname: string
    avatar: string
    is_online: boolean
  }
  matched_at: string
  last_message?: {
    content: string
    created_at: string
  }
}

export function getCards(): Promise<UserCard[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/discover/cards', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as UserCard[])
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getRecommend(): Promise<UserCard[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/discover/recommend', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as UserCard[])
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function likeUser(userId: number): Promise<{ is_match: boolean; match?: MatchInfo }> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/discover/like/${userId}`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as { is_match: boolean; match?: MatchInfo })
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function dislikeUser(userId: number): Promise<void> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/discover/dislike/${userId}`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(() => {
        resolve()
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function passUser(userId: number): Promise<void> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/discover/pass/${userId}`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(() => {
        resolve()
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getMatches(): Promise<MatchInfo[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/discover/matches', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data.matches as MatchInfo[])
      })
      .catch(error => {
        reject(error)
      })
  })
}