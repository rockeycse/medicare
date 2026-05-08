<script setup lang="ts">
import { ref, onMounted } from 'vue'
import dashboardService from '@/services/dashboard.service'
import appointmentService from '@/services/appointment.service'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import type { Appointment } from '@/types/appointment'
import AppCard from '@/components/ui/AppCard.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppSkeleton from '@/components/ui/AppSkeleton.vue'
import { useToast } from '@/composables/useToast'
import {
  CalendarDaysIcon,
  UserGroupIcon,
  BanknotesIcon,
  ClockIcon,
  DocumentPlusIcon,
  CheckCircleIcon,
  XCircleIcon
} from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const authStore = useAuthStore()
const router = useRouter()
const toast = useToast()

const loading = ref(true)
const stats = ref<Record<string, unknown>>({})
const todayAppointments = ref<Appointment[]>([])

onMounted(async () => {
  try {
    const [dashRes, aptRes] = await Promise.all([
      dashboardService.getStats(),
      appointmentService.getAll({
        date: dayjs().format('YYYY-MM-DD'),
        per_page: 20
      })
    ])
    stats.value = dashRes.data || {}
    todayAppointments.value = aptRes.data || []
  } catch {
    toast.error('Failed to load dashboard')
  } finally {
    loading.value = false
  }
})

async function completeApt(id: number) {
  try {
    await appointmentService.complete(id)
    toast.success('Appointment completed')
    const apt = todayAppointments.value.find(a => a.id === id)
    if (apt) apt.status = 'completed'
  } catch {
    toast.error('Failed to update appointment')
  }
}

async function cancelApt(id: number) {
  try {
    await appointmentService.cancel(id)
    toast.success('Appointment cancelled')
    const apt = todayAppointments.value.find(a => a.id === id)
    if (apt) apt.status = 'cancelled'
  } catch {
    toast.error('Failed to cancel appointment')
  }
}

const statCards = [
  { key: 'today_appointments', label: "Today's Appointments", icon: CalendarDaysIcon, color: 'bg-blue-50 text-blue-500' },
  { key: 'total_patients', label: 'Total Patients', icon: UserGroupIcon, color: 'bg-emerald-50 text-emerald-500' },
  { key: 'monthly_revenue', label: 'Monthly Revenue', icon: BanknotesIcon, color: 'bg-purple-50 text-purple-500' },
]
</script>

<template>
  <div class="space-y-6">
    <!-- Welcome -->
    <div class="rounded-2xl bg-gradient-to-r from-primary-600 to-sky-500 p-6 text-white">
      <h1 class="text-2xl font-bold">
        Good {{ dayjs().hour() < 12 ? 'Morning' : dayjs().hour() < 17 ? 'Afternoon' : 'Evening' }},
        Dr. {{ authStore.user?.name }} 👋
      </h1>
      <p class="text-white/80 mt-1 text-sm">{{ dayjs().format('dddd, MMMM D, YYYY') }}</p>
      <AppButton
        variant="ghost"
        class="mt-4 bg-white/20 text-white hover:bg-white/30 border-0"
        @click="router.push('/doctor/prescriptions/create')"
      >
        <DocumentPlusIcon class="h-4 w-4" />
        Write Prescription
      </AppButton>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <AppSkeleton v-if="loading" v-for="i in 3" :key="i" type="card" />
      <AppCard v-else v-for="card in statCards" :key="card.key" class="flex items-center gap-4">
        <div :class="['flex h-12 w-12 shrink-0 items-center justify-center rounded-xl', card.color]">
          <component :is="card.icon" class="h-6 w-6" />
        </div>
        <div>
          <p class="label-text">{{ card.label }}</p>
          <p class="text-2xl font-bold text-slate-800 mt-0.5">
            {{ card.key === 'monthly_revenue' ? '৳' : '' }}{{ stats[card.key] ?? 0 }}
          </p>
        </div>
      </AppCard>
    </div>

    <!-- Today's appointments -->
    <AppCard>
      <div class="flex items-center justify-between mb-6">
        <h3 class="section-title">Today's Appointments</h3>
        <AppButton variant="outline" size="sm" @click="router.push('/doctor/appointments')">
          View All
        </AppButton>
      </div>

      <AppSkeleton v-if="loading" type="table" :rows="4" />

      <div v-else-if="!todayAppointments.length" class="flex flex-col items-center py-12 gap-2">
        <CalendarDaysIcon class="h-12 w-12 text-slate-300" />
        <p class="text-slate-400 text-sm">No appointments today</p>
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="apt in todayAppointments"
          :key="apt.id"
          class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 hover:bg-slate-50 transition-colors"
        >
          <!-- Serial -->
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-600">
            {{ apt.serial_number }}
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <p class="text-sm font-semibold text-slate-700">
                Patient #{{ apt.patient?.id ?? '—' }}
              </p>
              <AppBadge :variant="apt.type">{{ apt.type }}</AppBadge>
            </div>
            <div class="flex items-center gap-2 mt-1 text-xs text-slate-400">
              <ClockIcon class="h-3.5 w-3.5" />
              {{ apt.appointment_time }}
              <span v-if="apt.symptoms" class="truncate max-w-xs">• {{ apt.symptoms }}</span>
            </div>
          </div>

          <!-- Status + actions -->
          <div class="flex items-center gap-2 shrink-0">
            <AppBadge :variant="apt.status">{{ apt.status }}</AppBadge>
            <template v-if="apt.status === 'confirmed' || apt.status === 'pending'">
              <button
                @click="completeApt(apt.id)"
                title="Mark complete"
                class="flex h-8 w-8 items-center justify-center rounded-lg text-emerald-500 hover:bg-emerald-50 transition-colors"
              >
                <CheckCircleIcon class="h-5 w-5" />
              </button>
              <button
                @click="cancelApt(apt.id)"
                title="Cancel"
                class="flex h-8 w-8 items-center justify-center rounded-lg text-red-400 hover:bg-red-50 transition-colors"
              >
                <XCircleIcon class="h-5 w-5" />
              </button>
            </template>
            <AppButton
              v-if="apt.status === 'completed' && !apt.prescription"
              size="xs"
              variant="outline"
              @click="router.push(`/doctor/prescriptions/create?appointment_id=${apt.id}`)"
            >
              Prescribe
            </AppButton>
          </div>
        </div>
      </div>
    </AppCard>
  </div>
</template>