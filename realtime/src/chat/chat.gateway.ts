import { WebSocketGateway, WebSocketServer, SubscribeMessage, OnGatewayConnection, OnGatewayDisconnect, MessageBody, ConnectedSocket } from '@nestjs/websockets'
import { Server, Socket } from 'socket.io'
import { ChatService } from './chat.service'

interface Connection {
  userId: number
  socketId: string
}

@WebSocketGateway({
  cors: {
    origin: ['https://chatmego.com', 'https://m.chatmego.com', 'http://localhost:8080', 'http://localhost:5173'],
    credentials: true
  }
})
export class ChatGateway implements OnGatewayConnection, OnGatewayDisconnect {
  @WebSocketServer()
  server: Server

  private connections: Map<string, Connection> = new Map()

  constructor(private readonly chatService: ChatService) {}

  async handleConnection(client: Socket) {
    const token = client.handshake.auth.token || client.handshake.query.token
    
    if (!token) {
      client.disconnect(true)
      return
    }

    const userId = await this.chatService.verifyToken(token)
    
    if (!userId) {
      client.disconnect(true)
      return
    }

    this.connections.set(client.id, { userId, socketId: client.id })
    this.chatService.registerConnection(userId, client.id)
    
    console.log(`User ${userId} connected, socket: ${client.id}`)
    
    client.emit('connected', { success: true, userId })
  }

  handleDisconnect(client: Socket) {
    const connection = this.connections.get(client.id)
    
    if (connection) {
      this.chatService.removeConnection(connection.userId, client.id)
      this.connections.delete(client.id)
      console.log(`User ${connection.userId} disconnected, socket: ${client.id}`)
    }
  }

  @SubscribeMessage('join')
  handleJoin(@MessageBody() data: { roomId: number }, @ConnectedSocket() client: Socket) {
    const connection = this.connections.get(client.id)
    
    if (!connection) {
      return { success: false, message: '未授权' }
    }

    const roomName = `chat:${data.roomId}`
    client.join(roomName)
    
    console.log(`User ${connection.userId} joined room: ${roomName}`)
    
    return { success: true, room: roomName }
  }

  @SubscribeMessage('leave')
  handleLeave(@MessageBody() data: { roomId: number }, @ConnectedSocket() client: Socket) {
    const connection = this.connections.get(client.id)
    
    if (!connection) {
      return { success: false, message: '未授权' }
    }

    const roomName = `chat:${data.roomId}`
    client.leave(roomName)
    
    console.log(`User ${connection.userId} left room: ${roomName}`)
    
    return { success: true }
  }

  @SubscribeMessage('message')
  async handleMessage(@MessageBody() data: any, @ConnectedSocket() client: Socket) {
    const connection = this.connections.get(client.id)
    
    if (!connection) {
      return { success: false, message: '未授权' }
    }

    const { to_user_id, message, type } = data
    
    if (!to_user_id || !message) {
      return { success: false, message: '参数错误' }
    }

    const formattedMessage = this.chatService.formatMessage({
      id: Date.now(),
      from_user_id: connection.userId,
      to_user_id,
      message,
      type: type || 'text',
      is_read: false,
      created_at: new Date().toISOString()
    })

    const targetSocketIds = this.chatService.getUserSocketIds(to_user_id)
    
    if (targetSocketIds.length > 0) {
      this.server.to(targetSocketIds).emit('message', formattedMessage)
      console.log(`Message sent from ${connection.userId} to ${to_user_id}, online`)
    }
    
    const roomName = `chat:${to_user_id}`
    this.server.to(roomName).emit('message', formattedMessage)
    
    return {
      success: true,
      message: formattedMessage,
      delivered: targetSocketIds.length > 0
    }
  }

  @SubscribeMessage('sync')
  async handleSync(@MessageBody() data: { targetUserId: number; lastMessageId: number }, @ConnectedSocket() client: Socket) {
    const connection = this.connections.get(client.id)
    
    if (!connection) {
      return { success: false, message: '未授权' }
    }

    const { targetUserId, lastMessageId } = data
    
    const messages = await this.chatService.getUnreadMessages(connection.userId, targetUserId, lastMessageId)
    
    if (messages.length > 0) {
      await this.chatService.markMessagesAsRead(connection.userId, targetUserId, lastMessageId)
    }
    
    return {
      success: true,
      messages
    }
  }

  @SubscribeMessage('markRead')
  async handleMarkRead(@MessageBody() data: { targetUserId: number; lastMessageId: number }, @ConnectedSocket() client: Socket) {
    const connection = this.connections.get(client.id)
    
    if (!connection) {
      return { success: false, message: '未授权' }
    }

    const { targetUserId, lastMessageId } = data
    
    await this.chatService.markMessagesAsRead(connection.userId, targetUserId, lastMessageId)
    
    const targetSocketIds = this.chatService.getUserSocketIds(targetUserId)
    
    if (targetSocketIds.length > 0) {
      this.server.to(targetSocketIds).emit('message_read', {
        userId: connection.userId,
        targetUserId,
        lastMessageId
      })
    }
    
    return { success: true }
  }

  @SubscribeMessage('status')
  handleStatus(@ConnectedSocket() client: Socket) {
    const connection = this.connections.get(client.id)
    
    if (!connection) {
      return { success: false, message: '未授权' }
    }

    const onlineUsers: { userId: number; online: boolean }[] = []
    this.chatService.userConnections.forEach((_, userId) => {
      onlineUsers.push({ userId, online: true })
    })
    
    return {
      success: true,
      userId: connection.userId,
      onlineUsers
    }
  }

  @SubscribeMessage('ping')
  handlePing(@ConnectedSocket() client: Socket) {
    return { pong: Date.now() }
  }

  async broadcastMessage(message: any): Promise<void> {
    const targetSocketIds = this.chatService.getUserSocketIds(message.to_user_id)
    
    if (targetSocketIds.length > 0) {
      const formattedMessage = this.chatService.formatMessage(message)
      this.server.to(targetSocketIds).emit('message', formattedMessage)
    }
    
    const roomName = `chat:${message.to_user_id}`
    const formattedMessage = this.chatService.formatMessage(message)
    this.server.to(roomName).emit('message', formattedMessage)
  }
}