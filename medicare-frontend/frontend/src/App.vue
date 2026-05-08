<script setup lang="ts">
import { onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useWebSocket } from '@/composables/useWebSocket'
import { useUIStore } from '@/stores/ui'
import ToastContainer from '@/components/ui/ToastContainer.vue'

const authStore = useAuthStore()
const uiStore = useUIStore()
const { connect, disconnect } = useWebSocket()

onMounted(async () => {
  await authStore.fetchUser()
  if (authStore.isAuthenticated) {
    connect()
  }
})

watch(
  () => authStore.isAuthenticated,
  (val) => {
    if (val) connect()
    else disconnect()
  }
)
</script>

<template>
  <router-view v-slot="{ Component, route }">
    <transition name="page" mode="out-in">
      <component :is="Component" :key="route.path" />
    </transition>
  </router-view>
  <ToastContainer :toasts="uiStore.toasts" @remove="uiStore.removeToast" />
</template>