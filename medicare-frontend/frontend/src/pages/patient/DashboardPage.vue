<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import dashboardService from '@/services/dashboard.service'
import invoiceService from '@/services/invoice.service'
import type { Appointment } from '@/types/appointment'
import type { Invoice } from '@/types/invoice'
import AppCard from '@/components/ui/AppCard.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppAlert from '@/components/ui/AppAlert.vue'
import AppSkeleton from '@/components/ui/AppSkeleton.vue'
import {
  CalendarDaysIcon, ClockIcon, PlusCircleIcon,
  CreditCardIcon, CheckCircleIcon
} from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const router = useRouter()
const authStore = useAuthStore()
const loading = ref(true)
const stats = ref<Record<string, unknown>>({})
const upcoming = ref<Appointment[]>([])
const unpaidInvoices = ref<Invoice[]>([])

onMounted(async () => {
  try {
    const [dashRes, invoiceRes] = await Promise.all([
      dashboardService.getStats(),
      invoiceService.getAll({ status: 'unpaid', per_page: 5 })
    ])
    stats.value = dashRes.data || {}
    upcoming.value = (dashRes.data?.upcoming_appointments as Appointment[]) || []
    unpaidInvoices.value = invoiceRes.data || []
  } finally {
    loading.value = false
  }
})

const nextAppointment = computed(() => upcoming.value[0] ?? null)

const statCards = [
  { key: 'total_appointments', label: 'Total Appointments', icon: CalendarDaysIcon, color: 'bg-blue-50 text-blue-500' },
  { key: 'completed_appointments', label: 'Completed', icon: CheckCircleIcon, color: 'bg-emerald-50 text-emerald-500' },
  { key: 'upcoming_appointments', label: 'Upcoming', icon: ClockIcon, color: 'bg-amber-50 text-amber-500' },
]
</script>

<template>
  <div class="space-y-6">
    <!-- Welcome -->
    <div class="rounded-2xl bg-gradient-to-r from-primary-600 to-sky-500 p-6 text-white">
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-2xl font-bold">Welcome back, {{ authStore.user?.name }}! 👋</h1>
          <p class="text-white/80 mt-1 text-sm">{{ dayjs().format('dddd, MMMM D, YYYY') }}</p>
        </div>
        <AppButton
          variant="ghost"
          class="bg-white/20 text-white hover:bg-white/30 border-0 shrink-0"
          @click="router.push('/patient/book-appointment')"
        >
          <PlusCircleIcon class="h-4 w-4" />
          Book Appointment
        </AppButton>
      </div>
    </div>

    <!-- Unpaid invoices alert -->
    <AppAlert v-if="unpaidInvoices.length" type="warning" title="Pending Payments">
      You have {{ unpaidInvoices.length }} unpaid invoice{{ unpaidInvoices.length > 1 ? 's' : '' }}.
      <router-link to="/patient/invoices" class="ml-2 font-semibold underline">View Invoices →</router-link>
    </AppAlert>

    <!-- Next appointment -->
    <AppSkeleton v-if="loading" type="card" :lines="3" />
    <AppCard v-else-if="nextAppointment" class="border-l-4 border-primary-500">
      <div class="flex items-center gap-2 mb-3">
        <CalendarDaysIcon class="h-5 w-5 text-primary-500" />
        <h3 class="section-title">Next Appointment</h3>
      </div>
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
          <p class="text-lg font-bold text-slate-800">
            Dr. {{ nextAppointment.doctor?.user?.name ?? '—' }}
          </p>
          <p class="text-sm text-primary-600">{{ nextAppointment.doctor?.specialization?.name }}</p>
          <div class="flex items-center gap-3 mt-2 text-sm text-slate-500">
            <span class="flex items-center gap-1">
              <CalendarDaysIcon class="h-4 w-4" />
              {{ dayjs(nextAppointment.appointment_date).format('MMMM D, YYYY') }}
            </span>
            <span class="flex items-center gap-1">
              <ClockIcon class="h-4 w-4" />
              {{ nextAppointment.appointment_time }}
            </span>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <AppBadge :variant="nextAppointment.type">{{ nextAppointment.type }}</AppBadge>
          <AppBadge :variant="nextAppointment.status">{{ nextAppointment.status }}</AppBadge>
        </div>
      </div>
    </AppCard>

    <!-- Stat cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <AppSkeleton v-if="loading" v-for="i in 3" :key="i" type="card" />
      <AppCard v-else v-for="card in statCards" :key="card.key" class="flex items-center gap-4">
        <div :class="['flex h-12 w-12 shrink-0 items-center justify-center rounded-xl', card.color]">
          <component :is="card.icon" class="h-6 w-6" />
        </div>
        <div>
          <p class="label-text">{{ card.label }}</p>
          <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ stats[card.key] ?? 0 }}</p>
        </div>
      </AppCard>
    </div>

    <!-- Upcoming list -->
    <AppCard>
      <div class="flex items-center justify-between mb-5">
        <h3 class="section-title">Upcoming Appointments</h3>
        <router-link to="/patient/appointments">
          <AppButton variant="outline" size="sm">View All</AppButton>
        </router-link>
      </div>

      <AppSkeleton v-if="loading" type="table" :rows="3" />

      <div v-else-if="!upcoming.length" class="flex flex-col items-center py-10 gap-3">
        <CalendarDaysIcon class="h-10 w-10 text-slate-300" />
        <p class="text-sm text-slate-400">No upcoming appointments</p>
        <AppButton variant="primary" size="sm" @click="router.push('/patient/book-appointment')">
          Book Now
        </AppButton>
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="apt in upcoming.slice(0, 4)"
          :key="apt.id"
          class="flex items-center gap-4 rounded-xl bg-slate-50 p-4"
        >
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-600">
            {{ apt.serial_number }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-700">Dr. {{ apt.doctor?.user?.name ?? '—' }}</p>
            <p class="text-xs text-slate-400">
              {{ dayjs(apt.appointment_date).format('MMM D') }} at {{ apt.appointment_time }}
            </p>
          </div>
          <AppBadge :variant="apt.status">{{ apt.status }}</AppBadge>
        </div>
      </div>
    </AppCard>
  </div>
</template>