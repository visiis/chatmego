import { NestFactory } from '@nestjs/core'
import { AppModule } from './app.module'
import { NestExpressApplication } from '@nestjs/platform-express'

async function bootstrap() {
  const app = await NestFactory.create<NestExpressApplication>(AppModule)
  
  app.enableCors({
    origin: ['https://chatmego.com', 'https://m.chatmego.com', 'http://localhost:8080', 'http://localhost:5173'],
    credentials: true
  })
  
  await app.listen(process.env.PORT || 6001, process.env.HOST || '0.0.0.0')
  console.log(`Realtime service running on http://${process.env.HOST || '0.0.0.0'}:${process.env.PORT || 6001}`)
}

bootstrap()