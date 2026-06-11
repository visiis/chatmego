import { get, post } from '@/utils/request'
import type { Gift, GiftCategory } from '@/types'

export function getGifts() {
  return get<{ categories: GiftCategory[] }>('/api/gifts')
}

export function sendGift(userId: number, giftId: number, message?: string) {
  return post(`/api/users/${userId}/send-gift`, { gift_id: giftId, message })
}

export function getGiftsReceived() {
  return get<{ gifts: any[] }>('/api/user/gifts-received')
}

export function convertGift(giftId: number) {
  return post(`/api/gifts/${giftId}/convert`)
}
