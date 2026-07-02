<template>
  <view class="membership-container">
    <scroll-view class="content" scroll-y>
      <view class="current-membership-card" v-if="membershipInfo">
        <view class="card-header">
          <text class="card-title">{{ __('messages.membership.current_membership') }}</text>
        </view>
        
        <view class="membership-content" v-if="membershipInfo.has_membership && membershipInfo.plan">
          <view class="membership-badge-wrapper">
            <view class="membership-badge" :style="{ backgroundColor: membershipInfo.plan.badge_color }">
              <FontAwesome :name="getPlanIcon(membershipInfo.plan!.code)" size="20px" color="#fff" />
              <text class="badge-text">{{ membershipInfo.plan.name }}</text>
            </view>
          </view>
          <text class="membership-expire">有效期至：{{ formatDateTime(membershipInfo.ends_at) }}</text>
          <text class="membership-days" v-if="membershipInfo.days_remaining !== null">剩余 {{ membershipInfo.days_remaining }} 天</text>
          <view class="cancel-btn-wrapper">
            <view class="cancel-btn" @click="confirmCancel">
              <text class="cancel-btn-text">取消自动续费</text>
            </view>
          </view>
        </view>
        
        <view class="membership-content" v-else>
          <view class="membership-badge-wrapper">
            <view class="membership-badge basic">
              <text class="badge-text">{{ __('messages.membership.basic_member') }}</text>
            </view>
          </view>
          <text class="membership-no">您还不是会员，开通会员享受更多特权</text>
          <view class="view-plans-btn" @click="scrollToPlans">
            <text class="view-plans-text">查看可用会员计划</text>
          </view>
        </view>
      </view>
      
      <view class="points-card">
        <view class="points-item">
          <FontAwesome name="coins" size="28px" color="#ffd700" />
          <text class="points-label">{{ __('messages.nav.points') }}</text>
          <text class="points-value">{{ userCoins }}</text>
        </view>
        <view class="points-divider"></view>
        <view class="points-item">
          <FontAwesome name="star" size="28px" color="#ff6b9d" />
          <text class="points-label">{{ __('messages.nav.activity_points') }}</text>
          <text class="points-value">{{ userPoints }}</text>
        </view>
      </view>
      
      <view class="convert-card">
        <view class="card-header">
          <FontAwesome name="exchange" size="24px" color="#ff6b9d" />
          <text class="card-title">{{ __('messages.membership.convert_points_to_coins') }}</text>
        </view>
        <view class="convert-body">
          <text class="convert-desc">100 {{ __('messages.nav.activity_points') }} = 1 <FontAwesome name="coins" size="18px" color="#ffd700" /></text>
          <view class="input-group">
            <text class="input-label">{{ __('messages.membership.convert_amount') }}</text>
            <input 
              class="convert-input" 
              type="number" 
              v-model="convertPointsAmount"
              placeholder="{{ __('messages.membership.enter_activity_points') }}"
              @input="onConvertInput"
            />
            <text class="convert-available">{{ __('messages.nav.activity_points') }}: {{ userPoints }}</text>
          </view>
          <view class="convert-preview" v-if="coinsPreview > 0">
            <FontAwesome name="gift" size="20px" color="#2ed573" />
            <text class="preview-text">{{ __('messages.membership.will_get_coins') }}: <text class="preview-value">{{ coinsPreview }}</text> <FontAwesome name="coins" size="18px" color="#ffd700" /></text>
          </view>
          <view class="convert-btn" @click="doConvert">
            <text class="convert-btn-text">{{ __('messages.membership.convert_now') }}</text>
          </view>
        </view>
      </view>
      
      <view class="plans-section" id="plans">
        <view class="section-header">
          <text class="section-title">{{ __('messages.membership.available_plans') }}</text>
        </view>
        
        <view class="plans-list">
          <view 
            class="plan-card" 
            v-for="plan in availablePlans" 
            :key="plan.id"
            :style="{ borderTopColor: plan.badge_color }"
          >
            <view class="plan-badge-wrapper">
              <view class="plan-badge" :style="{ backgroundColor: plan.badge_color }">
                <FontAwesome :name="getPlanIcon(plan.code)" size="20px" color="#fff" />
                <text class="badge-text">{{ plan.name }}</text>
              </view>
            </view>
            
            <view class="plan-price">
              <FontAwesome name="coins" size="28px" color="#ffd700" />
              <text class="price-value">{{ plan.price }}</text>
              <text class="price-unit">{{ __('messages.nav.points') }}</text>
              <text class="price-yuan">≈ ¥{{ plan.price_yuan }}</text>
            </view>
            
            <view class="plan-privileges">
              <view class="privilege-item">
                <FontAwesome name="calendar-alt" size="18px" color="#ff6b9d" />
                <text class="privilege-text">{{ __('messages.membership.days') }}: {{ plan.duration_days }}</text>
              </view>
              <view class="privilege-item" v-for="(privilege, idx) in plan.privileges" :key="idx">
                <FontAwesome name="check-circle" size="18px" color="#2ed573" />
                <text class="privilege-text">{{ privilege }}</text>
              </view>
            </view>
            
            <view class="current-using" v-if="membershipInfo?.has_membership && membershipInfo?.plan?.code === plan.code">
              <text class="current-using-text">💡 当前正在使用此会员</text>
            </view>
            
            <view class="purchase-form">
              <view class="form-group">
                <text class="form-label">{{ __('messages.membership.purchase_duration') }}</text>
                <picker 
                  mode="selector" 
                  :range="purchaseOptions" 
                  :range-key="'label'"
                  :value="getSelectedMonthIndex(plan.id)"
                  @change="(e: any) => onMonthChange(e, plan.id)"
                >
                  <view class="form-select">
                    <text class="select-text">{{ getSelectedMonthLabel(plan.id) }}</text>
                    <FontAwesome name="chevron-down" size="18px" color="#999" />
                  </view>
                </picker>
              </view>
              
              <view class="price-info">
                <text class="price-label">{{ __('messages.membership.required_coins') }}:</text>
                <FontAwesome name="coins" size="24px" color="#ffd700" />
                <text class="price-amount">{{ getTotalPrice(plan) }}</text>
              </view>
              
              <view 
                class="purchase-btn" 
                :class="{ disabled: userCoins < getTotalPrice(plan) }"
                @click="doPurchase(plan)"
              >
                <text class="purchase-btn-text">
                  {{ userCoins < getTotalPrice(plan) ? __('messages.membership.insufficient_coins') : __('messages.membership.buy_now') }}
                </text>
              </view>
            </view>
          </view>
        </view>
      </view>
      
      <view class="history-section">
        <view class="section-header">
          <FontAwesome name="list-alt" size="24px" color="#ff6b9d" />
          <text class="section-title">{{ __('messages.membership.subscription_queue') }}</text>
        </view>
        
        <view class="history-list" v-if="subscriptionHistory.length > 0">
          <view class="history-item" v-for="history in subscriptionHistory" :key="history.id">
            <view class="history-plan" :style="{ backgroundColor: history.plan_badge_color }">
              <FontAwesome :name="getPlanIcon(history.plan_code)" size="18px" color="#fff" />
              <text class="history-plan-text">{{ history.plan_name }}</text>
            </view>
            <view class="history-info">
              <text class="history-date">{{ formatDateTime(history.starts_at) }} - {{ formatDateTime(history.ends_at) }}</text>
              <view class="history-status" :class="history.status">
                <text class="status-text">{{ getStatusText(history.status) }}</text>
              </view>
              <view class="history-price">
                <FontAwesome name="coins" size="20px" color="#ffd700" />
                <text>{{ history.price_paid }}</text>
              </view>
            </view>
          </view>
        </view>
        
        <view class="empty-history" v-else>
          <text class="empty-text">暂无订阅记录</text>
        </view>
        
        <view class="stacking-info">
          <FontAwesome name="info-circle" size="20px" color="#0c5460" />
          <text class="stacking-text"><strong>{{ __('messages.membership.stacking_info') }}</strong></text>
        </view>
      </view>
      
      <view class="bottom-spacing"></view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { 
  getMembershipInfo, 
  purchaseMembership, 
  convertPoints, 
  cancelMembership,
  type MembershipInfo as MembershipInfoType,
  type MembershipPlan,
  type SubscriptionHistory,
  type MembershipResponse
} from '../../api/membership'

const membershipInfo = ref<MembershipInfoType | null>(null)
const availablePlans = ref<MembershipPlan[]>([])
const subscriptionHistory = ref<SubscriptionHistory[]>([])
const userCoins = ref(0)
const userPoints = ref(0)

const convertPointsAmount = ref('')
const coinsPreview = ref(0)

const purchaseMonths = reactive<Record<number, number>>({})

const purchaseOptions = [
  { value: 1, label: '1 個月' },
  { value: 3, label: '3 個月' },
  { value: 6, label: '6 個月' },
  { value: 12, label: '12 個月' },
]

onMounted(() => {
  loadMembershipInfo()
})

async function loadMembershipInfo() {
  try {
    const data: MembershipResponse = await getMembershipInfo()
    membershipInfo.value = data.membership_info
    availablePlans.value = data.available_plans
    subscriptionHistory.value = data.subscription_history
    userCoins.value = data.user.coins
    userPoints.value = data.user.points
    
    availablePlans.value.forEach(plan => {
      if (!purchaseMonths[plan.id]) {
        purchaseMonths[plan.id] = 1
      }
    })
  } catch (error) {
    console.error('加载会员信息失败:', error)
    uni.showToast({ title: '載入失敗', icon: 'none' })
  }
}

function formatDateTime(dateStr?: string): string {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const hour = String(date.getHours()).padStart(2, '0')
  const minute = String(date.getMinutes()).padStart(2, '0')
  return `${year}-${month}-${day} ${hour}:${minute}`
}

function onConvertInput() {
  const points = parseInt(convertPointsAmount.value) || 0
  coinsPreview.value = Math.floor(points / 100)
}

async function doConvert() {
  const points = parseInt(convertPointsAmount.value) || 0
  
  if (points < 100) {
    uni.showToast({ title: '最少需要 100 活躍度', icon: 'none' })
    return
  }
  
  if (points % 100 !== 0) {
    uni.showToast({ title: '兌換的活躍度必須是 100 的倍數', icon: 'none' })
    return
  }
  
  if (points > userPoints.value) {
    uni.showToast({ title: '活躍度不足', icon: 'none' })
    return
  }
  
  try {
    await convertPoints(points)
    uni.showToast({ title: '兌換成功', icon: 'success' })
    convertPointsAmount.value = ''
    coinsPreview.value = 0
    loadMembershipInfo()
  } catch (error: any) {
    uni.showToast({ title: error?.message || '兌換失敗', icon: 'none' })
  }
}

function getSelectedMonthIndex(planId: number): number {
  const months = purchaseMonths[planId] || 1
  return purchaseOptions.findIndex(o => o.value === months)
}

function getSelectedMonthLabel(planId: number): string {
  const months = purchaseMonths[planId] || 1
  const option = purchaseOptions.find(o => o.value === months)
  return option?.label || '1 个月'
}

function onMonthChange(e: any, planId: number) {
  purchaseMonths[planId] = purchaseOptions[e.detail.value].value
}

function getTotalPrice(plan: MembershipPlan): number {
  const months = purchaseMonths[plan.id] || 1
  return plan.price * months
}

async function doPurchase(plan: MembershipPlan) {
  const months = purchaseMonths[plan.id] || 1
  const totalPrice = getTotalPrice(plan)
  
  if (userCoins.value < totalPrice) {
    uni.showToast({ title: '金币不足', icon: 'none' })
    return
  }
  
  uni.showModal({
    title: '確認購買',
    content: `確認購買${plan.name}，${months}個月，需要 ${totalPrice} 金幣？`,
    success: async (res) => {
      if (res.confirm) {
        try {
          await purchaseMembership(plan.id, months)
          uni.showToast({ title: '購買成功', icon: 'success' })
          loadMembershipInfo()
        } catch (error: any) {
          uni.showToast({ title: error?.message || '購買失敗', icon: 'none' })
        }
      }
    }
  })
}

async function confirmCancel() {
  uni.showModal({
    title: '确认取消',
    content: '确定要取消会员自动续费吗？会员权益将持续到当前有效期结束。',
    success: async (res) => {
      if (res.confirm) {
        try {
          await cancelMembership()
          uni.showToast({ title: '已取消自动续费', icon: 'success' })
          loadMembershipInfo()
        } catch (error: any) {
          uni.showToast({ title: error?.message || '取消失败', icon: 'none' })
        }
      }
    }
  })
}

function getStatusText(status: string): string {
  const statusMap: Record<string, string> = {
    'active': '進行中',
    'expired': '已過期',
    'cancelled': '已取消',
  }
  return statusMap[status] || status
}

function getPlanIcon(code: string): string {
  const iconMap: Record<string, string> = {
    'basic': 'credit-card',
    'vip': 'star',
    'svip': 'crown',
  }
  return iconMap[code] || 'star'
}

function scrollToPlans() {
  uni.pageScrollTo({ selector: '#plans', duration: 300 })
}

function goBack() {
  uni.navigateBack()
}

function __ (key: string): string {
  const translations: Record<string, string> = {
    'messages.membership.title': '會員中心',
    'messages.membership.current_membership': '當前會員',
    'messages.membership.plans.silver': '銀卡會員',
    'messages.membership.plans.gold': '金卡會員',
    'messages.membership.plans.diamond': '鑽石會員',
    'messages.membership.expires_at': '有效期至',
    'messages.membership.days_remaining': '剩餘 :days 天',
    'messages.membership.cancel_auto_renewal': '取消自動續費',
    'messages.membership.basic_member': '普通會員',
    'messages.membership.no_membership': '您還不是會員',
    'messages.membership.available_plans': '可用會員計劃',
    'messages.nav.points': '金幣',
    'messages.nav.activity_points': '活躍度',
    'messages.membership.convert_points_to_coins': '活躍度兌換金幣',
    'messages.membership.convert_amount': '兌換數量',
    'messages.membership.enter_activity_points': '請輸入活躍度數量',
    'messages.membership.will_get_coins': '將獲得金幣',
    'messages.membership.convert_now': '立即兌換',
    'messages.membership.days': '天數',
    'messages.membership.purchase_duration': '購買時長',
    'messages.membership.required_coins': '所需金幣',
    'messages.membership.insufficient_coins': '金幣不足',
    'messages.membership.buy_now': '立即購買',
    'messages.membership.subscription_queue': '訂閱歷史隊列',
    'messages.membership.stacking_info': '會員有效期採用疊加模式，購買後有效期將自動延長',
    'messages.membership.currently_using': '當前正在使用',
    'messages.membership.confirm_cancel': '確定要取消會員訂閱嗎？',
    'messages.membership.active': '進行中',
    'messages.membership.expired': '已過期',
    'messages.membership.cancelled': '已取消',
    'messages.membership.no_subscription_records': '暫無訂閱記錄',
    'messages.membership.month': '個月',
    'messages.membership.months': '個月',
  }
  return translations[key] || key
}
</script>

<style lang="scss">
page {
  height: 100%;
  margin: 0;
  padding: 0;
  background: #f5f5f5;
}

.membership-container {
  min-height: 100vh;
  background: #f5f5f5;
}

.content {
  padding: 24rpx;
  box-sizing: border-box;
}

.card-header {
  margin-bottom: 24rpx;
}

.card-title {
  font-size: 32rpx;
  font-weight: 600;
  color: #333;
}

.current-membership-card {
  background: #fff;
  border-radius: 16rpx;
  padding: 32rpx;
  margin-bottom: 24rpx;
  box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.05);
}

.membership-content {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.membership-badge-wrapper {
  margin-bottom: 20rpx;
}

.membership-badge {
  padding: 16rpx 32rpx;
  border-radius: 32rpx;
  
  &.basic {
    background: #6c757d;
  }
}

.badge-text {
  font-size: 28rpx;
  color: #fff;
  font-weight: 600;
}

.membership-expire {
  font-size: 26rpx;
  color: #666;
  margin-bottom: 8rpx;
}

.membership-days {
  font-size: 28rpx;
  color: #ff6b9d;
  font-weight: 600;
  margin-bottom: 20rpx;
}

.membership-no {
  font-size: 26rpx;
  color: #999;
  margin-bottom: 20rpx;
}

.cancel-btn-wrapper {
  width: 100%;
}

.cancel-btn {
  border: 2rpx solid #dc3545;
  border-radius: 32rpx;
  padding: 16rpx;
  text-align: center;
}

.cancel-btn-text {
  font-size: 26rpx;
  color: #dc3545;
}

.view-plans-btn {
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  border-radius: 32rpx;
  padding: 20rpx 48rpx;
}

.view-plans-text {
  font-size: 28rpx;
  color: #fff;
  font-weight: 600;
}

.points-card {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
  margin-bottom: 24rpx;
  display: flex;
  align-items: center;
  box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.05);
}

.points-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.points-label {
  font-size: 24rpx;
  color: #999;
  margin-bottom: 8rpx;
}

.points-value {
  font-size: 36rpx;
  font-weight: bold;
  color: #ff6b9d;
}

.points-divider {
  width: 2rpx;
  height: 60rpx;
  background: #eee;
}

.convert-card {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
  margin-bottom: 24rpx;
  box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.05);
}

.convert-body {
  display: flex;
  flex-direction: column;
}

.convert-desc {
  font-size: 24rpx;
  color: #999;
  margin-bottom: 20rpx;
}

.input-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 16rpx;
}

.input-label {
  font-size: 26rpx;
  color: #333;
  margin-bottom: 12rpx;
}

.convert-input {
  border: 2rpx solid #eee;
  border-radius: 8rpx;
  padding: 20rpx;
  font-size: 28rpx;
  margin-bottom: 8rpx;
}

.convert-available {
  font-size: 22rpx;
  color: #999;
}

.convert-preview {
  margin-bottom: 20rpx;
}

.preview-text {
  font-size: 24rpx;
  color: #2ed573;
}

.preview-value {
  font-weight: bold;
  font-size: 28rpx;
}

.convert-btn {
  background: linear-gradient(135deg, #2ed573 0%, #1e90ff 100%);
  border-radius: 32rpx;
  padding: 24rpx;
  text-align: center;
}

.convert-btn-text {
  font-size: 28rpx;
  color: #fff;
  font-weight: 600;
}

.plans-section {
  margin-bottom: 32rpx;
}

.section-header {
  margin-bottom: 20rpx;
}

.section-title {
  font-size: 32rpx;
  font-weight: 600;
  color: #333;
}

.plans-list {
  display: flex;
  flex-direction: column;
  gap: 20rpx;
}

.plan-card {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
  border-top: 8rpx solid;
  box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.05);
}

.plan-badge-wrapper {
  display: flex;
  justify-content: center;
  margin-bottom: 20rpx;
}

.plan-badge {
  padding: 12rpx 28rpx;
  border-radius: 24rpx;
}

.plan-price {
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 8rpx;
  margin-bottom: 20rpx;
}

.price-value {
  font-size: 40rpx;
  font-weight: bold;
  color: #ff6b9d;
}

.price-unit {
  font-size: 24rpx;
  color: #999;
}

.price-yuan {
  font-size: 22rpx;
  color: #999;
}

.plan-privileges {
  display: flex;
  flex-direction: column;
  gap: 12rpx;
  margin-bottom: 20rpx;
}

.privilege-item {
  display: flex;
  align-items: center;
  gap: 12rpx;
}

.privilege-text {
  font-size: 24rpx;
  color: #666;
}

.current-using {
  background: #d1ecf1;
  border-radius: 8rpx;
  padding: 12rpx;
  margin-bottom: 20rpx;
}

.current-using-text {
  font-size: 22rpx;
  color: #0c5460;
}

.purchase-form {
  display: flex;
  flex-direction: column;
  gap: 16rpx;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 12rpx;
}

.form-label {
  font-size: 24rpx;
  color: #999;
}

.form-select {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border: 2rpx solid #eee;
  border-radius: 8rpx;
  padding: 20rpx;
}

.select-text {
  font-size: 26rpx;
  color: #333;
}

.price-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.price-label {
  font-size: 24rpx;
  color: #999;
}

.price-amount {
  font-size: 28rpx;
  font-weight: bold;
  color: #ff6b9d;
}

.purchase-btn {
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  border-radius: 32rpx;
  padding: 24rpx;
  text-align: center;
  
  &.disabled {
    background: #ccc;
  }
}

.purchase-btn-text {
  font-size: 28rpx;
  color: #fff;
  font-weight: 600;
}

.history-section {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
  box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.05);
}

.history-list {
  display: flex;
  flex-direction: column;
  gap: 16rpx;
  margin-bottom: 20rpx;
}

.history-item {
  display: flex;
  flex-direction: column;
  gap: 12rpx;
  padding: 16rpx;
  background: #f8f9fa;
  border-radius: 8rpx;
}

.history-plan {
  align-self: flex-start;
  padding: 8rpx 16rpx;
  border-radius: 16rpx;
}

.history-plan-text {
  font-size: 24rpx;
  color: #fff;
}

.history-info {
  display: flex;
  flex-direction: column;
  gap: 8rpx;
}

.history-date {
  font-size: 24rpx;
  color: #666;
}

.history-status {
  display: inline-flex;
  align-self: flex-start;
  padding: 6rpx 16rpx;
  border-radius: 12rpx;
  
  &.active {
    background: #d4edda;
    
    .status-text {
      color: #155724;
    }
  }
  
  &.expired {
    background: #e2e3e5;
    
    .status-text {
      color: #383d41;
    }
  }
  
  &.cancelled {
    background: #fff3cd;
    
    .status-text {
      color: #856404;
    }
  }
}

.status-text {
  font-size: 22rpx;
}

.history-price {
  font-size: 26rpx;
  color: #ff6b9d;
  font-weight: 600;
}

.empty-history {
  text-align: center;
  padding: 40rpx;
  margin-bottom: 20rpx;
}

.empty-text {
  font-size: 26rpx;
  color: #999;
}

.stacking-info {
  background: #d1ecf1;
  border-radius: 8rpx;
  padding: 16rpx;
}

.stacking-text {
  font-size: 24rpx;
  color: #0c5460;
}

.bottom-spacing {
  height: 40rpx;
}
</style>
