import { request } from '../utils/request'

export interface Conversation {
  id: number
  friend: {
    id: number
    nickname: string
    avatar: string
    is_online?: boolean
  }
  last_message?: {
    id: number
    content: string
    type: string
    created_at: string
  }
  unread_count: number
}

export interface Message {
  id: number
  from_user_id: number
  to_user_id: number
  message: string
  type: string
  created_at: string
  is_read: boolean
  attachment_url?: string
}

export function getConversations(): Promise<Conversation[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/chat/conversations', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        if (response.code === 200 || response.success) {
          const list = response.data?.conversations ? response.data.conversations :
                       Array.isArray(response.conversations) ? response.conversations : 
                       Array.isArray(response.data) ? response.data : []
          resolve(list as Conversation[])
        } else {
          reject(new Error(response.message || '获取会话列表失败'))
        }
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getMessages(userId: number): Promise<Message[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/chat/messages/${userId}`, 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        if (response.code === 200 || response.success) {
          const msgs = response.data?.messages ? response.data.messages : 
                      Array.isArray(response.messages) ? response.messages : 
                      Array.isArray(response.data) ? response.data : []
          resolve(msgs as Message[])
        } else {
          reject(new Error(response.message || '获取消息失败'))
        }
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function sendMessage(userId: number, content: string, type: string = 'text'): Promise<Message> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/chat/send/${userId}`, 'POST', { message: content, type }, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        if (response.code === 200 || response.success) {
          resolve(response.data?.message ? response.data.message : response.message as Message)
        } else {
          reject(new Error(response.message || '发送失败'))
        }
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function fetchMessages(userId: number, lastMessageId: number): Promise<Message[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/chat/fetch/${userId}?last_message_id=${lastMessageId}`, 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        if (response.code === 200 || response.success) {
          const msgs = response.data?.messages ? response.data.messages : 
                      Array.isArray(response.messages) ? response.messages : 
                      Array.isArray(response.data) ? response.data : []
          resolve(msgs as Message[])
        } else {
          reject(new Error(response.message || '获取消息失败'))
        }
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function getGifts(): Promise<any[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request('/api/chat/gifts', 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        if (response.code === 200 || response.success) {
          const list = response.data?.gifts ? response.data.gifts :
                       Array.isArray(response.gifts) ? response.gifts : 
                       Array.isArray(response.data) ? response.data : []
          resolve(list)
        } else {
          reject(new Error(response.message || '获取礼物失败'))
        }
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function sendGift(userId: number, giftId: number): Promise<Message> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/chat/send-gift/${userId}`, 'POST', { gift_id: giftId }, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        if (response.code === 200 || response.success) {
          resolve(response.data?.message ? response.data.message : response.message as Message)
        } else {
          reject(new Error(response.message || '发送礼物失败'))
        }
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function fetchHistoryMessages(userId: number, beforeId: number, limit: number = 10): Promise<Message[]> {
  return new Promise((resolve, reject) => {
    const token = uni.getStorageSync('token')
    request(`/api/chat/history/${userId}?before_id=${beforeId}&limit=${limit}`, 'GET', undefined, { 'Authorization': 'Bearer ' + token })
      .then(response => {
        if (response.code === 200 || response.success) {
          const msgs = response.data?.messages ? response.data.messages : 
                      Array.isArray(response.messages) ? response.messages : 
                      Array.isArray(response.data) ? response.data : []
          resolve(msgs as Message[])
        } else {
          reject(new Error(response.message || '获取历史消息失败'))
        }
      })
      .catch(error => {
        reject(error)
      })
  })
}