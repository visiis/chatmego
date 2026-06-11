<template>
  <view class="membership-page">
    <view class="header">
      <text class="back-btn" @click="goBack">←</text>
      <text class="page-title">会员中心</text>
      <view class="placeholder"></view>
    </view>
    
    <view class="current-plan" v-if="currentMembership">
      <view class="plan-card">
        <view class="plan-header">
          <text class="plan-badge">当前会员</text>
          <text class="plan-expire" v-if="currentMembership.expire_at">
            有效期至 {{ formatDate(currentMembership.expire_at) }}
          </text>
        </view>
        <view class="plan-info">
          <text class="plan-name">{{ currentMembership.level_name }}</text>
          <view class="plan-benefits">
            <text 
              class="benefit-tag" 
              v-for="(benefit, idx) in currentMembership.benefits" 
              :key="idx"
            >
              {{ benefit }}
            </text>
          </view>
        </view>
        <view class="renew-btn" @click="renewMembership">
          <text>续费会员</text>
        </view>
      </view>
    </view>
    
    <view class="current-plan" v-else>
      <view class="non-member-card">
        <text class="non-member-icon">🌟</text>
        <text class="non-member-title">升级会员</text>
        <text class="non-member-desc">解锁更多特权，提升交友体验</text>
        <view class="upgrade-btn" @click="showPlans = true">
          <text>立即升级</text>
        </view>
      </view>
    </view>
    
    <view class="benefits-section">
      <text class="section-title">会员特权</text>
      <view class="benefits-grid">
        <view class="benefit-item" v-for="benefit in allBenefits" :key="benefit.id">
          <view class="benefit-icon-wrapper">
            <text class="benefit-icon">{{ benefit.icon }}</text>
          </view>
          <text class="benefit-name">{{ benefit.name }}</text>
          <text class="benefit-desc">{{ benefit.desc }}</text>
          <view class="benefit-level">
            <text class="level-text" v-if="benefit.minLevel === 1">VIP1+</text>
            <text class="level-text" v-if="benefit.minLevel === 2">VIP2+</text>
            <text class="level-text" v-if="benefit.minLevel === 3">VIP3+</text>
          </view>
        </view>
      </view>
    </view>
    
    <view class="plans-section">
      <text class="section-title">会员套餐</text>
      <view 
        class="plan-item" 
        v-for="(plan, index) in membershipPlans" 
        :key="plan.id"
        :class="{ selected: selectedPlan === plan.id }"
        @click="selectPlan(plan.id)"
      >
        <view class="plan-badge-popular" v-if="index === 1">推荐</view>
        <view class="plan-price">
          <text class="price-symbol">¥</text>
          <text class="price-value">{{ plan.monthly_price }}</text>
          <text class="price-unit">/月</text>
        </view>
        <text class="plan-name">{{ plan.name }}</text>
        <view class="plan-features">
          <text 
            class="feature-item" 
            v-for="feature in plan.features" 
            :key="feature"
          >
            ✓ {{ feature }}
          </text>
        </view>
        <view class="select-btn" :class="{ active: selectedPlan === plan.id }">
          <text>{{ selectedPlan === plan.id ? '已选择' : '选择' }}</text>
        </view>
      </view>
      
      <view class="purchase-btn" @click="purchasePlan">
        <text>立即购买</text>
      </view>
    </view>
    
    <view class="tips-section">
      <text class="tips-title">💡 温馨提示</text>
      <text class="tips-text">会员购买后即时生效，到期自动续费</text>
      <text class="tips-text">如有问题请联系客服</text>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { getMembershipLevels } from '@/api/membership'

const currentMembership = ref<any>(null)
const selectedPlan = ref(2)
const showPlans = ref(false)

const allBenefits = ref([
  { id: 1, icon: '❤️', name: '每日喜欢', desc: '增加每日喜欢次数', minLevel: 1 },
  { id: 2, icon: '💎', name: '超级喜欢', desc: '优先展示给对方', minLevel: 2 },
  { id: 3, icon: '👀', name: '查看喜欢', desc: '查看谁喜欢了你', minLevel: 1 },
  { id: 4, icon: '🎭', name: '隐身查看', desc: '查看他人不留下记录', minLevel: 2 },
  { id: 5, icon: '🔄', name: '消息撤回', desc: '撤回已发送消息', minLevel: 3 },
  { id: 6, icon: '🏆', name: '优先匹配', desc: '匹配排名更靠前', minLevel: 3 },
  { id: 7, icon: '🎨', name: '专属头像框', desc: '个性化头像框', minLevel: 2 },
  { id: 8, icon: '⭐', name: '身份标识', desc: 'VIP专属标识', minLevel: 1 }
])

const membershipPlans = ref([
  { id: 1, name: '月卡会员', monthly_price: 29, features: ['每日50次喜欢', '查看喜欢我的人', 'VIP标识'] },
  { id: 2, name: '季卡会员', monthly_price: 69, features: ['每日100次喜欢', '超级喜欢x5', '隐身查看', '专属头像框'] },
  { id: 3, name: '年卡会员', monthly_price: 199, features: ['每日200次喜欢', '超级喜欢x20', '消息撤回', '优先匹配', '全部特权'] }
])

function formatDate(dateStr: string) {
  const date = new Date(dateStr)
  return `${date.getFullYear()}/${date.getMonth() + 1}/${date.getDate()}`
}

function selectPlan(planId: number) {
  selectedPlan.value = planId
}

function renewMembership() {
  showPlans.value = true
}

async function purchasePlan() {
  const plan = membershipPlans.value.find(p => p.id === selectedPlan.value)
  if (!plan) return
  
  uni.showModal({
    title: '购买确认',
    content: `确认购买 ${plan.name} (¥${plan.monthly_price}/月)?`,
    success: (res) => {
      if (res.confirm) {
        uni.showToast({ title: '购买成功', icon: 'success' })
      }
    }
  })
}

function goBack() {
  uni.navigateBack()
}

onMounted(() => {
  currentMembership.value = {
    level_name: 'VIP2 季卡会员',
    expire_at: '2025-12-31',
    benefits: ['每日100次喜欢', '超级喜欢', '隐身查看']
  }
})
</script>

<style lang="scss" scoped>
.membership-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 40rpx;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 60rpx 32rpx 24rpx;
  background: linear-gradient(135deg, #ffd700 0%, #ffb700 100%);
}

.back-btn {
  font-size: 44rpx;
  color: #fff;
}

.page-title {
  font-size: 36rpx;
  font-weight: bold;
  color: #fff;
}

.placeholder {
  width: 80rpx;
}

.current-plan {
  padding: 24rpx;
}

.plan-card {
  background: linear-gradient(135deg, #ffd700 0%, #ffb700 100%);
  border-radius: 20rpx;
  padding: 32rpx;
  color: #fff;
}

.plan-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20rpx;
}

.plan-badge {
  background: rgba(255, 255, 255, 0.3);
  padding: 8rpx 20rpx;
  border-radius: 20rpx;
  font-size: 22rpx;
}

.plan-expire {
  font-size: 24rpx;
  opacity: 0.9;
}

.plan-name {
  display: block;
  font-size: 40rpx;
  font-weight: bold;
  margin-bottom: 16rpx;
}

.plan-benefits {
  display: flex;
  flex-wrap: wrap;
  gap: 12rpx;
  margin-bottom: 24rpx;
}

.benefit-tag {
  background: rgba(255, 255, 255, 0.3);
  padding: 8rpx 20rpx;
  border-radius: 20rpx;
  font-size: 24rpx;
}

.renew-btn {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 40rpx;
  padding: 20rpx;
  text-align: center;
  
  text {
    font-size: 28rpx;
    font-weight: bold;
  }
}

.non-member-card {
  background: #fff;
  border-radius: 20rpx;
  padding: 48rpx 32rpx;
  text-align: center;
}

.non-member-icon {
  font-size: 80rpx;
  margin-bottom: 20rpx;
}

.non-member-title {
  display: block;
  font-size: 36rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 12rpx;
}

.non-member-desc {
  display: block;
  font-size: 26rpx;
  color: #999;
  margin-bottom: 32rpx;
}

.upgrade-btn {
  background: linear-gradient(135deg, #ffd700 0%, #ffb700 100%);
  border-radius: 40rpx;
  padding: 24rpx;
  
  text {
    font-size: 30rpx;
    font-weight: bold;
    color: #fff;
  }
}

.benefits-section {
  padding: 0 24rpx;
  margin-bottom: 24rpx;
}

.section-title {
  display: block;
  font-size: 32rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 20rpx;
}

.benefits-grid {
  display: flex;
  flex-wrap: wrap;
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
}

.benefit-item {
  width: 50%;
  padding: 20rpx 16rpx;
  text-align: center;
}

.benefit-icon-wrapper {
  width: 80rpx;
  height: 80rpx;
  background: #f5f5f5;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12rpx;
}

.benefit-icon {
  font-size: 36rpx;
}

.benefit-name {
  display: block;
  font-size: 26rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 8rpx;
}

.benefit-desc {
  display: block;
  font-size: 22rpx;
  color: #999;
  margin-bottom: 12rpx;
}

.benefit-level {
  display: inline-block;
}

.level-text {
  font-size: 20rpx;
  color: #f87c7c;
  background: rgba(248, 124, 124, 0.1);
  padding: 4rpx 12rpx;
  border-radius: 12rpx;
}

.plans-section {
  padding: 0 24rpx;
}

.plan-item {
  background: #fff;
  border-radius: 16rpx;
  padding: 24rpx;
  margin-bottom: 20rpx;
  border: 3rpx solid transparent;
  
  &.selected {
    border-color: #ffd700;
  }
}

.plan-badge-popular {
  position: absolute;
  top: -1rpx;
  right: 24rpx;
  background: #f87c7c;
  padding: 6rpx 16rpx;
  border-radius: 0 0 12rpx 12rpx;
  font-size: 20rpx;
  color: #fff;
}

.plan-price {
  display: flex;
  align-items: baseline;
  margin-bottom: 12rpx;
}

.price-symbol {
  font-size: 28rpx;
  color: #ffd700;
}

.price-value {
  font-size: 56rpx;
  font-weight: bold;
  color: #ffd700;
}

.price-unit {
  font-size: 24rpx;
  color: #999;
  margin-left: 4rpx;
}

.plan-name {
  display: block;
  font-size: 32rpx;
  font-weight: bold;
  color: #333;
  margin-bottom: 16rpx;
}

.plan-features {
  margin-bottom: 20rpx;
}

.feature-item {
  display: block;
  font-size: 24rpx;
  color: #666;
  margin-bottom: 8rpx;
}

.select-btn {
  background: #f5f5f5;
  border-radius: 30rpx;
  padding: 16rpx;
  text-align: center;
  
  text {
    font-size: 26rpx;
    color: #666;
  }
  
  &.active {
    background: #ffd700;
    
    text {
      color: #fff;
      font-weight: bold;
    }
  }
}

.purchase-btn {
  background: linear-gradient(135deg, #ffd700 0%, #ffb700 100%);
  border-radius: 40rpx;
  padding: 24rpx;
  text-align: center;
  margin-top: 8rpx;
  
  text {
    font-size: 32rpx;
    font-weight: bold;
    color: #fff;
  }
}

.tips-section {
  margin: 32rpx 24rpx;
  padding: 24rpx;
  background: #fff9e6;
  border-radius: 12rpx;
}

.tips-title {
  display: block;
  font-size: 28rpx;
  font-weight: bold;
  color: #d49500;
  margin-bottom: 12rpx;
}

.tips-text {
  display: block;
  font-size: 24rpx;
  color: #999;
  margin-bottom: 8rpx;
}
</style>
