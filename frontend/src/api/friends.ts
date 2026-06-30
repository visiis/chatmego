import type { ApiResponse } from '../types'
import { request } from '../utils/request'

export interface Friend {
  id: number
  name: string
  nickname: string
  avatar: string
  gender?: string
  age?: number
  love_declaration?: string
  status?: string
}

export interface FriendRequest {
  id: number
  user: {
    id: number
    name: string
    nickname: string
    avatar: string
  }
  message?: string
  created_at: string
}

export function getFriends(): Promise<Friend[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/friends', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data.friends || response.data || [])
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getFriendRequests(): Promise<FriendRequest[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/friends/requests', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data || [])
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function sendFriendRequest(userId: number, message?: string): Promise<void> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/friends/request/${userId}`, 'POST', message ? { message } : undefined, { 'Authorization': 'Bearer ' + token })
      .then(() => {
        resolve()
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function acceptFriendRequest(userId: number): Promise<void> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/friends/accept/${userId}`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(() => {
        resolve()
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function rejectFriendRequest(userId: number): Promise<void> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/friends/reject/${userId}`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(() => {
        resolve()
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getBlocked(): Promise<Friend[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/friends/blocked', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data.blocked || response.data || [])
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function blockFriend(userId: number): Promise<void> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/friends/block/${userId}`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(() => {
        resolve()
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function unblockFriend(userId: number): Promise<void> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/friends/unblock/${userId}`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(() => {
        resolve()
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function deleteFriend(userId: number): Promise<void> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/friends/delete/${userId}`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(() => {
        resolve()
      })
      .catch(error => {
        reject(error)
      })
  })
}
