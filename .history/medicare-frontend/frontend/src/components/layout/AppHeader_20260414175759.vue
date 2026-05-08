<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useUIStore } from '@/stores/ui'
import { Bars3Icon, UserCircleIcon, ArrowRightOnRectangleIcon, Cog6ToothIcon } from '@heroicons/vue/24/outline'
import NotificationBell from './NotificationBell.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const uiStore = useUIStore()
const dropdownOpen = ref(false)

async function logout() {
  await authStore.logout()
  router.push('/login')
}

function getPageTitle() {
  const name = route.name as string
  const titles: Record<string, string> = {
    'admin-dashboard': 'Dashboard',
    'admin-doctors': 'Doctors',
    'admin-doctor-create': 'Add Doctor',
    'admin-doctor-edit': 'Edit Doctor',
    'admin-patients': 'Patients',
    'admin-users': 'Users',
    'admin-user-create': 'Create User',
    'admin-appointments': 'Appointments',
    'admin-invoices': 'Invoices',
    'admin-medical-records': 'Medical Records',
    'doctor-dashboard': 'Dashboard',
    'doctor-appointments': 'Appointments',
    'doctor-prescriptions': 'Prescriptions',
    'doctor-prescription-create': 'Write Prescription',
    'doctor-schedule': 'My Schedule',
    'patient-dashboard': 'Dashboard',
    'book-appointment': 'Book Appointment',
    'patient-appointments': 'My Appointments',
    'patient-prescriptions': 'My Prescriptions',
    'patient-invoices': 'My Invoices',
    'patient-medical-records': 'Medical Records',
    'receptionist-dashboard': 'Dashboard',
    'receptionist-queue': "Today's Queue",
    'receptionist-invoice-create': 'Generate Invoice',
  }
  return titles[name] || 'MediCare'
}
</script>

<template>
  <header class="flex h-16 items-center justify-between border-b border-slate-100 bg-white px-6 shrink-0">
    <!-- Left: hamburger + title -->
    <div class="flex items-center gap-4">
      <button
        @click="uiStore.toggleSidebar()"
        class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors lg:hidden"
      >
        <Bars3Icon class="h-5 w-5" />
      </button>
      <h1 class="text-lg font-semibold text-slate-800">{{ getPageTitle() }}</h1>
    </div>

    <!-- Right: notifications + user -->
    <div class="flex items-center gap-2">
      <NotificationBell />

      <!-- User dropdown -->
      <div class="relative">
        <button
          @click="dropdownOpen = !dropdownOpen"
          class="flex items-center gap-2.5 rounded-lg px-3 py-1.5 hover:bg-slate-100 transition-colors"
        >
          <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
            <span class="text-sm font-semibold text-primary-600">
              {{ authStore.user?.name?.charAt(0)?.toUpperCase() }}
            </span>
          </div>
          <div class="hidden md:block text-left">
            <p class="text-sm font-medium text-slate-700 leading-none">{{ authStore.user?.name }}</p>
            <p class="text-xs text-slate-400 mt-0.5 capitalize">{{ authStore.user?.role }}</p>
          </div>
        </button>

        <transition name="slide-down">
          <div
            v-if="dropdownOpen"
            class="absolute right-0 top-12 z-50 w-48 rounded-xl border border-slate-100 bg-white shadow-lg py-1"
          >
            <div class="px-4 py-2 border-b border-slate-100">
              <p class="text-sm font-medium text-slate-700">{{ authStore.user?.name }}</p>
              <p class="text-xs text-slate-400">{{ authStore.user?.email }}</p>
            </div>
            <button
              @click="logout(); dropdownOpen = false"
              class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition-colors"
            >
              <ArrowRightOnRectangleIcon class="h-4 w-4" />
              Logout
            </button>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>