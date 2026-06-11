import { get, put, post } from '@/utils/request'
import type { User, Photo } from '@/types'

export function getProfile() {
  return get<User>('/api/user/profile')
}

export function getUserProfile(userId: number) {
  return get<User>(`/api/user/${userId}/profile`)
}

export function updateProfile(data: Partial<User>) {
  return put('/api/user/profile', data)
}

export function uploadPhoto(filePath: string, isMain: number = 0, isPremium: number = 0, pointsPrice: number = 0) {
  return post('/api/user/photos', {
    photo: filePath,
    is_main: isMain,
    is_premium: isPremium,
    points_price: pointsPrice
  })
}

export function getPhotos() {
  return get<{ photos: Photo[] }>('/api/user/photos')
}

export function deletePhoto(photoId: number) {
  return post(`/api/user/photos/${photoId}/delete`)
}

export function setMainPhoto(photoId: number) {
  return put(`/api/user/photos/${photoId}/set-main`)
}

export function purchasePhoto(photoId: number) {
  return post(`/api/photos/${photoId}/purchase`)
}

export function getPhotoPurchases() {
  return get('/api/user/photo-purchases')
}
