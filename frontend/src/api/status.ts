import { request } from '../utils/request'

export interface Status {
  id: number
  user_id: number
  content: string
  images: string[]
  is_private: boolean
  likes_count: number
  comments_count: number
  liked?: boolean
  is_liked?: boolean
  created_at: string
  comments?: StatusComment[]
}

export interface StatusComment {
  id: number
  status_id: number
  user_id: number
  content: string
  user: {
    id: number
    name: string
    avatar: string
  }
  created_at: string
}

export function getUserStatuses(userId: number): Promise<Status[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/status/user/${userId}`, 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        const data = response.data || []
        resolve(data as Status[])
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getStatuses(page: number = 1, limit: number = 10): Promise<{ data: Status[], has_more: boolean }> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/status?page=${page}&limit=${limit}`, 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        const data = response.data || []
        const has_more = response.has_more || false
        resolve({ data, has_more })
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function createStatus(content: string, images: string[] = [], isPrivate: boolean = false): Promise<Status> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/status', 'POST', { content, images, is_private: isPrivate }, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as Status)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function likeStatus(statusId: number): Promise<{ liked: boolean; count: number }> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/status/like/${statusId}`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as { liked: boolean; count: number })
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function commentStatus(statusId: number, content: string): Promise<StatusComment> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/status/comment/${statusId}`, 'POST', { content }, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as StatusComment)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function deleteStatus(statusId: number): Promise<void> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/status/${statusId}`, 'DELETE', undefined, { 'Authorization': 'Bearer ' + token })
      .then(() => {
        resolve()
      })
      .catch(error => {
        reject(error)
      })
  })
}