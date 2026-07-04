<template>
  <view class="album-container">
    <scroll-view class="content" scroll-y>
      <view class="photos-grid">
        <view class="photo-item" v-for="photo in photos" :key="photo.id">
          <image class="photo-img" :src="photo.thumbnail_url || photo.url" mode="aspectFill" />
          <view class="photo-badge premium" v-if="photo.is_premium">
            <FontAwesome name="lock" size="16px" color="#ffd700" />
          </view>
          <view class="photo-badge main" v-if="photo.is_main">
            <FontAwesome name="star" size="16px" color="#ff6b9d" />
          </view>
        </view>

        <view class="photo-item uploading-item" v-if="uploading">
          <view class="upload-progress">
            <view class="progress-bar">
              <view class="progress-fill" :style="{ width: uploadProgress + '%' }"></view>
            </view>
            <text class="progress-text">{{ uploadProgress }}%</text>
          </view>
        </view>

        <view class="photo-item upload-btn" @click="uploadPhoto" v-if="!uploading">
          <view class="upload-icon">
            <FontAwesome name="plus" size="48px" color="#ccc" />
          </view>
        </view>
      </view>

      <view class="empty-state" v-if="photos.length === 0">
        <FontAwesome name="image" size="80px" color="#ddd" />
        <text class="empty-text">还没有照片，点击添加</text>
        <view class="upload-btn-large" @click="uploadPhoto">
          <FontAwesome name="plus" size="32px" color="#fff" />
          <text>上传照片</text>
        </view>
      </view>
    </scroll-view>
  </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import FontAwesome from '../../components/FontAwesome.vue'
import { getAlbum } from '../../api/user'

interface Photo {
  id: number
  url: string
  thumbnail_url?: string
  title?: string
  is_main: number
  is_premium: number
  points_price: number
}

const photos = ref<Photo[]>([])
const uploading = ref(false)
const uploadProgress = ref(0)

onMounted(() => {
  loadPhotos()
})

async function loadPhotos() {
  try {
    const result = await getAlbum()
    photos.value = result.photos || []
  } catch (error) {
    console.error('加载相册失败:', error)
  }
}

function uploadPhoto() {
  if (uploading.value) return

  uni.chooseImage({
    count: 1,
    success: (res) => {
      const tempFilePath = res.tempFilePaths[0]
      const token = uni.getStorageSync('token')

      uploading.value = true
      uploadProgress.value = 0

      const uploadTask = uni.uploadFile({
        url: 'https://chatmego.com/api/user/album/upload',
        filePath: tempFilePath,
        name: 'photo',
        header: {
          'Authorization': 'Bearer ' + token
        },
        success: (uploadRes) => {
          try {
            const data = JSON.parse(uploadRes.data)
            if (data.code === 200) {
              const photoData = data.data
              if (photoData.url && !photoData.thumbnail_url) {
                if (photoData.url.includes('.th.')) {
                  photoData.thumbnail_url = photoData.url
                } else {
                  const urlParts = photoData.url.split('.')
                  const ext = urlParts.pop()
                  photoData.thumbnail_url = urlParts.join('.') + '.th.' + ext
                }
              }
              photos.value.unshift(photoData)
              uni.showToast({ title: '上傳成功', icon: 'success' })
            } else {
              uni.showToast({ title: data.message || '上傳失敗', icon: 'none' })
            }
          } catch (e) {
            uni.showToast({ title: '上傳失敗', icon: 'none' })
          }
        },
        fail: () => {
          uni.showToast({ title: '上傳失敗', icon: 'none' })
        },
        complete: () => {
          uploading.value = false
          uploadProgress.value = 0
        }
      })

      uploadTask.onProgressUpdate((res) => {
        uploadProgress.value = res.progress
      })
    }
  })
}
</script>

<style lang="scss">
* {
  box-sizing: border-box;
}

page {
  height: 100%;
  width: 100%;
  overflow-x: hidden;
  background: #ffffff;
}

.album-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  width: 100%;
  background: #ffffff;
  overflow-x: hidden;
}

.content {
  flex: 1;
  padding: 24rpx;
}

.photos-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16rpx;
}

.photo-item {
  aspect-ratio: 1;
  border-radius: 12rpx;
  overflow: hidden;
  position: relative;
  background: #fff;

  &.upload-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2rpx dashed #ddd;
  }
}

.photo-img {
  width: 100%;
  height: 100%;
}

.upload-icon {
  width: 100rpx;
  height: 100rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.photo-badge {
  position: absolute;
  top: 8rpx;
  right: 8rpx;
  width: 36rpx;
  height: 36rpx;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;

  &.premium {
    background: rgba(255, 215, 0, 0.9);
  }

  &.main {
    background: rgba(255, 107, 157, 0.9);
  }
}

.uploading-item {
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 107, 157, 0.1);
}

.upload-progress {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  padding: 20rpx;
}

.progress-bar {
  width: 80%;
  height: 12rpx;
  background: #eee;
  border-radius: 6rpx;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
  transition: width 0.2s ease;
}

.progress-text {
  font-size: 24rpx;
  color: #ff6b9d;
  margin-top: 12rpx;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 200rpx 0;
}

.empty-text {
  font-size: 28rpx;
  color: #999;
  margin-top: 24rpx;
}

.upload-btn-large {
  margin-top: 40rpx;
  padding: 24rpx 48rpx;
  background: linear-gradient(135deg, #ff6b9d, #c44569);
  border-radius: 44rpx;
  display: flex;
  align-items: center;
  gap: 12rpx;
  font-size: 30rpx;
  color: #fff;
}
</style>