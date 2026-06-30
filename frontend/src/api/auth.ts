import type { LoginData, LoginForm, RegisterForm } from '../types'
import { request } from '../utils/request'

export function login(form: LoginForm): Promise<LoginData> {
  return new Promise((resolve, reject) => {
    request('/api/auth/login', 'POST', form)
      .then(response => {
        resolve(response.data as LoginData)
      })
      .catch(error => {
        reject(error)
      })
  })
}

export function register(form: RegisterForm): Promise<LoginData> {
  return new Promise((resolve, reject) => {
    request('/api/auth/register', 'POST', form)
      .then(response => {
        resolve(response.data as LoginData)
      })
      .catch(error => {
        reject(error)
      })
  })
}
