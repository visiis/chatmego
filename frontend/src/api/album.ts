import { request } from '../utils/request'

export interface Album {
  id: number
  name: string
  description: string
  privacy: boolean
  price: number
  view_count: number
  purchase_count: number
  cover_photos: string[]
  photos_count: number
  created_at: string
}

export interface AlbumPhoto {
  id: number
  image_url: string
  thumbnail_url: string
  title: string
  description: string
  can_view_full: boolean
}

export interface AlbumDetail {
  id: number
  name: string
  description: string
  privacy: boolean
  price: number
  view_count: number
  purchase_count: number
  is_owner: boolean
  can_view: boolean
  photos: AlbumPhoto[]
  owner: {
    id: number
    name: string
    avatar: string
  }
}

export interface PurchaseRecord {
  id: number
  album_id: number
  album_name: string
  album_cover: string
  price: number
  seller_name: string
  seller_avatar: string
  expires_at: string
  created_at: string
}

export interface PurchaseResult {
  expires_at: string
}

export function getUserAlbums(): Promise<Album[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/album', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as Album[])
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getAlbum(albumId: number): Promise<AlbumDetail> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/album/${albumId}`, 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as AlbumDetail)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function createAlbum(data: { name: string; description?: string; privacy?: boolean; price?: number }): Promise<Album> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/album', 'POST', data, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as Album)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function updateAlbum(albumId: number, data: { name: string; description?: string; privacy?: boolean; price?: number }): Promise<Album> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/album/${albumId}`, 'PUT', data, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as Album)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function deleteAlbum(albumId: number): Promise<any> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/album/${albumId}`, 'DELETE', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function uploadPhoto(albumId: number, filePath: string): Promise<any> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    const uploadTask = uni.uploadFile({
      url: 'https://chatmego.com/api/album/' + albumId + '/upload',
      filePath: filePath,
      name: 'image',
      header: {
        'Authorization': 'Bearer ' + token
      },
      success: (res) => {
        try {
          const data = JSON.parse(res.data)
          if (data.code === 200) {
            resolve(data)
          } else {
            reject(new Error(data.message || '上传失败'))
          }
        } catch (e) {
          reject(new Error('上传失败'))
        }
      },
      fail: (err) => {
        reject(new Error(err.errMsg || '上传失败'))
      }
    })
  })
}

export function deletePhoto(photoId: number): Promise<any> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/album/photo/${photoId}`, 'DELETE', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function purchaseAlbum(albumId: number): Promise<PurchaseResult> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/album/${albumId}/purchase`, 'POST', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as PurchaseResult)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function checkPurchase(albumId: number): Promise<{ purchased: boolean; expires_at: string | null }> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/album/${albumId}/check-purchase`, 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as { purchased: boolean; expires_at: string | null })
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getPurchaseHistory(): Promise<PurchaseRecord[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/album/purchases/history', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        resolve(response.data as PurchaseRecord[])
      })
      .catch(error => {
        reject(error)
      })
  })
}
