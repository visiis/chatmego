import { storageManager } from './storage'
import { wsManager } from './websocket'
import { getMessages, fetchMessages, sendMessage } from '../api/chat'

export type MessageStatus = 'sending' | 'sent' | 'delivered' | 'read' | 'error'

export interface MessageWithStatus {
  id: number
  from_user_id: number
  to_user_id: number
  message: string
  type: string
  is_read: boolean
  created_at: string
  status: MessageStatus
  attachment_url?: string
  giftName?: string
  giftImage?: string
}

class MessageSyncManager {
  private syncInterval: ReturnType<typeof setInterval> | null = null
  private syncing = false

  async syncMessages(friendId: number): Promise<any[]> {
    if (this.syncing) {
      return []
    }

    this.syncing = true
    try {
      const lastMessageId = storageManager.getLastMessageId(friendId)
      let newMessages: any[] = []

      if (wsManager.connected) {
        const syncData = await fetchMessages(friendId, lastMessageId)
        newMessages = Array.isArray(syncData) ? syncData : (syncData?.messages || [])
      } else {
        const allMessages = await getMessages(friendId)
        const messagesArray = Array.isArray(allMessages) ? allMessages : (allMessages?.messages || [])
        newMessages = messagesArray.filter(m => m.id > lastMessageId)
      }

      if (newMessages.length > 0) {
        const cachedMessages = storageManager.getMessageCache(friendId)
        const updatedMessages = [...cachedMessages, ...newMessages]
        storageManager.saveMessageCache(friendId, updatedMessages)
      }

      return newMessages
    } catch (e) {
      console.error('Message sync error:', e)
      return []
    } finally {
      this.syncing = false
    }
  }

  async sendMessageWithSync(friendId: number, content: string, type: string = 'text'): Promise<MessageWithStatus> {
    const tempId = Date.now() + Math.random()

    const localMessage: MessageWithStatus = {
      id: tempId,
      from_user_id: parseInt(uni.getStorageSync('userId') || '0'),
      to_user_id: friendId,
      message: content,
      type,
      is_read: false,
      created_at: new Date().toISOString(),
      status: 'sending'
    }

    storageManager.addMessageToCache(friendId, localMessage)

    try {
      if (wsManager.connected) {
        await wsManager.send('message', {
          to_user_id: friendId,
          message: content,
          type
        })

        localMessage.status = 'sent'
      } else {
        const result = await sendMessage(friendId, content, type)
        localMessage.id = result.id || tempId
        localMessage.status = 'sent'
      }

      storageManager.addMessageToCache(friendId, localMessage)
      return localMessage
    } catch (e) {
      localMessage.status = 'error'
      storageManager.addUnsyncedMessage(localMessage)
      return localMessage
    }
  }

  async syncOfflineMessages(): Promise<void> {
    const offlineMessages = storageManager.getOfflineMessages()
    for (const message of offlineMessages) {
      try {
        await this.sendMessageWithSync(message.to_user_id, message.message, message.type)
        storageManager.removeOfflineMessage(message.id)
      } catch (e) {
        console.error('Failed to sync offline message:', e)
      }
    }
  }

  async syncUnsyncedMessages(): Promise<void> {
    const unsynced = storageManager.getUnsyncedMessages()
    for (const message of unsynced) {
      try {
        await this.sendMessageWithSync(message.to_user_id, message.message, message.type)
        storageManager.removeUnsyncedMessage(message.id)
      } catch (e) {
        console.error('Failed to sync unsynced message:', e)
      }
    }
  }

  startSync(friendId: number, callback: (messages: any[]) => void, interval: number = 10000): void {
    this.stopSync()

    this.syncInterval = setInterval(async () => {
      if (!wsManager.connected) {
        const newMessages = await this.syncMessages(friendId)
        if (newMessages.length > 0) {
          callback(newMessages)
        }
      }
    }, interval)
  }

  stopSync(): void {
    if (this.syncInterval) {
      clearInterval(this.syncInterval)
      this.syncInterval = null
    }
  }

  subscribeToMessages(callback: (message: any) => void): void {
    wsManager.onMessage('message', callback)
    wsManager.onMessage('message_delivered', (data) => {
      this.updateMessageStatus(data.message_id, 'delivered')
    })
    wsManager.onMessage('message_read', (data) => {
      this.updateMessageStatus(data.message_id, 'read')
    })
  }

  unsubscribeFromMessages(): void {
    wsManager.offMessage('message', () => {})
    wsManager.offMessage('message_delivered', () => {})
    wsManager.offMessage('message_read', () => {})
  }

  private updateMessageStatus(messageId: number, status: MessageStatus): void {
    const cache = storageManager.get<any>('messages') || {}
    Object.keys(cache).forEach(friendId => {
      const messages = cache[friendId]?.messages || []
      const message = messages.find((m: any) => m.id === messageId)
      if (message) {
        message.status = status
        cache[friendId] = {
          ...cache[friendId],
          messages
        }
      }
    })
    storageManager.set('messages', cache)
  }
}

export const messageSyncManager = new MessageSyncManager()