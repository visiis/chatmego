import { request } from '../utils/request'

export interface AttractionStatus {
  my_type: number
  their_type: number
  is_mutual: boolean
}

export interface AttractionResponse {
  code: number
  message: string
  data?: AttractionStatus | { type: number; is_mutual?: boolean }
}

export function likeUser(userId: number): Promise<AttractionResponse> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/attraction/like/${userId}`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response as AttractionResponse)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function dislikeUser(userId: number): Promise<AttractionResponse> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/attraction/dislike/${userId}`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response as AttractionResponse)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function cancelAttraction(userId: number): Promise<AttractionResponse> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/attraction/cancel/${userId}`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response as AttractionResponse)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getAttractionStatus(userId: number): Promise<AttractionStatus> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/attraction/status/${userId}`, 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as AttractionStatus)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getMyLikes(): Promise<any[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/attraction/likes', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data || [])
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getMutualLikes(): Promise<any[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/attraction/mutual', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data || [])
      })
      .catch(error => {
        reject(error)
      })
  })
}