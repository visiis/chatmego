import { get, post } from '@/utils/request'
import type { User, Match, LikeRecord } from '@/types'

export function getRecommend() {
  return get<{ users: User[] }>('/api/discover/recommend')
}

export function getRecommendUsers(params?: {
  page?: number
  per_page?: number
  min_age?: number
  max_age?: number
  gender?: number
  location?: string
}) {
  return get<{ users: User[]; pagination: any }>('/api/discover/users', params)
}

export function likeUser(userId: number) {
  return post<{ is_match: boolean; match?: Match }>(`/api/users/${userId}/like`)
}

export function dislikeUser(userId: number) {
  return post(`/api/users/${userId}/dislike`)
}

export function getMatches() {
  return get<{ matches: Match[] }>('/api/matches')
}

export function getLikesReceived() {
  return get<{ users: LikeRecord[] }>('/api/likes/received')
}
