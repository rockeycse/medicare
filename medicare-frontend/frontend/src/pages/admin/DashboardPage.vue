<script setup lang="ts">
import { ref, onMounted } from 'vue'
import dashboardService from '@/services/dashboard.service'
import AppCard from '@/components/ui/AppCard.vue'
import AppSkeleton from '@/components/ui/AppSkeleton.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppointmentChart from '@/components/charts/AppointmentChart.vue'
import RevenueChart from '@/components/charts/RevenueChart.vue'
import {
  UsersIcon, UserPlusIcon, HeartIcon, CalendarIcon,
  BanknotesIcon, ArrowTrendingUpIcon
} from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const loading = ref(true)
const stats = ref<Record<string, unknown>>({})

onMounted(async () => {
  try {
    const res = await dashboardService.getStats()
    stats.value = res.data || {}
  } finally {
    loading.value = false
  }
})

const statCards = [
  { key: 'total_users', label: 'Total Users', icon: UsersIcon, color: 'bg-blue-50 text-blue-500' },
  { key: 'total_doctors', label: 'Total Doctors', icon: UserPlusIcon, color: 'bg-emerald-50 text-emerald-500' },
  { key: 'total_patients', label: 'Total Patients', icon: HeartIcon, color: 'bg-purple-50 text-purple-500' },
  { key: 'total_appointments', label: 'Appointments', icon: CalendarIcon, color: 'bg-orange-50 text-orange-500' },
]

const revenueCards = [
  { key: 'today_revenue', label: "Today's Revenue", color: 'border-l-4 border-emerald-500' },
  { key: 'monthly_revenue', label: 'Monthly Revenue', color: 'border-l-4 border-blue-500' },
  { key: 'total_revenue', label: 'Total Revenue', color: 'border-l-4 border-purple-500' },
]

function formatCurrency(val: unknown) {
  const num = Number(val) || 0
  return '৳' + num.toLocaleString()
}
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="page-title">Dashboard</h1>
      <p class="text-sm text-slate-500 mt-1">Welcome back! Here's what's happening today.</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
      <AppSkeleton v-if="loading" v-for="i in 4" :key="i" type="card" />
      <AppCard v-else v-for="card in statCards" :key="card.key" class="flex items-center gap-4">
        <div :class="['flex h-12 w-12 shrink-0 items-center justify-center rounded-xl', card.color]">
          <component :is="card.icon" class="h-6 w-6" />
        </div>
        <div>
          <p class="label-text">{{ card.label }}</p>
          <p class="text-2xl font-bold text-slate-800 mt-0.5">
            {{ (stats[card.key] as number | undefined) ?? 0 }}
          </p>
        </div>
      </AppCard>
    </div>

    <!-- Revenue Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <AppSkeleton v-if="loading" v-for="i in 3" :key="i" type="card" />
      <AppCard v-else v-for="card in revenueCards" :key="card.key" :class="card.color">
        <p class="label-text">{{ card.label }}</p>
        <p class="text-2xl font-bold text-slate-800 mt-1">{{ formatCurrency(stats[card.key]) }}</p>
        <div class="flex items-center gap-1 mt-2">
          <ArrowTrendingUpIcon class="h-3.5 w-3.5 text-emerald-500" />
          <span class="text-xs text-emerald-600 font-medium">This period</span>
        </div>
      </AppCard>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
      <AppCard class="xl:col-span-3">
        <h3 class="section-title mb-4">Monthly Appointments</h3>
        <AppSkeleton v-if="loading" type="card" :lines="5" />
        <AppointmentChart
          v-else
          :data="(stats.monthly_appointments as { month: string; count: number }[]) || []"
        />
      </AppCard>
      <AppCard class="xl:col-span-2">
        <h3 class="section-title mb-4">Appointment Status</h3>
        <AppSkeleton v-if="loading" type="card" :lines="5" />
        <RevenueChart
          v-else
          :data="(stats.appointment_status as { status: string; count: number }[]) || []"
        />
      </AppCard>
    </div>

    <!-- Bottom tables -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
      <!-- Top Doctors -->
      <AppCard>
        <h3 class="section-title mb-4">Top Doctors</h3>
        <AppSkeleton v-if="loading" type="table" :rows="4" />
        <div v-else class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="border-b border-slate-100">
                <th class="pb-3 text-left label-text">Doctor</th>
                <th class="pb-3 text-left label-text">Specialization</th>
                <th class="pb-3 text-right label-text">Appointments</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr v-for="doc in ((stats.top_doctors as unknown[]) || [])" :key="(doc as Record<string, unknown>).id as number" class="hover:bg-slate-50">
                <td class="py-3 text-sm font-medium text-slate-700">{{ (doc as Record<string, unknown>).name }}</td>
                <td class="py-3 text-sm text-slate-500">{{ (doc as Record<string, unknown>).specialization }}</td>
                <td class="py-3 text-sm text-right font-semibold text-primary-600">{{ (doc as Record<string, unknown>).appointments_count }}</td>
              </tr>
            </tbody>
          </table>
          <p v-if="!((stats.top_doctors as unknown[]) || []).length" class="text-center text-sm text-slate-400 py-8">No data</p>
        </div>
      </AppCard>

      <!-- Recent Appointments -->
      <AppCard>
        <h3 class="section-title mb-4">Recent Appointments</h3>
        <AppSkeleton v-if="loading" type="table" :rows="4" />
        <div v-else class="space-y-3">
          <div
            v-for="apt in ((stats.recent_appointments as unknown[]) || []).slice(0, 5)"
            :key="(apt as Record<string, unknown>).id as number"
            class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 hover:bg-slate-100 transition-colors"
          >
            <div class="h-9 w-9 rounded-full bg-primary-100 flex items-center justify-center text-sm font-semibold text-primary-600 shrink-0">
              {{ ((apt as Record<string, unknown>).patient_name as string)?.charAt(0) }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-slate-700 truncate">{{ (apt as Record<string, unknown>).patient_name }}</p>
              <p class="text-xs text-slate-400">{{ (apt as Record<string, unknown>).doctor_name }} • {{ dayjs((apt as Record<string, unknown>).appointment_date as string).format('MMM D') }}</p>
            </div>
            <AppBadge :variant="(apt as Record<string, unknown>).status as string">{{ (apt as Record<string, unknown>).status }}</AppBadge>
          </div>
          <p v-if="!((stats.recent_appointments as unknown[]) || []).length" class="text-center text-sm text-slate-400 py-8">No recent appointments</p>
        </div>
      </AppCard>
    </div>
  </div>
</template>