import { Injectable } from '@nestjs/common'
import { DatabaseService } from '../database/database.service'
import { JwtService } from '@nestjs/jwt'

export interface Message {
  id: number
  from_user_id: number
  to_user_id: number
  message: string
  type: string
  is_read: boolean
  created_at: string
  attachment_url?: string
}

@Injectable()
export class ChatService {
  public userConnections: Map<number, Set<string>> = new Map()

  constructor(
    private readonly databaseService: DatabaseService,
    private readonly jwtService: JwtService
  ) {}

  async verifyToken(token: string): Promise<number | null> {
    try {
      const decoded = this.jwtService.verify(token)
      return decoded.sub || decoded.id || null
    } catch {
      return null
    }
  }

  registerConnection(userId: number, socketId: string): void {
    if (!this.userConnections.has(userId)) {
      this.userConnections.set(userId, new Set())
    }
    this.userConnections.get(userId)!.add(socketId)
  }

  removeConnection(userId: number, socketId: string): void {
    const connections = this.userConnections.get(userId)
    if (connections) {
      connections.delete(socketId)
      if (connections.size === 0) {
        this.userConnections.delete(userId)
      }
    }
  }

  getUserSocketIds(userId: number): string[] {
    return Array.from(this.userConnections.get(userId) || [])
  }

  formatMessage(message: any): Message {
    return {
      id: message.id,
      from_user_id: message.from_user_id,
      to_user_id: message.to_user_id,
      message: message.message,
      type: message.type || 'text',
      is_read: message.is_read || false,
      created_at: message.created_at ? new Date(message.created_at).toISOString() : new Date().toISOString(),
      attachment_url: message.attachment_url
    }
  }

  async getUnreadMessages(userId: number, targetUserId: number, lastMessageId: number): Promise<Message[]> {
    const messages = await this.databaseService.getMessages(userId, targetUserId, lastMessageId)
    return messages.map(this.formatMessage.bind(this))
  }

  async markMessagesAsRead(userId: number, targetUserId: number, lastMessageId: number): Promise<void> {
    await this.databaseService.markAsRead(userId, targetUserId, lastMessageId)
  }

  async getUserInfo(userId: number): Promise<any> {
    return this.databaseService.getUserById(userId)
  }

  isOnline(userId: number): boolean {
    return this.userConnections.has(userId)
  }
}