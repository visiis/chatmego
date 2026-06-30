import { request } from '../utils/request'

export interface PointsLog {
  id: number
  type: string
  amount: number
  balance: number
  reason: string
  created_at: string
}

export interface CoinsLog {
  id: number
  type: string
  amount: number
  balance: number
  reason: string
  created_at: string
}

export interface PointsHistoryResponse {
  records: PointsLog[]
  total: number
  current_page: number
  last_page: number
}

export interface CoinsHistoryResponse {
  records: CoinsLog[]
  total: number
  current_page: number
  last_page: number
}

export interface ConvertResponse {
  coins_obtained: number
  remaining_points: number
  current_coins: number
}

export function getPointsHistory(): Promise<PointsHistoryResponse> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/points/history', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as PointsHistoryResponse)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function convertToCoins(points: number): Promise<ConvertResponse> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/points/convert', 'POST', { points }, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as ConvertResponse)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getCoinsHistory(): Promise<CoinsHistoryResponse> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/points/coins/history', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as CoinsHistoryResponse)
      })
      .catch(error => {
        reject(error)
      })
  })
}
