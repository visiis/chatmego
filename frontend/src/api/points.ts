import { get, post } from '@/utils/request'

export function getPoints() {
  return get('/api/user/points')
}

export function getPointTransactions(params?: { page?: number; per_page?: number; type?: string }) {
  return get('/api/user/point-transactions', params)
}

export function checkIn() {
  return post('/api/user/check-in')
}
