import { request } from '../utils/request'

export interface UserGift {
  id: number
  gift_id: number
  name: string
  image: string
  description: string
  price_type: string
  price: number
  quantity: number
  created_at: string
}

export interface Redemption {
  id: number
  gift_id: number
  gift_name: string
  gift_image: string
  quantity: number
  recipient_name: string
  phone: string
  address: string
  recipient_phone: string
  status: string
  status_label: string
  created_at: string
}

export interface Gift {
  id: number
  name: string
  type: string
  price_type: string
  price: number
  image: string
  description: string
  is_active: boolean
  sort_order: number
}

export interface GiftResponse {
  physical_gifts: UserGift[]
  virtual_gifts: UserGift[]
  has_redemption_info: boolean
}

export interface RedemptionInfo {
  recipient_name: string
  phone: string
  address: string
  recipient_phone?: string
}

export function getUserGifts(): Promise<GiftResponse> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/gift/my-gifts', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as GiftResponse)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getAllGifts(): Promise<Gift[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/gift/all', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as Gift[])
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getRedemptionHistory(): Promise<Redemption[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/gift/redemptions', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as Redemption[])
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function saveRedemptionInfo(data: RedemptionInfo): Promise<any> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/gift/save-redemption-info', 'POST', data, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function redeemGifts(userGiftIds: number[], info: RedemptionInfo): Promise<any> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/gift/redeem', 'POST', {
      user_gift_ids: userGiftIds,
      ...info
    }, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response)
      })
      .catch(error => {
        reject(error)
      })
  })
}
