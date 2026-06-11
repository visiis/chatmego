<script setup lang="ts">
import { onLaunch, onShow, onHide } from '@dcloudio/uni-app'
import { useUserStore } from '@/stores/user'

onLaunch(() => {
  const userStore = useUserStore()
  userStore.loadUserInfo()
})

onShow(() => {
})

onHide(() => {
})
</script>

<style lang="scss">
@import './styles/global.scss';
</style>
