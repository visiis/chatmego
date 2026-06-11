import zhCN from '@/locales/zh-CN.json'
import zhTW from '@/locales/zh-TW.json'
import en from '@/locales/en.json'

export type Locale = 'zh-CN' | 'zh-TW' | 'en'

export interface I18nMessages {
  common: Record<string, string>
  auth: Record<string, string>
  discover: Record<string, string>
  chats: Record<string, string>
  contacts: Record<string, string>
  profile: Record<string, string>
  settings: Record<string, string>
  membership: Record<string, string>
}

const messages: Record<Locale, I18nMessages> = {
  'zh-CN': zhCN,
  'zh-TW': zhTW,
  'en': en
}

let currentLocale: Locale = 'zh-TW'

export function setLocale(locale: Locale) {
  currentLocale = locale
  uni.setStorageSync('locale', locale)
}

export function getLocale(): Locale {
  const stored = uni.getStorageSync('locale') as Locale
  if (stored && messages[stored]) {
    return stored
  }
  return currentLocale
}

export function t(key: string): string {
  const locale = getLocale()
  const keys = key.split('.')
  
  let result: any = messages[locale]
  
  for (const k of keys) {
    if (result && typeof result === 'object' && k in result) {
      result = result[k]
    } else {
      return key
    }
  }
  
  return typeof result === 'string' ? result : key
}

export function getAvailableLocales(): { code: Locale; label: string }[] {
  return [
    { code: 'zh-TW', label: '繁體中文' },
    { code: 'zh-CN', label: '简体中文' },
    { code: 'en', label: 'English' }
  ]
}

// 初始化时读取存储的语言设置
currentLocale = getLocale()