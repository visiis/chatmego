import type { ApiResponse } from '../types'

const BASE_URL = 'https://chatmego.com'

interface RequestConfig {
  url: string
  method?: 'GET' | 'POST' | 'PUT' | 'DELETE'
  data?: Record<string, any>
  headers?: Record<string, string>
}

function requestInterceptor(config: RequestConfig): RequestConfig {
  const token = uni.getStorageSync('token')
  const headers = {
    'Content-Type': 'application/json',
    ...config.headers
  }
  
  if (token) {
    headers['Authorization'] = 'Bearer ' + token
  }
  
  return {
    ...config,
    headers,
    url: config.url.startsWith('http') ? config.url : BASE_URL + config.url
  }
}

function responseInterceptor(response: any): ApiResponse<any> {
  const data = response.data as ApiResponse<any>
  
  if (data.code === 401) {
    uni.removeStorageSync('token')
    uni.removeStorageSync('user')
    uni.redirectTo({ url: '/pages/auth/login' })
    throw new Error('登录已过期')
  }
  
  return data
}

export function request<T = any>(
  url: string,
  method: 'GET' | 'POST' | 'PUT' | 'DELETE' = 'GET',
  data?: Record<string, any>,
  headers?: Record<string, string>
): Promise<ApiResponse<T>> {
  const config = requestInterceptor({ url, method, data, headers })
  
  return new Promise((resolve, reject) => {
    uni.request({
      url: config.url,
      method: config.method,
      data: config.data,
      header: config.headers,
      success: (res) => {
        try {
          const response = responseInterceptor(res)
          if (response.code === 200 || response.success === true) {
            resolve(response)
          } else {
            reject(new Error(response.message || '请求失败'))
          }
        } catch (error) {
          reject(error)
        }
      },
      fail: (err) => {
        reject(new Error(err.errMsg || '网络请求失败'))
      }
    })
  })
}
