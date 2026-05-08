<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import doctorService from '@/services/doctor.service'
import type { DoctorSchedule } from '@/types/doctor'
import AppCard from '@/components/ui/AppCard.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppSkeleton from '@/components/ui/AppSkeleton.vue'
import { useToast } from '@/composables/useToast'
import { ClockIcon } from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const toast = useToast()
const loading = ref(true)
const saving = ref(false)

const days = [
  { key: 'saturday', label: 'Saturday' },
  { key: 'sunday', label: 'Sunday' },
  { key: 'monday', label: 'Monday' },
  { key: 'tuesday', label: 'Tuesday' },
  { key: 'wednesday', label: 'Wednesday' },
  { key: 'thursday', label: 'Thursday' },
  { key: 'friday', label: 'Friday' },
]

interface ScheduleForm {
  day_of_week: string
  start_time: string
  end_time: string
  max_patients: number
  is_available: boolean
}

const schedules = ref<ScheduleForm[]>(
  days.map(d => ({
    day_of_week: d.key,
    start_time: '09:00',
    end_time: '17:00',
    max_patients: 20,
    is_available: false,
  }))
)

onMounted(async () => {
  try {
    const doctorId = authStore.user?.doctor?.id
    if (!doctorId) return
    const res = await doctorService.getSchedules(doctorId)
    const existing: DoctorSchedule[] = res.data || []
    schedules.value = schedules.value.map(s => {
      const found = existing.find(e => e.day_of_week === s.day_of_week)
      if (found) {
        return {
          day_of_week: found.day_of_week,
          start_time: found.start_time,
          end_time: found.end_time,
          max_patients: found.max_patients,
          is_available: found.is_available,
        }
      }
      return s
    })
  } finally {
    loading.value = false
  }
})

async function handleSave() {
  const doctorId = authStore.user?.doctor?.id
  if (!doctorId) return
  saving.value = true
  try {
    await doctorService.syncSchedules(doctorId, {
      schedules: schedules.value.filter(s => s.is_available)
    })
    toast.success('Schedule updated successfully!')
  } catch {
    toast.error('Failed to update schedule')
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="page-title">My Schedule</h1>
        <p class="text-sm text-slate-500 mt-1">Set your weekly availability</p>
      </div>
      <AppButton variant="primary" :loading="saving" @click="handleSave">
        Save Schedule
      </AppButton>
    </div>

    <AppCard v-if="loading">
      <AppSkeleton type="table" :rows="7" />
    </AppCard>

    <div v-else class="space-y-3">
      <div
        v-for="(sch, idx) in schedules"
        :key="sch.day_of_week"
        :class="[
          'rounded-xl border p-5 transition-all duration-200',
          sch.is_available
            ? 'border-primary-200 bg-primary-50/50'
            : 'border-slate-100 bg-white opacity-70'
        ]"
      >
        <div class="flex items-center gap-4 flex-wrap">
          <!-- Toggle + Day label -->
          <div class="flex items-center gap-3 w-36">
            <button
              type="button"
              @click="sch.is_available = !sch.is_available"
              :class="[
                'relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors',
                sch.is_available ? 'bg-primary-500' : 'bg-slate-200'
              ]"
            >
              <span
                :class="[
                  'inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform',
                  sch.is_available ? 'translate-x-6' : 'translate-x-1'
                ]"
              />
            </button>
            <span class="text-sm font-semibold text-slate-700 capitalize">{{ sch.day_of_week }}</span>
          </div>

          <!-- Time inputs -->
          <div class="flex items-center gap-3 flex-1 flex-wrap">
            <div class="flex items-center gap-2">
              <ClockIcon class="h-4 w-4 text-slate-400 shrink-0" />
              <div class="flex items-center gap-2">
                <input
                  v-model="sch.start_time"
                  type="time"
                  :disabled="!sch.is_available"
                  class="input-base w-32 py-1.5 text-sm disabled:opacity-50"
                />
                <span class="text-slate-400 text-sm">to</span>
                <input
                  v-model="sch.end_time"
                  type="time"
                  :disabled="!sch.is_available"
                  class="input-base w-32 py-1.5 text-sm disabled:opacity-50"
                />
              </div>
            </div>

            <div class="flex items-center gap-2">
              <span class="text-xs text-slate-500 whitespace-nowrap">Max patients:</span>
              <input
                v-model.number="sch.max_patients"
                type="number"
                min="1"
                max="100"
                :disabled="!sch.is_available"
                class="input-base w-20 py-1.5 text-sm disabled:opacity-50"
              />
            </div>
          </div>

          <!-- Closed label -->
          <span v-if="!sch.is_available" class="text-xs font-medium text-slate-400 uppercase tracking-wide">
            Closed
          </span>
        </div>
      </div>
    </div>
  </div>
</template>