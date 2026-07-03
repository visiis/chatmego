import { createSSRApp } from 'vue'
import { createPinia } from 'pinia'
import uviewPlus from 'uview-plus'
import Vuex from 'vuex'
import App from './App.vue'
import store from './store'

export function createApp() {
  const app = createSSRApp(App)
  const pinia = createPinia()
  app.use(pinia)
  app.use(uviewPlus)
  app.use(Vuex)
  app.use(store)
  return { app }
}
