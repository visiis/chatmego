<template>
  <view class="gifts-container">
    <view class="status-bar"></view>
    
    <view class="header">
      <view class="header-content">
        <view class="back-btn" @click="goBack">
          <FontAwesome name="chevron-left" size="36px" color="#fff" />
        </view>
        <text class="header-title">我的禮物</text>
        <view class="header-right">
          <view class="history-btn" @click="goHistory">
            <FontAwesome name="history" size="32px" color="#fff" />
          </view>
        </view>
      </view>
    </view>
    
    <scroll-view class="content" scroll-y>
      <view class="physical-section" v-if="physicalGifts.length > 0">
        <view class="section-header">
          <view class="section-icon">
            <FontAwesome name="package" size="28px" color="#27ae60" />
          </view>
          <text class="section-title">實體禮物</text>
          <view class="section-actions">
            <view class="info-btn" @click="openRedemptionInfo">
              <FontAwesome name="edit" size="24px" color="#666" />
              <text class="info-btn-text">填寫地址</text>
            </view>
            <view class="redeem-btn" :class="{ disabled: selectedGifts.length === 0 }" @click="handleRedeem">
              <FontAwesome name="check-circle" size="24px" color="#fff" />
              <text class="redeem-btn-text">兌換</text>
              <text class="redeem-count" v-if="selectedGifts.length > 0">({{ selectedGifts.length }})</text>
            </view>
          </view>
        </view>
        
        <view class="gift-list">
          <view class="gift-item" v-for="gift in physicalGifts" :key="gift.id" @click="toggleSelect(gift.id)">
            <view class="gift-checkbox" :class="{ checked: selectedGifts.includes(gift.id) }">
              <FontAwesome v-if="selectedGifts.includes(gift.id)" name="check" size="24px" color="#fff" />
            </view>
            <image v-if="gift.image" class="gift-image" :src="gift.image" mode="aspectFill" />
            <view class="gift-image-placeholder" v-else>
              <FontAwesome name="gift" size="48px" color="#ccc" />
            </view>
            <view class="gift-info">
              <text class="gift-name">{{ gift.name }}</text>
              <text class="gift-desc">{{ gift.description }}</text>
              <view class="gift-meta">
                <text class="gift-price">{{ getPriceTypeLabel(gift.price_type) }}: {{ gift.price }}</text>
                <text class="gift-quantity" v-if="gift.quantity > 1">×{{ gift.quantity }}</text>
              </view>
              <text class="gift-date">{{ formatDate(gift.created_at) }}</text>
            </view>
          </view>
        </view>
      </view>
      
      <view class="empty-section" v-else>
        <view class="empty-icon">
          <FontAwesome name="box" size="64px" color="#ccc" />
        </view>
        <text class="empty-text">暫無實體禮物</text>
      </view>
      
      <view class="virtual-section" v-if="virtualGifts.length > 0">
        <view class="section-header">
          <view class="section-icon">
            <FontAwesome name="gem" size="28px" color="#3498db" />
          </view>
          <text class="section-title">虛擬禮物</text>
        </view>
        
        <view class="virtual-grid">
          <view class="virtual-item" v-for="gift in virtualGifts" :key="gift.id">
            <view class="virtual-image-wrapper">
              <image v-if="gift.image" class="virtual-image" :src="gift.image" mode="aspectFill" />
              <view class="virtual-image-placeholder" v-else>
                <FontAwesome name="gift" size="48px" color="#ccc" />
              </view>
              <view class="quantity-badge" v-if="gift.quantity > 1">
                <text class="quantity-text">×{{ gift.quantity }}</text>
              </view>
            </view>
            <text class="virtual-name">{{ gift.name }}</text>
            <text class="virtual-price">{{ getPriceTypeLabel(gift.price_type) }}: {{ gift.price }}</text>
          </view>
        </view>
      </view>
      
      <view class="empty-section" v-if="virtualGifts.length > 0 && physicalGifts.length === 0">
        <view class="empty-icon">
          <FontAwesome name="gift" size="64px" color="#ccc" />
        </view>
        <text class="empty-text">暫無虛擬禮物</text>
      </view>
    </scroll-view>
    
    <view class="modal-overlay" v-if="showModal" @click="closeModal">
      <view class="modal-content" @click.stop>
        <view class="modal-header">
          <text class="modal-title">填寫收貨地址</text>
          <view class="modal-close" @click="closeModal">
            <FontAwesome name="times" size="32px" color="#999" />
          </view>
        </view>
        
        <view class="form-group">
          <text class="form-label">收件人姓名 *</text>
          <input class="form-input" v-model="form.recipient_name" placeholder="請輸入收件人姓名" />
        </view>
        
        <view class="form-group">
          <text class="form-label">聯繫電話 *</text>
          <input class="form-input" v-model="form.phone" placeholder="請輸入聯繫電話" type="tel" />
        </view>
        
        <view class="form-group">
          <text class="form-label">詳細地址 *</text>
          <textarea class="form-textarea" v-model="form.address" placeholder="請輸入詳細地址" />
        </view>
        
        <view class="form-group">
          <text class="form-label">收件人手機（選填）</text>
          <input class="form-input" v-model="form.recipient_phone" placeholder="如與上面不同請填寫" type="tel" />
        </view>
        
        <view class="modal-footer">
          <view class="modal-btn cancel-btn" @click="closeModal">
            <text>取消</text>
          </view>
          <view class="modal-btn confirm-btn" @click="saveInfo">
            <text>保存</text>
          </view>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { getUserGifts, saveRedemptionInfo, redeemGifts, type UserGift, type RedemptionInfo } from '../../api/gift'

const physicalGifts = ref<UserGift[]>([])
const virtualGifts = ref<UserGift[]>([])
const selectedGifts = ref<number[]>([])
const showModal = ref(false)
const hasRedemptionInfo = ref(false)

const form = ref<RedemptionInfo>({
  recipient_name: '',
  phone: '',
  address: '',
  recipient_phone: ''
})

onMounted(() => {
  loadGifts()
})

async function loadGifts() {
  try {
    const result = await getUserGifts()
    physicalGifts.value = result.physical_gifts || []
    virtualGifts.value = result.virtual_gifts || []
    hasRedemptionInfo.value = result.has_redemption_info || false
  } catch (error) {
    console.error('加載禮物失敗:', error)
    uni.showToast({ title: '加載失敗', icon: 'none' })
  }
}

function toggleSelect(giftId: number) {
  const index = selectedGifts.value.indexOf(giftId)
  if (index > -1) {
    selectedGifts.value.splice(index, 1)
  } else {
    selectedGifts.value.push(giftId)
  }
}

function getPriceTypeLabel(type: string): string {
  const labels: Record<string, string> = {
    points: '積分',
    coins: '金幣',
    money: '元'
  }
  return labels[type] || type
}

function formatDate(dateStr: string): string {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const month = date.getMonth() + 1
  const day = date.getDate()
  return `${month}月${day}日`
}

function goBack() {
  uni.navigateBack()
}

function goHistory() {
  uni.navigateTo({ url: '/pages/profile/redemption-history' })
}

function openRedemptionInfo() {
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

async function saveInfo() {
  if (!form.value.recipient_name || !form.value.phone || !form.value.address) {
    uni.showToast({ title: '請填寫必填項', icon: 'none' })
    return
  }
  
  try {
    await saveRedemptionInfo(form.value)
    hasRedemptionInfo.value = true
    closeModal()
    uni.showToast({ title: '保存成功', icon: 'success' })
  } catch (error) {
    console.error('保存失敗:', error)
    uni.showToast({ title: '保存失敗', icon: 'none' })
  }
}

async function handleRedeem() {
  if (selectedGifts.value.length === 0) {
    uni.showToast({ title: '請選擇要兌換的禮物', icon: 'none' })
    return
  }
  
  if (!hasRedemptionInfo.value && !form.value.recipient_name) {
    uni.showModal({
      title: '提示',
      content: '請先填寫收貨地址',
      showCancel: false,
      success: () => {
        openRedemptionInfo()
      }
    })
    return
  }
  
  uni.showModal({
    title: '確認兌換',
    content: `將兌換 ${selectedGifts.value.length} 件禮物`,
    success: async (res) => {
      if (res.confirm) {
        try {
          const info = hasRedemptionInfo.value ? form.value : {
            recipient_name: form.value.recipient_name,
            phone: form.value.phone,
            address: form.value.address,
            recipient_phone: form.value.recipient_phone
          }
          await redeemGifts(selectedGifts.value, info)
          selectedGifts.value = []
          loadGifts()
          uni.showToast({ title: '兌換成功', icon: 'success' })
        } catch (error) {
          console.error('兌換失敗:', error)
          uni.showToast({ title: '兌換失敗', icon: 'none' })
        }
      }
    }
  })
}
</script>

<style lang="scss">
page {
  height: 100%;
  margin: 0;
  padding: 0;
  background: #f5f5f5;
}

.gifts-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.status-bar {
  height: var(--status-bar-height, 44px);
}

.header {
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  padding: 16rpx 24rpx;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.back-btn {
  width: 72rpx;
  height: 72rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.header-title {
  font-size: 36rpx;
  color: #fff;
  font-weight: bold;
}

.header-right {
  display: flex;
  align-items: center;
}

.history-btn {
  width: 72rpx;
  height: 72rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.content {
  flex: 1;
  padding: 24rpx;
}

.section-header {
  display: flex;
  align-items: center;
  margin-bottom: 20rpx;
  background: #fff;
  padding: 20rpx 24rpx;
  border-radius: 12rpx;
}

.section-icon {
  width: 56rpx;
  height: 56rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 16rpx;
}

.section-title {
  font-size: 32rpx;
  color: #333;
  font-weight: bold;
  flex: 1;
}

.section-actions {
  display: flex;
  align-items: center;
  gap: 16rpx;
}

.info-btn {
  display: flex;
  align-items: center;
  gap: 8rpx;
  padding: 12rpx 20rpx;
  background: #f8f9fa;
  border-radius: 20rpx;
}

.info-btn-text {
  font-size: 24rpx;
  color: #666;
}

.redeem-btn {
  display: flex;
  align-items: center;
  gap: 8rpx;
  padding: 12rpx 24rpx;
  background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
  border-radius: 20rpx;
  
  &.disabled {
    background: #ccc;
  }
}

.redeem-btn-text {
  font-size: 24rpx;
  color: #fff;
  font-weight: 500;
}

.redeem-count {
  font-size: 24rpx;
  color: #fff;
}

.gift-list {
  background: #fff;
  border-radius: 12rpx;
  overflow: hidden;
}

.gift-item {
  display: flex;
  align-items: center;
  padding: 20rpx 24rpx;
  border-bottom: 1rpx solid #f0f0f0;
  
  &:last-child {
    border-bottom: none;
  }
  
  &:active {
    background: #f8f9fa;
  }
}

.gift-checkbox {
  width: 48rpx;
  height: 48rpx;
  border: 2rpx solid #ddd;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 20rpx;
  
  &.checked {
    background: #27ae60;
    border-color: #27ae60;
  }
}

.gift-image {
  width: 120rpx;
  height: 120rpx;
  border-radius: 12rpx;
  background: #f8f9fa;
}

.gift-image-placeholder {
  width: 120rpx;
  height: 120rpx;
  background: #f8f9fa;
  border-radius: 12rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.gift-info {
  flex: 1;
  margin-left: 20rpx;
}

.gift-name {
  display: block;
  font-size: 30rpx;
  color: #333;
  font-weight: 500;
  margin-bottom: 8rpx;
}

.gift-desc {
  display: block;
  font-size: 24rpx;
  color: #999;
  margin-bottom: 8rpx;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.gift-meta {
  display: flex;
  align-items: center;
  gap: 16rpx;
  margin-bottom: 8rpx;
}

.gift-price {
  font-size: 24rpx;
  color: #27ae60;
}

.gift-quantity {
  font-size: 24rpx;
  color: #3498db;
}

.gift-date {
  font-size: 22rpx;
  color: #ccc;
}

.virtual-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16rpx;
}

.virtual-item {
  background: #fff;
  border-radius: 12rpx;
  overflow: hidden;
}

.virtual-image-wrapper {
  position: relative;
  height: 200rpx;
}

.virtual-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.virtual-image-placeholder {
  width: 100%;
  height: 100%;
  background: #f8f9fa;
  display: flex;
  align-items: center;
  justify-content: center;
}

.quantity-badge {
  position: absolute;
  top: 12rpx;
  right: 12rpx;
  background: #3498db;
  padding: 6rpx 16rpx;
  border-radius: 12rpx;
}

.quantity-text {
  font-size: 22rpx;
  color: #fff;
}

.virtual-name {
  display: block;
  font-size: 28rpx;
  color: #333;
  font-weight: 500;
  padding: 16rpx 16rpx 8rpx;
}

.virtual-price {
  display: block;
  font-size: 24rpx;
  color: #3498db;
  padding: 0 16rpx 16rpx;
}

.empty-section {
  background: #fff;
  border-radius: 12rpx;
  padding: 60rpx 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 20rpx;
}

.empty-icon {
  margin-bottom: 16rpx;
}

.empty-text {
  font-size: 28rpx;
  color: #999;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  width: calc(100% - 48rpx);
  background: #fff;
  border-radius: 20rpx;
  overflow: hidden;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 32rpx 24rpx;
  border-bottom: 1rpx solid #f0f0f0;
}

.modal-title {
  font-size: 34rpx;
  color: #333;
  font-weight: bold;
}

.modal-close {
  width: 64rpx;
  height: 64rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-group {
  padding: 24rpx;
}

.form-label {
  display: block;
  font-size: 28rpx;
  color: #333;
  margin-bottom: 12rpx;
}

.form-input {
  width: 100%;
  height: 80rpx;
  background: #f8f9fa;
  border-radius: 12rpx;
  padding: 0 20rpx;
  font-size: 28rpx;
  box-sizing: border-box;
}

.form-textarea {
  width: 100%;
  height: 160rpx;
  background: #f8f9fa;
  border-radius: 12rpx;
  padding: 20rpx;
  font-size: 28rpx;
  box-sizing: border-box;
}

.modal-footer {
  display: flex;
  padding: 24rpx;
  gap: 20rpx;
}

.modal-btn {
  flex: 1;
  height: 88rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 44rpx;
  font-size: 30rpx;
  
  &.cancel-btn {
    background: #f8f9fa;
    color: #666;
  }
  
  &.confirm-btn {
    background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
    color: #fff;
  }
}
</style>
