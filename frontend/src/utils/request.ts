import type { ApiResponse } from '@/types'

const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'

export function request<T = any>(
  url: string,
  method: 'GET' | 'POST' | 'PUT' | 'DELETE' = 'GET',
  data?: Record<string, any>,
  headers?: Record<string, string>
): Promise<ApiResponse<T>> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    
    const defaultHeaders: Record<string, string> = {
      'Content-Type': 'application/json',
      'Accept-Language': 'zh-TW'
    }
    
    if (token) {
      defaultHeaders['Authorization'] = `Bearer ${token}`
    }
    
    uni.request({
      url: `${baseUrl}${url}`,
      method,
      data,
      header: { ...defaultHeaders, ...headers },
      success: (res) => {
        const response = res.data as ApiResponse<T>
        if (response.code === 200) {
          resolve(response)
        } else if (response.code === 401) {
          uni.removeStorageSync('token')
          uni.removeStorageSync('user')
          uni.navigateTo({ url: '/pages/auth/login' })
          reject(new Error('登录已过期'))
        } else {
          reject(new Error(response.message || '请求失败'))
        }
      },
      fail: (err) => {
        reject(new Error(err.errMsg || '网络请求失败'))
      }
    })
  })
}

export function get<T = any>(url: string, params?: Record<string, any>): Promise<ApiResponse<T>> {
  if (params) {
    const query = new URLSearchParams(params).toString()
    url += `?${query}`
  }
  return request<T>(url, 'GET')
}

export function post<T = any>(url: string, data?: Record<string, any>): Promise<ApiResponse<T>> {
  return request<T>(url, 'POST', data)
}

export function put<T = any>(url: string, data?: Record<string, any>): Promise<ApiResponse<T>> {
  return request<T>(url, 'PUT', data)
}

export function del<T = any>(url: string): Promise<ApiResponse<T>> {
  return request<T>(url, 'DELETE')
}

export function uploadFile(url: string, filePath: string, name: string = 'file'): Promise<any> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    
    uni.uploadFile({
      url: `${baseUrl}${url}`,
      filePath,
      name,
      header: {
        'Authorization': `Bearer ${token}`
      },
      success: (res) => {
        try {
          const data = JSON.parse(res.data)
          resolve(data)
        } catch {
          resolve(res.data)
        }
      },
      fail: (err) => {
        reject(err)
      }
    })
  })
}
