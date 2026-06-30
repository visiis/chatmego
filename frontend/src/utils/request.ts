import type { ApiResponse } from '../types'

const BASE_URL = 'https://chatmego.com'

export function request<T = any>(
  url: string,
  method: 'GET' | 'POST' | 'PUT' | 'DELETE' = 'GET',
  data?: Record<string, any>,
  headers?: Record<string, string>
): Promise<ApiResponse<T>> {
  const fullUrl = url.startsWith('http') ? url : BASE_URL + url
  
  return new Promise((resolve, reject) => {
    uni.request({
      url: fullUrl,
      method,
      data,
      header: {
        'Content-Type': 'application/json',
        ...headers
      },
      success: (res) => {
        const response = res.data as ApiResponse<T>
        if (response.code === 200 || response.success === true) {
          resolve(response)
        } else {
          reject(new Error(response.message))
        }
      },
      fail: (err) => {
        reject(new Error(err.errMsg || '请求失败'))
      }
    })
  })
}