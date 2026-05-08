<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import doctorService from '@/services/doctor.service'
import type { Doctor } from '@/types/doctor'
import AppButton from '@/components/ui/AppButton.vue'
import AppCard from '@/components/ui/AppCard.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppSkeleton from '@/components/ui/AppSkeleton.vue'
import { StarIcon, CalendarDaysIcon, ClockIcon, CurrencyDollarIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const doctor = ref<Doctor | null>(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await doctorService.getById(Number(route.params.id))
    doctor.value = res.data
  } finally {
    loading.value = false
  }
})

function handleBook() {
  if (authStore.isAuthenticated && authStore.isPatient) {
    router.push(`/patient/book-appointment?doctor_id=${doctor.value?.id}`)
  } else {
    router.push('/login')
  }
}

const dayLabels: Record<string, string> = {
  monday: 'Monday', tuesday: 'Tuesday', wednesday: 'Wednesday',
  thursday: 'Thursday', friday: 'Friday', saturday: 'Saturday', sunday: 'Sunday'
}
</script>

<template>
  <div class="pt-20 min-h-screen bg-slate-50">
    <div class="mx-auto max-w-5xl px-6 py-12">
      <!-- Skeleton -->
      <div v-if="loading" class="space-y-6">
        <AppCard><AppSkeleton type="card" :lines="4" /></AppCard>
        <AppCard><AppSkeleton type="card" :lines="3" /></AppCard>
      </div>

      <template v-else-if="doctor">
        <!-- Profile header -->
        <AppCard class="mb-6">
          <div class="flex flex-col md:flex-row gap-8">
            <div class="flex flex-col items-center md:items-start gap-4">
              <div class="h-28 w-28 rounded-2xl bg-primary-100 flex items-center justify-center text-4xl font-bold text-primary-600">
                {{ doctor.user.name.charAt(0) }}
              </div>
              <AppBadge :variant="doctor.is_available ? 'active' : 'inactive'">
                {{ doctor.is_available ? '● Available' : '● Unavailable' }}
              </AppBadge>
            </div>
            <div class="flex-1">
              <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                  <h1 class="text-3xl font-bold text-slate-800">Dr. {{ doctor.user.name }}</h1>
                  <p class="text-primary-600 font-medium mt-1">{{ doctor.specialization?.name }}</p>
                  <p class="text-sm text-slate-500 mt-1">License: {{ doctor.license_number }}</p>
                  <div class="flex gap-1 mt-2">
                    <StarIcon v-for="i in 5" :key="i" class="h-4 w-4 text-amber-400 fill-amber-400" />
                    <span class="text-sm text-slate-500 ml-1">5.0 (200+ reviews)</span>
                  </div>
                </div>
                <AppButton variant="primary" size="lg" @click="handleBook">
                  <CalendarDaysIcon class="h-5 w-5" />
                  Book Appointment
                </AppButton>
              </div>

              <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-6">
                <div class="rounded-xl bg-slate-50 p-4">
                  <p class="label-text">Experience</p>
                  <p class="text-xl font-bold text-slate-800 mt-1">{{ doctor.experience_years }} yrs</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                  <p class="label-text">Consultation Fee</p>
                  <p class="text-xl font-bold text-slate-800 mt-1">৳{{ doctor.consultation_fee }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                  <p class="label-text">Contact</p>
                  <p class="text-sm font-medium text-slate-700 mt-1 truncate">{{ doctor.user.email }}</p>
                </div>
              </div>
            </div>
          </div>

          <div v-if="doctor.bio" class="mt-6 pt-6 border-t border-slate-100">
            <h3 class="section-title mb-2">About</h3>
            <p class="text-slate-600 leading-relaxed">{{ doctor.bio }}</p>
          </div>
        </AppCard>

        <!-- Schedule -->
        <AppCard>
          <h3 class="section-title mb-4">Available Schedule</h3>
          <div v-if="doctor.schedules?.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div
              v-for="sch in doctor.schedules"
              :key="sch.id"
              :class="[
                'rounded-xl border p-4 transition-colors',
                sch.is_available ? 'border-primary-200 bg-primary-50' : 'border-slate-100 bg-slate-50 opacity-60'
              ]"
            >
              <div class="flex items-center justify-between mb-2">
                <p class="font-semibold text-slate-800 capitalize">{{ dayLabels[sch.day_of_week] }}</p>
                <AppBadge :variant="sch.is_available ? 'active' : 'inactive'">
                  {{ sch.is_available ? 'Open' : 'Closed' }}
                </AppBadge>
              </div>
              <div class="flex items-center gap-2 text-sm text-slate-500">
                <ClockIcon class="h-4 w-4" />
                {{ sch.start_time }} – {{ sch.end_time }}
              </div>
              <p class="text-xs text-slate-400 mt-1">Max {{ sch.max_patients }} patients/day</p>
            </div>
          </div>
          <div v-else class="text-center py-8 text-slate-400 text-sm">No schedule available</div>
        </AppCard>
      </template>

      <!-- Not found -->
      <div v-else class="text-center py-24">
        <p class="text-slate-400">Doctor not found.</p>
        <router-link to="/doctors"><AppButton variant="primary" class="mt-4">Back to Doctors</AppButton></router-link>
      </div>
    </div>
  </div>
</template>