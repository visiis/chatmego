import { Module, Global } from '@nestjs/common'
import { JwtModule } from '@nestjs/jwt'

@Global()
@Module({
  imports: [
    JwtModule.register({
      secret: process.env.JWT_SECRET || 'chatmego_secret',
      signOptions: { expiresIn: '24h' }
    })
  ],
  exports: [JwtModule]
})
export class JwtConfigModule {}