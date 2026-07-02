import { request } from '../utils/request'

export interface MembershipPlan {
  id: number
  name: string
  code: string
  price: number
  price_yuan: number
  duration_days: number
  privileges: string[]
  icon: string
  badge_color: string
}

export interface MembershipInfo {
  has_membership: boolean
  plan?: {
    id: number
    name: string
    code: string
    icon: string
    badge_color: string
  }
  ends_at?: string
  days_remaining?: number
}

export interface SubscriptionHistory {
  id: number
  plan_id: number
  plan_name: string
  plan_code: string
  plan_icon: string
  plan_badge_color: string
  starts_at: string
  ends_at: string
  status: string
  price_paid: number
  notes: string
}

export interface MembershipResponse {
  user: {
    coins: number
    points: number
  }
  membership_info: MembershipInfo
  available_plans: MembershipPlan[]
  subscription_history: SubscriptionHistory[]
}

export function getMembershipInfo(): Promise<MembershipResponse> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/membership', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as MembershipResponse)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function purchaseMembership(planId: number, months: number): Promise<any> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/membership/purchase', 'POST', { plan_id: planId, months }, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function convertPoints(points: number): Promise<any> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/membership/convert-points', 'POST', { points }, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function cancelMembership(): Promise<any> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/membership/cancel', 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data)
      })
      .catch(error => {
        reject(error)
      })
  })
}
