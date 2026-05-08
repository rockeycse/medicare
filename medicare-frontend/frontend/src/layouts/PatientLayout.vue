<script setup lang="ts">
import { computed } from 'vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'
import AppHeader from '@/components/layout/AppHeader.vue'
import { useNotificationStore } from '@/stores/notification'
import {
  HomeIcon,
  PlusCircleIcon,
  CalendarIcon,
  DocumentTextIcon,
  CreditCardIcon,
  FolderIcon,
  BellIcon
} from '@heroicons/vue/24/outline'

const notificationStore = useNotificationStore()

const menuItems = computed(() => [
  { label: 'Dashboard', icon: HomeIcon, route: '/patient/dashboard' },
  { label: 'Book Appointment', icon: PlusCircleIcon, route: '/patient/book-appointment' },
  { label: 'My Appointments', icon: CalendarIcon, route: '/patient/appointments' },
  { label: 'Prescriptions', icon: DocumentTextIcon, route: '/patient/prescriptions' },
  { label: 'Invoices', icon: CreditCardIcon, route: '/patient/invoices' },
  { label: 'Medical Records', icon: FolderIcon, route: '/patient/medical-records' },
  {
    label: 'Notifications', icon: BellIcon, route: '/patient/notifications',
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