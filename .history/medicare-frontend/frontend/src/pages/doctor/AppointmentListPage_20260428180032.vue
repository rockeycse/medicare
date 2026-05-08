<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
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
import { useRouter } from 'vue-router'
import {
  CalendarDaysIcon, ClockIcon, CheckCircleIcon,
  XCircleIcon, EyeIcon, DocumentPlusIcon
} from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const route = useRoute()
const router = useRouter()
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

async function fetchAppointments() {
  loading.value = true
  try {
    const params: Record<string, unknown> = {
      page: currentPage.value,
      per_page: 12,
    }
    if (statusFilter.value) params.status = statusFilter.value
    if (route.query.filter === 'today') params.date = dayjs().format('YYYY-MM-DD')

    const res = await appointmentService.getAll(params)
    appointments.value = res.data || []
    if (res.meta) { lastPage.value = res.meta.last_page; total.value = res.meta.total }
  } finally {
    loading.value = false
  }
}

onMounted(fetchAppointments)
watch([statusFilter], () => { currentPage.value = 1; fetchAppointments() })

async function completeApt(id: number) {
  try {
    await appointmentService.complete(id)
    toast.success('Marked as completed')
    fetchAppointments()
  } catch { toast.error('Failed') }
}

async function cancelApt(id: number) {
  try {
    await appointmentService.cancel(id)
    toast.success('Appointment cancelled')
    fetchAppointments()
  } catch { toast.error('Failed') }
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="page-title">Appointments</h1>
        <p class="text-sm text-slate-500 mt-1">
          {{ route.query.filter === 'today' ? "Today's appointments" : 'All your appointments' }}
        </p>
      </div>
    </div>

    <AppCard>
      <!-- Filter -->
      <div class="mb-5 flex flex-wrap gap-3">
        <div class="w-44">
          <AppSelect v-model="statusFilter" :options="statusOptions" placeholder="All Status" />
        </div>
      </div>

      <AppSkeleton v-if="loading" type="table" :rows="6" />

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
            <div class="flex items-center gap-2 flex-wrap">
              <p class="text-sm font-semibold text-slate-700">Patient #{{ apt.patient?.id ?? '—' }}</p>
              <AppBadge :variant="apt.type">{{ apt.type }}</AppBadge>
            </div>
            <div class="flex items-center gap-3 mt-1 text-xs text-slate-400">
              <span class="flex items-center gap-1">
                <CalendarDaysIcon class="h-3.5 w-3.5" />
                {{ dayjs(apt.appointment_date).format('MMM D, YYYY') }}
              </span>
              <span class="flex items-center gap-1">
                <ClockIcon class="h-3.5 w-3.5" />
                {{ apt.appointment_time }}
              </span>
            </div>
            <p v-if="apt.symptoms" class="text-xs text-slate-400 mt-0.5 truncate">{{ apt.symptoms }}</p>
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <AppBadge :variant="apt.status">{{ apt.status }}</AppBadge>

            <button
              @click="selected = apt; viewModal = true"
              class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 transition-colors"
            >
              <EyeIcon class="h-4 w-4" />
            </button>

            <template v-if="apt.status === 'confirmed' || apt.status === 'pending'">
              <button
                @click="completeApt(apt.id)"
                class="flex h-8 w-8 items-center justify-center rounded-lg text-emerald-500 hover:bg-emerald-50 transition-colors"
                title="Complete"
              >
                <CheckCircleIcon class="h-5 w-5" />
              </button>
              <button
                @click="cancelApt(apt.id)"
                class="flex h-8 w-8 items-center justify-center rounded-lg text-red-400 hover:bg-red-50 transition-colors"
                title="Cancel"
              >
                <XCircleIcon class="h-5 w-5" />
              </button>
            </template>

            <AppButton
              v-if="apt.status === 'completed' && !apt.prescription"
              size="xs"
              variant="primary"
              @click="router.push(`/doctor/prescriptions/create?appointment_id=${apt.id}`)"
            >
              <DocumentPlusIcon class="h-3.5 w-3.5" />
              Prescribe
            </AppButton>
          </div>
        </div>
      </div>

      <AppPagination
        v-if="lastPage > 1"
        :current-page="currentPage"
        :last-page="lastPage"
        :total="total"
        @page-change="(p) => { currentPage = p; fetchAppointments() }"
      />
    </AppCard>

    <!-- View detail modal -->
    <AppModal v-model="viewModal" title="Appointment Details" size="md">
      <div v-if="selected" class="space-y-4 text-sm">
        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-lg bg-slate-50 p-3">
            <p class="label-text mb-1">Date</p>
            <p class="font-medium text-slate-700">{{ dayjs(selected.appointment_date).format('MMM D, YYYY') }}</p>
          </div>
          <div class="rounded-lg bg-slate-50 p-3">
            <p class="label-text mb-1">Time</p>
            <p class="font-medium text-slate-700">{{ selected.appointment_time }}</p>
          </div>
          <div class="rounded-lg bg-slate-50 p-3">
            <p class="label-text mb-1">Type</p>
            <AppBadge :variant="selected.type">{{ selected.type }}</AppBadge>
          </div>
          <div class="rounded-lg bg-slate-50 p-3">
            <p class="label-text mb-1">Status</p>
            <AppBadge :variant="selected.status">{{ selected.status }}</AppBadge>
          </div>
        </div>
        <div v-if="selected.symptoms" class="rounded-lg bg-slate-50 p-3">
          <p class="label-text mb-1">Symptoms</p>
          <p class="text-slate-600">{{ selected.symptoms }}</p>
        </div>
        <div v-if="selected.prescription" class="rounded-lg bg-emerald-50 border border-emerald-100 p-3">
          <p class="label-text mb-1 text-emerald-700">Prescription</p>
          <p class="text-emerald-700 font-medium">{{ selected.prescription.diagnosis }}</p>
        </div>
      </div>
    </AppModal>
  </div>
</template>