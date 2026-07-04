import { Injectable } from '@nestjs/common'
import * as mysql from 'mysql2/promise'

@Injectable()
export class DatabaseService {
  private connection: mysql.Connection | null = null

  async getConnection(): Promise<mysql.Connection> {
    if (!this.connection) {
      this.connection = await mysql.createConnection({
        host: process.env.DB_HOST,
        port: parseInt(process.env.DB_PORT || '3306'),
        database: process.env.DB_DATABASE,
        user: process.env.DB_USERNAME,
        password: process.env.DB_PASSWORD
      })
      console.log('MySQL connection established')
    }
    return this.connection
  }

  async getMessages(userId: number, targetUserId: number, lastMessageId: number = 0): Promise<any[]> {
    const conn = await this.getConnection()
    const [rows] = await conn.execute(
      `SELECT * FROM messages WHERE 
        (from_user_id = ? AND to_user_id = ?) OR (from_user_id = ? AND to_user_id = ?)
        AND id > ?
        ORDER BY created_at ASC`,
      [userId, targetUserId, targetUserId, userId, lastMessageId]
    )
    return rows as any[]
  }

  async markAsRead(userId: number, targetUserId: number, lastMessageId: number = 0): Promise<void> {
    const conn = await this.getConnection()
    await conn.execute(
      `UPDATE messages SET is_read = 1, read_at = NOW() 
        WHERE from_user_id = ? AND to_user_id = ? AND is_read = 0 AND id > ?`,
      [targetUserId, userId, lastMessageId]
    )
  }

  async getUserById(userId: number): Promise<any | null> {
    const conn = await this.getConnection()
    const [rows] = await conn.execute(
      `SELECT id, name, avatar_url FROM users WHERE id = ?`,
      [userId]
    )
    const users = rows as any[]
    return users.length > 0 ? users[0] : null
  }
}