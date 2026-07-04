export interface WebSocketMessage {
  type: string
  data: any
}

export interface WebSocketConfig {
  url: string
  reconnectInterval?: number
  maxReconnectAttempts?: number
}

class WebSocketManager {
  private socketTask: UniApp.SocketTask | null = null
  private config: WebSocketConfig
  private reconnectAttempts = 0
  private isConnected = false
  private messageHandlers: Map<string, ((data: any) => void)[]> = new Map()
  private connectionHandlers: ((connected: boolean) => void)[] = []

  constructor(config: WebSocketConfig) {
    this.config = {
      url: config.url,
      reconnectInterval: config.reconnectInterval || 5000,
      maxReconnectAttempts: config.maxReconnectAttempts || -1
    }
  }

  connect() {
    if (this.isConnected || this.socketTask) {
      return
    }

    const token = uni.getStorageSync('token')
    const url = this.config.url + (this.config.url.includes('?') ? '&' : '?') + 'token=' + encodeURIComponent(token)

    this.socketTask = uni.connectSocket({
      url: url,
      success: () => {
        this.reconnectAttempts = 0
      },
      fail: () => {
        this.handleDisconnect()
      }
    })

    this.socketTask.onOpen(() => {
      this.isConnected = true
      this.notifyConnectionHandlers(true)
    })

    this.socketTask.onMessage((res) => {
      this.handleMessage(res.data)
    })

    this.socketTask.onClose(() => {
      this.handleDisconnect()
    })

    this.socketTask.onError(() => {
      this.handleDisconnect()
    })
  }

  private handleDisconnect() {
    this.isConnected = false
    this.socketTask = null
    this.notifyConnectionHandlers(false)

    if (this.config.maxReconnectAttempts === -1 || 
        this.reconnectAttempts < this.config.maxReconnectAttempts) {
      this.reconnectAttempts++
      setTimeout(() => {
        this.connect()
      }, this.config.reconnectInterval * this.reconnectAttempts)
    }
  }

  private handleMessage(data: string) {
    try {
      const message: WebSocketMessage = JSON.parse(data)
      const handlers = this.messageHandlers.get(message.type) || []
      handlers.forEach(handler => handler(message.data))
    } catch (e) {
      console.error('WebSocket message parse error:', e)
    }
  }

  disconnect() {
    if (this.socketTask) {
      this.socketTask.close()
      this.socketTask = null
    }
    this.isConnected = false
    this.reconnectAttempts = this.config.maxReconnectAttempts || 0
  }

  send(type: string, data: any) {
    if (!this.isConnected || !this.socketTask) {
      return Promise.reject(new Error('WebSocket not connected'))
    }

    return new Promise<void>((resolve, reject) => {
      this.socketTask!.send({
        data: JSON.stringify({ type, data }),
        success: resolve,
        fail: reject
      })
    })
  }

  onMessage(type: string, handler: (data: any) => void) {
    const handlers = this.messageHandlers.get(type) || []
    handlers.push(handler)
    this.messageHandlers.set(type, handlers)
  }

  offMessage(type: string, handler: (data: any) => void) {
    const handlers = this.messageHandlers.get(type) || []
    const filtered = handlers.filter(h => h !== handler)
    this.messageHandlers.set(type, filtered)
  }

  onConnection(handler: (connected: boolean) => void) {
    this.connectionHandlers.push(handler)
  }

  offConnection(handler: (connected: boolean) => void) {
    this.connectionHandlers = this.connectionHandlers.filter(h => h !== handler)
  }

  private notifyConnectionHandlers(connected: boolean) {
    this.connectionHandlers.forEach(handler => handler(connected))
  }

  get connected() {
    return this.isConnected
  }
}

const wsUrl = process.env.NODE_ENV === 'production' 
  ? 'wss://chatmego.com/socket.io/?EIO=4&transport=websocket' 
  : 'ws://localhost:6001/socket.io/?EIO=4&transport=websocket'

export const wsManager = new WebSocketManager({
  url: wsUrl,
  reconnectInterval: 5000,
  maxReconnectAttempts: -1
})