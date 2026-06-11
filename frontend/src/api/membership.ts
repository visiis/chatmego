import { get, post } from '@/utils/request'
import type { MembershipLevel } from '@/types'

export function getMembershipLevels() {
  return get<{ levels: MembershipLevel[] }>('/api/membership-levels')
}

export function getCurrentMembership() {
  return get('/api/user/membership')
}

export function purchaseMembership(levelId: number, duration: 'monthly' | 'quarterly' | 'yearly', paymentMethod: string) {
  return post('/api/membership/purchase', { level_id: levelId, duration, payment_method: paymentMethod })
}
