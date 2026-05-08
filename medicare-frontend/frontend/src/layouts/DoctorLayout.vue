<script setup lang="ts">
import { computed } from 'vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'
import AppHeader from '@/components/layout/AppHeader.vue'
import { useNotificationStore } from '@/stores/notification'
import {
  HomeIcon,
  CalendarIcon,
  DocumentTextIcon,
  ClockIcon,
  BellIcon
} from '@heroicons/vue/24/outline'

const notificationStore = useNotificationStore()

const menuItems = computed(() => [
  { label: 'Dashboard', icon: HomeIcon, route: '/doctor/dashboard' },
  {
    label: 'Appointments', icon: CalendarIcon, children: [
      { label: 'Today', route: '/doctor/appointments?filter=today' },
      { label: 'All Appointments', route: '/doctor/appointments' },
    ]
  },
  {
    label: 'Prescriptions', icon: DocumentTextIcon, children: [
      { label: 'Write Prescription', route: '/doctor/prescriptions/create' },
      { label: 'History', route: '/doctor/prescriptions' },
    ]
  },
  { label: 'My Schedule', icon: ClockIcon, route: '/doctor/schedule' },
  {
    label: 'Notifications', icon: BellIcon, route: '/doctor/notifications',
    badge: notificationStore.unreadCount || undefined
  },
])
</script>

<template>
  <div class="flex h-screen bg-slate-50 overflow-hidden">
    <AppSidebar :menu-items="menuItems" />
    <div class="flex flex-1 flex-col overflow-hidden">
      <AppHeader />
      <main class="flex-1 overflow-y-auto p-6">
        <router-view v-slot="{ Component, route }">
          <transition name="page" mode="out-in">
            <component :is="Component" :key="route.path" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>