<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import doctorService from '@/services/doctor.service'
import appointmentService from '@/services/appointment.service'
import type { Doctor, DoctorSchedule } from '@/types/doctor'
import AppButton from '@/components/ui/AppButton.vue'
import AppCard from '@/components/ui/AppCard.vue'
import AppTextarea from '@/components/ui/AppTextarea.vue'
import AppAlert from '@/components/ui/AppAlert.vue'
import { useToast } from '@/composables/useToast'
import {
  ChevronRightIcon, ChevronLeftIcon, CheckIcon,
  CalendarDaysIcon, ClockIcon, StarIcon
} from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const router = useRouter()
const route = useRoute()
const toast = useToast()

const step = ref(1)
const loading = ref(false)
const loadingDoctors = ref(true)
const booked = ref(false)
const errorMsg = ref('')

const doctors = ref<Doctor[]>([])
const selectedDoctor = ref<Doctor | null>(null)
const selectedSchedule = ref<DoctorSchedule | null>(null)
const selectedDate = ref('')
const selectedTime = ref('')
const aptType = ref<'in-person' | 'online'>('in-person')
const symptoms = ref('')

const dayMap: Record<string, number> = {
  sunday: 0, monday: 1, tuesday: 2, wednesday: 3,
  thursday: 4, friday: 5, saturday: 6
}

onMounted(async () => {
  try {
    const res = await doctorService.getAll({ per_page: 100 })
    doctors.value = (res.data || []).filter((d: Doctor) => d.is_available)
    if (route.query.doctor_id) {
      const found = doctors.value.find(d => d.id === Number(route.query.doctor_id))
      if (found) { selectDoctor(found); step.value = 3 }
    }
  } finally {
    loadingDoctors.value = false
  }
})

const specializations = computed(() => {
  const map = new Map<number, string>()
  doctors.value.forEach(d => {
    if (d.specialization) map.set(d.specialization.id, d.specialization.name)
  })
  return [...map.entries()].map(([id, name]) => ({ id, name }))
})

const selectedSpecId = ref<number | null>(null)

const filteredDoctors = computed(() =>
  selectedSpecId.value
    ? doctors.value.filter(d => d.specialization?.id === selectedSpecId.value)
    : doctors.value
)

function selectDoctor(doc: Doctor) {
  selectedDoctor.value = doc
  selectedSchedule.value = null
  selectedDate.value = ''
  selectedTime.value = ''
}

function selectSchedule(sch: DoctorSchedule) {
  selectedSchedule.value = sch
  selectedDate.value = ''
  selectedTime.value = ''
}

const availableDates = computed(() => {
  if (!selectedSchedule.value) return []
  const dayNum = dayMap[selectedSchedule.value.day_of_week]
  const dates: string[] = []
  let cur = dayjs()
  for (let i = 0; i < 30; i++) {
    if (cur.day() === dayNum) dates.push(cur.format('YYYY-MM-DD'))
    cur = cur.add(1, 'day')
  }
  return dates
})

const timeSlots = computed(() => {
  if (!selectedSchedule.value || !selectedDate.value) return []
  const slots: string[] = []
  let cur = dayjs(`2000-01-01 ${selectedSchedule.value.start_time}`)
  const end = dayjs(`2000-01-01 ${selectedSchedule.value.end_time}`)
  while (cur.isBefore(end)) {
    slots.push(cur.format('HH:mm'))
    cur = cur.add(30, 'minute')
  }
  return slots
})

const steps = [
  { num: 1, label: 'Specialization' },
  { num: 2, label: 'Doctor' },
  { num: 3, label: 'Schedule' },
  { num: 4, label: 'Time' },
  { num: 5, label: 'Details' },
  { num: 6, label: 'Confirm' },
]

function canProceed() {
  if (step.value === 1) return selectedSpecId.value !== null
  if (step.value === 2) return selectedDoctor.value !== null
  if (step.value === 3) return selectedSchedule.value !== null && selectedDate.value !== ''
  if (step.value === 4) return selectedTime.value !== ''
  return true
}

async function handleBook() {
  if (!selectedDoctor.value || !selectedDate.value || !selectedTime.value) return
  loading.value = true
  errorMsg.value = ''
  try {
    await appointmentService.book({
      doctor_id: selectedDoctor.value.id,
      appointment_date: selectedDate.value,
      appointment_time: selectedTime.value,
      type: aptType.value,
      symptoms: symptoms.value || undefined,
    })
    booked.value = true
    toast.success('Appointment booked successfully!')
  } catch (err: unknown) {
    const e = err as { response?: { data?: { message?: string } } }
    errorMsg.value = e.response?.data?.message || 'Booking failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="space-y-6 max-w-3xl mx-auto">
    <div>
      <h1 class="page-title">Book Appointment</h1>
      <p class="text-sm text-slate-500 mt-1">Follow the steps to book your appointment</p>
    </div>

    <!-- Success -->
    <AppCard v-if="booked" class="text-center py-12">
      <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100">
        <CheckIcon class="h-10 w-10 text-emerald-500" />
      </div>
      <h2 class="text-2xl font-bold text-slate-800 mb-2">Appointment Booked!</h2>
      <p class="text-slate-500 mb-2">Your appointment has been confirmed.</p>
      <div class="mx-auto max-w-xs bg-slate-50 rounded-xl p-4 text-sm text-slate-600 mb-6 space-y-1">
        <p><span class="font-medium">Doctor:</span> Dr. {{ selectedDoctor?.user.name }}</p>
        <p><span class="font-medium">Date:</span> {{ dayjs(selectedDate).format('MMMM D, YYYY') }}</p>
        <p><span class="font-medium">Time:</span> {{ selectedTime }}</p>
        <p><span class="font-medium">Type:</span> {{ aptType }}</p>
      </div>
      <div class="flex gap-3 justify-center">
        <AppButton variant="outline" @click="router.push('/patient/appointments')">My Appointments</AppButton>
        <AppButton variant="primary" @click="router.push('/patient/dashboard')">Dashboard</AppButton>
      </div>
    </AppCard>

    <template v-else>
      <!-- Step indicator -->
      <div class="flex items-center gap-1 overflow-x-auto pb-1">
        <template v-for="(s, idx) in steps" :key="s.num">
          <div class="flex items-center gap-1 shrink-0">
            <div :class="[
              'flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold transition-all',
              step === s.num ? 'bg-primary-500 text-white shadow-md' :
              step > s.num ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400'
            ]">
              <CheckIcon v-if="step > s.num" class="h-4 w-4" />
              <span v-else>{{ s.num }}</span>
            </div>
            <span :class="['text-xs font-medium hidden sm:block', step === s.num ? 'text-primary-600' : 'text-slate-400']">
              {{ s.label }}
            </span>
          </div>
          <div v-if="idx < steps.length - 1" class="flex-1 h-0.5 bg-slate-200 min-w-[12px]">
            <div :class="['h-full bg-primary-400 transition-all', step > s.num ? 'w-full' : 'w-0']" />
          </div>
        </template>
      </div>

      <AppAlert v-if="errorMsg" type="error">{{ errorMsg }}</AppAlert>

      <!-- Step 1: Specialization -->
      <AppCard v-if="step === 1">
        <h3 class="section-title mb-5">Select Specialization</h3>
        <div v-if="loadingDoctors" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <div v-for="i in 6" :key="i" class="h-20 bg-slate-100 rounded-xl animate-pulse" />
        </div>
        <div v-else class="grid grid-cols-2 sm:grid-cols-3 gap-3">
          <button
            v-for="spec in specializations"
            :key="spec.id"
            @click="selectedSpecId = spec.id"
            :class="[
              'rounded-xl border-2 p-4 text-center text-sm font-medium transition-all',
              selectedSpecId === spec.id
                ? 'border-primary-500 bg-primary-50 text-primary-700'
                : 'border-slate-100 hover:border-primary-200 hover:bg-primary-50/50 text-slate-600'
            ]"
          >
            <div class="text-2xl mb-1">🏥</div>
            {{ spec.name }}
          </button>
        </div>
      </AppCard>

      <!-- Step 2: Doctor -->
      <AppCard v-else-if="step === 2">
        <h3 class="section-title mb-5">Select Doctor</h3>
        <div class="space-y-3">
          <div
            v-for="doc in filteredDoctors"
            :key="doc.id"
            @click="selectDoctor(doc)"
            :class="[
              'flex items-center gap-4 rounded-xl border-2 p-4 cursor-pointer transition-all',
              selectedDoctor?.id === doc.id
                ? 'border-primary-500 bg-primary-50'
                : 'border-slate-100 hover:border-primary-200'
            ]"
          >
            <div class="h-14 w-14 shrink-0 rounded-full bg-primary-100 flex items-center justify-center text-xl font-bold text-primary-600">
              {{ doc.user.name.charAt(0) }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-semibold text-slate-800">Dr. {{ doc.user.name }}</p>
              <p class="text-sm text-primary-600">{{ doc.specialization?.name }}</p>
              <div class="flex items-center gap-3 text-xs text-slate-400 mt-1">
                <span>{{ doc.experience_years }} yrs exp</span>
                <span>৳{{ doc.consultation_fee }} fee</span>
              </div>
            </div>
            <div class="flex flex-col items-end gap-1">
              <div class="flex gap-0.5">
                <StarIcon v-for="i in 5" :key="i" class="h-3.5 w-3.5 text-amber-400 fill-amber-400" />
              </div>
              <div v-if="selectedDoctor?.id === doc.id" class="flex h-6 w-6 items-center justify-center rounded-full bg-primary-500">
                <CheckIcon class="h-3.5 w-3.5 text-white" />
              </div>
            </div>
          </div>
        </div>
      </AppCard>

      <!-- Step 3: Schedule & Date -->
      <AppCard v-else-if="step === 3">
        <h3 class="section-title mb-5">Select Day & Date</h3>
        <div class="space-y-4">
          <div>
            <p class="label-text mb-3">Available Days</p>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="sch in (selectedDoctor?.schedules || [])"
                :key="sch.id"
                @click="selectSchedule(sch)"
                :disabled="!sch.is_available"
                :class="[
                  'rounded-xl border-2 px-4 py-2.5 text-sm font-medium transition-all capitalize',
                  selectedSchedule?.id === sch.id
                    ? 'border-primary-500 bg-primary-50 text-primary-700'
                    : sch.is_available
                    ? 'border-slate-200 hover:border-primary-300 text-slate-600'
                    : 'border-slate-100 text-slate-300 cursor-not-allowed'
                ]"
              >
                {{ sch.day_of_week }}
              </button>
            </div>
          </div>

          <div v-if="selectedSchedule">
            <p class="label-text mb-3">Select Date</p>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="date in availableDates.slice(0, 8)"
                :key="date"
                @click="selectedDate = date"
                :class="[
                  'rounded-xl border-2 px-3 py-2 text-xs font-medium transition-all',
                  selectedDate === date
                    ? 'border-primary-500 bg-primary-500 text-white'
                    : 'border-slate-200 hover:border-primary-300 text-slate-600'
                ]"
              >
                <div>{{ dayjs(date).format('MMM D') }}</div>
                <div class="opacity-70">{{ dayjs(date).format('ddd') }}</div>
              </button>
            </div>
          </div>
        </div>
      </AppCard>

      <!-- Step 4: Time -->
      <AppCard v-else-if="step === 4">
        <h3 class="section-title mb-5">Select Time Slot</h3>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="slot in timeSlots"
            :key="slot"
            @click="selectedTime = slot"
            :class="[
              'flex items-center gap-1.5 rounded-xl border-2 px-4 py-2.5 text-sm font-medium transition-all',
              selectedTime === slot
                ? 'border-primary-500 bg-primary-500 text-white'
                : 'border-slate-200 hover:border-primary-300 text-slate-600'
            ]"
          >
            <ClockIcon class="h-3.5 w-3.5" />
            {{ slot }}
          </button>
        </div>
      </AppCard>

      <!-- Step 5: Details -->
      <AppCard v-else-if="step === 5">
        <h3 class="section-title mb-5">Appointment Details</h3>
        <div class="space-y-5">
          <div>
            <p class="label-text mb-3">Appointment Type</p>
            <div class="grid grid-cols-2 gap-3">
              <button
                v-for="type in ['in-person', 'online']"
                :key="type"
                @click="aptType = type as 'in-person' | 'online'"
                :class="[
                  'rounded-xl border-2 py-4 text-sm font-medium capitalize transition-all',
                  aptType === type
                    ? 'border-primary-500 bg-primary-50 text-primary-700'
                    : 'border-slate-200 hover:border-primary-200 text-slate-600'
                ]"
              >
                {{ type === 'in-person' ? '🏥 In-Person' : '💻 Online' }}
              </button>
            </div>
          </div>
          <AppTextarea
            v-model="symptoms"
            label="Symptoms (Optional)"
            placeholder="Describe your symptoms or reason for visit..."
            :rows="4"
          />
        </div>
      </AppCard>

      <!-- Step 6: Confirm -->
      <AppCard v-else-if="step === 6">
        <h3 class="section-title mb-5">Confirm Appointment</h3>
        <div class="space-y-3 text-sm">
          <div class="rounded-xl bg-primary-50 border border-primary-100 p-5 space-y-3">
            <div class="flex items-center gap-3 pb-3 border-b border-primary-100">
              <div class="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center text-lg font-bold text-primary-600">
                {{ selectedDoctor?.user.name.charAt(0) }}
              </div>
              <div>
                <p class="font-bold text-slate-800">Dr. {{ selectedDoctor?.user.name }}</p>
                <p class="text-primary-600">{{ selectedDoctor?.specialization?.name }}</p>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div><p class="label-text">Date</p><p class="font-medium text-slate-700 mt-0.5">{{ dayjs(selectedDate).format('MMMM D, YYYY') }}</p></div>
              <div><p class="label-text">Time</p><p class="font-medium text-slate-700 mt-0.5">{{ selectedTime }}</p></div>
              <div><p class="label-text">Type</p><p class="font-medium text-slate-700 mt-0.5 capitalize">{{ aptType }}</p></div>
              <div><p class="label-text">Fee</p><p class="font-bold text-primary-700 mt-0.5">৳{{ selectedDoctor?.consultation_fee }}</p></div>
            </div>
            <div v-if="symptoms" class="pt-3 border-t border-primary-100">
              <p class="label-text">Symptoms</p>
              <p class="text-slate-600 mt-0.5">{{ symptoms }}</p>
            </div>
          </div>
        </div>
      </AppCard>

      <!-- Navigation -->
      <div class="flex justify-between">
        <AppButton
          v-if="step > 1"
          variant="secondary"
          @click="step--"
        >
          <ChevronLeftIcon class="h-4 w-4" />
          Back
        </AppButton>
        <div v-else />

        <AppButton
          v-if="step < 6"
          variant="primary"
          :disabled="!canProceed()"
          @click="step++"
        >
          Next
          <ChevronRightIcon class="h-4 w-4" />
        </AppButton>

        <AppButton
          v-else
          variant="primary"
          :loading="loading"
          @click="handleBook"
        >
          <CalendarDaysIcon class="h-4 w-4" />
          Confirm Booking
        </AppButton>
      </div>
    </template>
  </div>
</template>