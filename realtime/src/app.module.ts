import { Module } from '@nestjs/common'
import { ChatGateway } from './chat/chat.gateway'
import { ChatService } from './chat/chat.service'
import { DatabaseModule } from './database/database.module'
import { JwtConfigModule } from './jwt/jwt.module'

@Module({
  imports: [DatabaseModule, JwtConfigModule],
  providers: [ChatGateway, ChatService]
})
export class AppModule {}