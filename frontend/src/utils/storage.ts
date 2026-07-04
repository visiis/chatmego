export interface CacheItem<T> {
  data: T
  timestamp: number
  ttl?: number
}

export interface MessageCache {
  [friendId: string]: {
    messages: any[]
    lastMessageId: number
    lastSyncTime: number
  }
}

class StorageManager {
  private readonly PREFIX = 'chatmego_'

  get<T>(key: string): T | null {
    try {
      const value = uni.getStorageSync(this.PREFIX + key)
      if (value) {
        return JSON.parse(value)
      }
      return null
    } catch (e) {
      console.error('Storage get error:', e)
      return null
    }
  }

  set<T>(key: string, value: T, ttl?: number): void {
    try {
      const item: CacheItem<T> = {
        data: value,
        timestamp: Date.now(),
        ttl
      }
      uni.setStorageSync(this.PREFIX + key, JSON.stringify(item))
    } catch (e) {
      console.error('Storage set error:', e)
    }
  }

  getWithCache<T>(key: string, fetcher: () => Promise<T>, ttl: number = 60000): Promise<T> {
    return new Promise((resolve, reject) => {
      const cached = this.get<CacheItem<T>>(key)
      if (cached && (!cached.ttl || Date.now() - cached.timestamp < cached.ttl)) {
        resolve(cached.data)
        return
      }

      fetcher().then(data => {
        this.set(key, data, ttl)
        resolve(data)
      }).catch(reject)
    })
  }

  remove(key: string): void {
    try {
      uni.removeStorageSync(this.PREFIX + key)
    } catch (e) {
      console.error('Storage remove error:', e)
    }
  }

  clear(): void {
    try {
      uni.clearStorageSync()
    } catch (e) {
      console.error('Storage clear error:', e)
    }
  }

  getMessageCache(friendId: number): any[] {
    const cache = this.get<MessageCache>('messages') || {}
    return cache[friendId]?.messages || []
  }

  saveMessageCache(friendId: number, messages: any[]): void {
    const cache = this.get<MessageCache>('messages') || {}
    const lastMessageId = messages.length > 0 
      ? Math.max(...messages.map(m => m.id || 0)) 
      : cache[friendId]?.lastMessageId || 0
    
    cache[friendId] = {
      messages,
      lastMessageId,
      lastSyncTime: Date.now()
    }
    
    this.set('messages', cache)
  }

  getLastMessageId(friendId: number): number {
    const cache = this.get<MessageCache>('messages') || {}
    return cache[friendId]?.lastMessageId || 0
  }

  addMessageToCache(friendId: number, message: any): void {
    const cache = this.get<MessageCache>('messages') || {}
    const existing = cache[friendId]?.messages || []
    
    const exists = existing.some(m => m.id === message.id)
    if (!exists) {
      existing.push(message)
      
      cache[friendId] = {
        messages: existing,
        lastMessageId: Math.max(cache[friendId]?.lastMessageId || 0, message.id || 0),
        lastSyncTime: Date.now()
      }
      
      this.set('messages', cache)
    }
  }

  getUnsyncedMessages(): any[] {
    const unsynced = this.get<any[]>('unsynced_messages') || []
    return unsynced
  }

  addUnsyncedMessage(message: any): void {
    const unsynced = this.get<any[]>('unsynced_messages') || []
    unsynced.push(message)
    this.set('unsynced_messages', unsynced)
  }

  removeUnsyncedMessage(messageId: number): void {
    const unsynced = this.get<any[]>('unsynced_messages') || []
    const filtered = unsynced.filter(m => m.id !== messageId)
    this.set('unsynced_messages', filtered)
  }

  clearUnsyncedMessages(): void {
    this.remove('unsynced_messages')
  }

  getOfflineMessages(): any[] {
    return this.get<any[]>('offline_messages') || []
  }

  saveOfflineMessage(message: any): void {
    const offline = this.get<any[]>('offline_messages') || []
    offline.push(message)
    this.set('offline_messages', offline)
  }

  removeOfflineMessage(messageId: number): void {
    const offline = this.get<any[]>('offline_messages') || []
    const filtered = offline.filter(m => m.id !== messageId)
    this.set('offline_messages', filtered)
  }
}

export const storageManager = new StorageManager()