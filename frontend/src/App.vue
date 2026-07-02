<script setup lang="ts">
import { onLaunch, onShow, onHide } from '@dcloudio/uni-app'
import { isLoggedIn } from './utils/auth'

const publicPages = ['/pages/auth/login', '/pages/auth/register', '/pages/auth/success']

function checkLogin() {
  const pages = getCurrentPages()
  if (pages.length === 0) return
  
  const currentPage = pages[pages.length - 1]
  const route = '/' + currentPage.route
  
  if (publicPages.includes(route)) return
  
  if (!isLoggedIn()) {
    uni.redirectTo({ url: '/pages/auth/login' })
  }
}

onLaunch(() => {
  checkLogin()
})

onShow(() => {
  checkLogin()
})

onHide(() => {
})
</script>

<style lang="scss">
@import './styles/global.scss';
@import '@dcloudio/uni-ui/lib/uni-icons/uniicons.css';
@import 'font-awesome/css/font-awesome.min.css';

@font-face {
  font-family: uniicons;
  src: url('/static/fonts/uniicons.ttf');
}

page {
  width: 100%;
  max-width: 100%;
  overflow-x: hidden;
}
</style>