import { get, post } from '@/utils/request'

export interface Friend {
  id: number
  nickname: string
  avatar: string
  gender: number
  age: number
  love_declaration: string
  status: string
}

export interface FriendRequest {
  id: number
  user: Friend
  message: string
}

export function getFriends() {
  return get<{ friends: Friend[] }>('/api/friends')
}

export function getFriendRequests() {
  return get<{ requests: FriendRequest[] }>('/api/friends/requests')
}

export function sendFriendRequest(userId: number) {
  return post('/api/friends/request/' + userId)
}

export function acceptFriendRequest(userId: number) {
  return post('/api/friends/accept/' + userId)
}

export function rejectFriendRequest(userId: number) {
  return post('/api/friends/reject/' + userId)
}