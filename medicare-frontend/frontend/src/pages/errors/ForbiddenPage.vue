<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppButton from '@/components/ui/AppButton.vue'

const router = useRouter()
const authStore = useAuthStore()
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
    <div class="text-center max-w-md">
      <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-red-100">
        <svg class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
      </div>
      <h1 class="text-6xl font-bold text-red-500 mb-4">403</h1>
      <h2 class="text-2xl font-semibold text-slate-800 mb-2">Access Forbidden</h2>
      <p class="text-slate-500 mb-8">You don't have permission to access this page.</p>
      <div class="flex gap-3 justify-center">
        <AppButton variant="outline" @click="router.back()">Go Back</AppButton>
        <AppButton variant="primary" @click="router.push(authStore.dashboardRoute)">
          Dashboard
        </AppButton>
      </div>
    </div>
  </div>
</template>