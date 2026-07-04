<template>
  <view class="filter-panel" :class="{ visible: visible }">
    <view class="filter-mask" @click="close"></view>
    
    <view class="filter-content">
      <view class="filter-header">
        <text class="filter-title">筛选条件</text>
        <view class="filter-close" @click="close">
          <FontAwesome name="times" size="24px" color="#999" />
        </view>
      </view>
      
      <scroll-view class="filter-scroll" scroll-y>
        <view class="filter-section">
          <text class="section-title">性别</text>
          <view class="filter-options">
            <view 
              class="filter-option"
              :class="{ active: filters.gender === '' }"
              @click="setGender('')"
            >
              <text>不限</text>
            </view>
            <view 
              class="filter-option"
              :class="{ active: filters.gender === 'male' }"
              @click="setGender('male')"
            >
              <FontAwesome name="mars" size="20px" color="#4a90d9" />
              <text>男</text>
            </view>
            <view 
              class="filter-option"
              :class="{ active: filters.gender === 'female' }"
              @click="setGender('female')"
            >
              <FontAwesome name="venus" size="20px" color="#ff6b9d" />
              <text>女</text>
            </view>
          </view>
        </view>
        
        <view class="filter-section">
          <text class="section-title">年龄 {{ filters.ageMin }}-{{ filters.ageMax }}岁</text>
          <view class="range-slider">
            <slider 
              class="slider"
              :min="18" 
              :max="50" 
              :step="1"
              :value="[filters.ageMin, filters.ageMax]"
              activeColor="#ff6b9d"
              backgroundColor="#eee"
              block-size="24"
              @change="onAgeChange"
            />
            <view class="slider-labels">
              <text>18</text>
              <text>50</text>
            </view>
          </view>
        </view>
        
        <view class="filter-section">
          <text class="section-title">身高 {{ filters.heightMin }}-{{ filters.heightMax }}cm</text>
          <view class="range-slider">
            <slider 
              class="slider"
              :min="140" 
              :max="190" 
              :step="5"
              :value="[filters.heightMin, filters.heightMax]"
              activeColor="#ff6b9d"
              backgroundColor="#eee"
              block-size="24"
              @change="onHeightChange"
            />
            <view class="slider-labels">
              <text>140</text>
              <text>190</text>
            </view>
          </view>
        </view>
        
        <view class="filter-section">
          <text class="section-title">体重 {{ filters.weightMin }}-{{ filters.weightMax }}kg</text>
          <view class="range-slider">
            <slider 
              class="slider"
              :min="40" 
              :max="100" 
              :step="5"
              :value="[filters.weightMin, filters.weightMax]"
              activeColor="#ff6b9d"
              backgroundColor="#eee"
              block-size="24"
              @change="onWeightChange"
            />
            <view class="slider-labels">
              <text>40</text>
              <text>100</text>
            </view>
          </view>
        </view>
      </scroll-view>
      
      <view class="filter-footer">
        <view class="btn-reset" @click="reset">
          <text>重置</text>
        </view>
        <view class="btn-confirm" @click="confirm">
          <text>确定</text>
        </view>
      </view>
    </view>
  </view>
</template>

<script setup lang="ts">
import { reactive, watch } from 'vue'
import FontAwesome from './FontAwesome.vue'

interface FilterData {
  gender: string
  ageMin: number
  ageMax: number
  heightMin: number
  heightMax: number
  weightMin: number
  weightMax: number
}

const props = defineProps<{
  visible: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'confirm', filters: FilterData): void
}>()

const filters = reactive<FilterData>({
  gender: '',
  ageMin: 18,
  ageMax: 50,
  heightMin: 140,
  heightMax: 190,
  weightMin: 40,
  weightMax: 100
})

const defaultFilters = {
  gender: '',
  ageMin: 18,
  ageMax: 50,
  heightMin: 140,
  heightMax: 190,
  weightMin: 40,
  weightMax: 100
}

function setGender(value: string) {
  filters.gender = value
}

function onAgeChange(e: any) {
  const values = e.detail.value as number[]
  filters.ageMin = values[0]
  filters.ageMax = values[1]
}

function onHeightChange(e: any) {
  const values = e.detail.value as number[]
  filters.heightMin = values[0]
  filters.heightMax = values[1]
}

function onWeightChange(e: any) {
  const values = e.detail.value as number[]
  filters.weightMin = values[0]
  filters.weightMax = values[1]
}

function reset() {
  Object.assign(filters, defaultFilters)
}

function confirm() {
  emit('confirm', { ...filters })
  emit('close')
}

function close() {
  emit('close')
}

watch(() => props.visible, (val) => {
  if (!val) {
    reset()
  }
})
</script>

<style lang="scss" scoped>
.filter-panel {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1000;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.3s ease;
  
  &.visible {
    pointer-events: auto;
    opacity: 1;
    
    .filter-content {
      transform: translateY(0);
    }
  }
}

.filter-mask {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
}

.filter-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: #fff;
  border-radius: 32rpx 32rpx 0 0;
  transform: translateY(100%);
  transition: transform 0.3s ease;
  max-height: 80vh;
  display: flex;
  flex-direction: column;
}

.filter-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 32rpx;
  border-bottom: 1rpx solid #eee;
}

.filter-title {
  font-size: 34rpx;
  font-weight: 600;
  color: #333;
}

.filter-close {
  width: 60rpx;
  height: 60rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.filter-scroll {
  flex: 1;
  padding: 24rpx 32rpx;
}

.filter-section {
  margin-bottom: 36rpx;
}

.section-title {
  font-size: 28rpx;
  font-weight: 600;
  color: #333;
  margin-bottom: 20rpx;
  display: block;
}

.filter-options {
  display: flex;
  flex-wrap: wrap;
  gap: 16rpx;
}

.filter-option {
  padding: 16rpx 28rpx;
  background: #f5f5f5;
  border-radius: 24rpx;
  font-size: 26rpx;
  color: #666;
  display: flex;
  align-items: center;
  gap: 8rpx;
  transition: all 0.2s ease;
  
  &.active {
    background: #ff6b9d;
    color: #fff;
  }
  
  &:active {
    opacity: 0.7;
  }
}

.range-slider {
  padding: 16rpx 0;
}

.slider {
  margin: 0;
}

.slider-labels {
  display: flex;
  justify-content: space-between;
  margin-top: 12rpx;
  font-size: 24rpx;
  color: #999;
}

.filter-footer {
  display: flex;
  gap: 24rpx;
  padding: 24rpx 32rpx;
  padding-bottom: calc(24rpx + env(safe-area-inset-bottom));
  border-top: 1rpx solid #eee;
}

.btn-reset,
.btn-confirm {
  flex: 1;
  height: 88rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 44rpx;
  font-size: 30rpx;
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn-reset {
  background: #f5f5f5;
  color: #666;
  
  &:active {
    background: #eee;
  }
}

.btn-confirm {
  background: linear-gradient(135deg, #ff6b9d, #c44569);
  color: #fff;
  
  &:active {
    opacity: 0.9;
  }
}
</style>
