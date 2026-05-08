<script setup lang="ts">
import { computed } from 'vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'
import AppHeader from '@/components/layout/AppHeader.vue'
import { useNotificationStore } from '@/stores/notification'
import {
  HomeIcon,
  UsersIcon,
  UserPlusIcon,
  HeartIcon,
  CalendarIcon,
  CreditCardIcon,
  FolderIcon
} from '@heroicons/vue/24/outline'

const notificationStore = useNotificationStore()

const menuItems = computed(() => [
  { label: 'Dashboard', icon: HomeIcon, route: '/admin/dashboard' },
  {
    label: 'User Management', icon: UsersIcon, children: [
      { label: 'All Users', route: '/admin/users' },
      { label: 'Create User', route: '/admin/users/create' },
    ]
  },
  {
    label: 'Doctors', icon: UserPlusIcon, children: [
      { label: 'All Doctors', route: '/admin/doctors' },
      { label: 'Add Doctor', route: '/admin/doctors/create' },
    ]
  },
  { label: 'Patients', icon: HeartIcon, route: '/admin/patients' },
  { label: 'Appointments', icon: CalendarIcon, route: '/admin/appointments' },
  { label: 'Invoices', icon: CreditCardIcon, route: '/admin/invoices' },
  { label: 'Medical Records', icon: FolderIcon, route: '/admin/medical-records' },
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