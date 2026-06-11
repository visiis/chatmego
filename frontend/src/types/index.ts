export interface User {
  id: number
  phone: string
  nickname: string
  avatar: string
  gender: number
  birthday: string
  bio: string
  love_declaration: string
  location: string
  height: number
  weight: number
  zodiac: string
  marital_status: string
  personality: string
  hobbies: string
  looking_for: string
  ideal_partner: string
  is_vip: number
  vip_level: number
  vip_expire_at: string
  points: {
    balance: number
    total_earned: number
    total_spent: number
  }
  photos: Photo[]
}

export interface Photo {
  id: number
  url: string
  blur_url: string
  is_main: number
  is_premium: number
  points_price: number
  is_unlocked?: boolean
}

export interface Message {
  id: string
  from: number
  to: number
  content: string
  type: 'text' | 'image' | 'voice' | 'gift'
  created_at: string
  is_read: boolean
  ext?: {
    gift_id?: number
    gift_points?: number
  }
}

export interface Gift {
  id: number
  name: string
  icon: string
  animation_url: string
  points: number
  category_id: number
}

export interface GiftCategory {
  id: number
  name: string
  gifts: Gift[]
}

export interface Match {
  id: number
  user: User
  matched_at: string
  last_message?: Message
}

export interface LikeRecord {
  id: number
  user: User
  created_at: string
}

export interface MembershipLevel {
  id: number
  name: string
  name_en: string
  level: number
  price_monthly: number
  price_quarterly: number
  price_yearly: number
  daily_likes: number
  daily_super_likes: number
  daily_points: number
  can_see_who_liked: boolean
  can_hide_profile: boolean
  can_recall_message: boolean
  priority_in_search: boolean
  badge_url: string
  avatar_frame_url: string
}

export interface SystemConfig {
  site_name: string
  site_logo: string
  default_language: string
  signup_points: number
  daily_signin_points: number
  gift_convert_rate: number
  photo_sale_enabled: boolean
  photo_sale_min_price: number
  photo_sale_max_price: number
}

export interface ApiResponse<T = any> {
  code: number
  message: string
  data: T
}

export interface LoginResponse {
  user: User
  im: {
    account: string
    token: string
  }
}
