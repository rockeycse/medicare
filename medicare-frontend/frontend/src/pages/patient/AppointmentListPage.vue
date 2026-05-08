<script setup lang="ts">
import { ref, onMounted } from 'vue'
import appointmentService from '@/services/appointment.service'
import type { Appointment } from '@/types/appointment'
import AppCard from '@/components/ui/AppCard.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppModal from '@/components/ui/AppModal.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import AppSkeleton from '@/components/ui/AppSkeleton.vue'
import AppSelect from '@/components/ui/AppSelect.vue'
import { useToast } from '@/composables/useToast'
import { CalendarDaysIcon, ClockIcon, EyeIcon, XCircleIcon } from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const toast = useToast()
const appointments = ref<Appointment[]>([])
const loading = ref(true)
const statusFilter = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const viewModal = ref(false)
const selected = ref<Appointment | null>(null)

const statusOptions = [
  { value: '', label: 'All Status' },
  { value: 'pending', label: 'Pending' },
  { value: 'confirmed', label: 'Confirmed' },
  { value: 'completed', label: 'Completed' },
  { value: 'cancelled', label: 'Cancelled' },
]

async function fetch() {
  loading.value = true
  try {
    const params: Record<string, unknown> = { page: currentPage.value, per_page: 10 }
    if (statusFilter.value) params.status = statusFilter.value
    const res = await appointmentService.getAll(params)
    appointments.value = res.data || []
    if (res.meta) { lastPage.value = res.meta.last_page; total.value = res.meta.total }
  } finally { loading.value = false }
}

onMounted(fetch)

async function cancelApt(id: number) {
  if (!confirm('Cancel this appointment?')) return
  try {
    await appointmentService.cancel(id)
    toast.success('Appointment cancelled')
    fetch()
  } catch { toast.error('Failed to cancel') }
}
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="page-title">My Appointments</h1>
      <p class="text-sm text-slate-500 mt-1">View and manage your appointments</p>
    </div>

    <AppCard>
      <div class="mb-4 w-44">
        <AppSelect v-model="statusFilter" :options="statusOptions" @update:model-value="() => { currentPage = 1; fetch() }" />
      </div>

      <AppSkeleton v-if="loading" type="table" :rows="5" />

      <div v-else-if="!appointments.length" class="flex flex-col items-center py-16 gap-3">
        <CalendarDaysIcon class="h-12 w-12 text-slate-300" />
        <p class="text-slate-400 text-sm">No appointments found</p>
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="apt in appointments"
          :key="apt.id"
          class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 hover:bg-slate-50 transition-colors"
        >
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-600">
            {{ apt.serial_number }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-700">Dr. {{ apt.doctor?.user?.name ?? '—' }}</p>
            <p class="text-xs text-primary-600">{{ apt.doctor?.specialization?.name }}</p>
            <div class="flex items-center gap-3 mt-1 text-xs text-slate-400">
              <span class="flex items-center gap-1"><CalendarDaysIcon class="h-3.5 w-3.5" />{{ dayjs(apt.appointment_date).format('MMM D, YYYY') }}</span>
              <span class="flex items-center gap-1"><ClockIcon class="h-3.5 w-3.5" />{{ apt.appointment_time }}</span>
            </div>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <AppBadge :variant="apt.type">{{ apt.type }}</AppBadge>
            <AppBadge :variant="apt.status">{{ apt.status }}</AppBadge>
            <button @click="selected = apt; viewModal = true" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
              <EyeIcon class="h-4 w-4" />
            </button>
            <button
              v-if="apt.status === 'pending' || apt.status === 'confirmed'"
              @click="cancelApt(apt.id)"
              class="flex h-8 w-8 items-center justify-center rounded-lg text-red-400 hover:bg-red-50 transition-colors"
            >
              <XCircleIcon class="h-4 w-4" />
            </button>
          </div>
        </div>
      </div>

      <AppPagination v-if="lastPage > 1" :current-page="currentPage" :last-page="lastPage" :total="total" @page-change="(p) => { currentPage = p; fetch() }" />
    </AppCard>

    <AppModal v-model="viewModal" title="Appointment Details" size="md">
      <div v-if="selected" class="space-y-3 text-sm">
        <div class="grid grid-cols-2 gap-3">
          <div class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Doctor</p><p class="font-medium">Dr. {{ selected.doctor?.user?.name }}</p></div>
          <div class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Specialization</p><p class="font-medium">{{ selected.doctor?.specialization?.name }}</p></div>
          <div class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Date</p><p class="font-medium">{{ dayjs(selected.appointment_date).format('MMM D, YYYY') }}</p></div>
          <div class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Time</p><p class="font-medium">{{ selected.appointment_time }}</p></div>
          <div class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Type</p><AppBadge :variant="selected.type">{{ selected.type }}</AppBadge></div>
          <div class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Status</p><AppBadge :variant="selected.status">{{ selected.status }}</AppBadge></div>
        </div>
        <div v-if="selected.symptoms" class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Symptoms</p><p class="text-slate-600">{{ selected.symptoms }}</p></div>
      </div>
    </AppModal>
  </div>
</template>