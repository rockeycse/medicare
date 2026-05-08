<script setup lang="ts">
import { useRouter } from 'vue-router'
import AppButton from '@/components/ui/AppButton.vue'
const router = useRouter()
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
    <div class="text-center max-w-md">
      <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-slate-100">
        <svg class="h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
        </svg>
      </div>
      <h1 class="text-6xl font-bold text-slate-300 mb-4">404</h1>
      <h2 class="text-2xl font-semibold text-slate-800 mb-2">Page Not Found</h2>
      <p class="text-slate-500 mb-8">The page you're looking for doesn't exist or has been moved.</p>
      <div class="flex gap-3 justify-center">
        <AppButton variant="outline" @click="router.back()">Go Back</AppButton>
        <AppButton variant="primary" @click="router.push('/')">Home</AppButton>
      </div>
    </div>
  </div>
</template>