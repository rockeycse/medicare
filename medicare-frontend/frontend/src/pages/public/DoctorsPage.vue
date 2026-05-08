<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import doctorService from '@/services/doctor.service'
import type { Doctor } from '@/types/doctor'
import AppButton from '@/components/ui/AppButton.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import { MagnifyingGlassIcon, StarIcon, CalendarDaysIcon } from '@heroicons/vue/24/outline'
import { useDebounceFn } from '@vueuse/core'

const router = useRouter()
const authStore = useAuthStore()

const doctors = ref<Doctor[]>([])
const loading = ref(true)
const search = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)

async function fetchDoctors() {
  loading.value = true
  try {
    const res = await doctorService.getAll({ search: search.value, page: currentPage.value, per_page: 9 })
    doctors.value = res.data || []
    if (res.meta) {
      lastPage.value = res.meta.last_page
      total.value = res.meta.total
    }
  } finally {
    loading.value = false
  }
}

const debouncedFetch = useDebounceFn(fetchDoctors, 400)

watch(search, () => {
  currentPage.value = 1
  debouncedFetch()
})

onMounted(fetchDoctors)

function handleBook(doctorId: number) {
  if (authStore.isAuthenticated && authStore.isPatient) {
    router.push(`/patient/book-appointment?doctor_id=${doctorId}`)
  } else {
    router.push('/login')
  }
}
</script>

<template>
  <div class="pt-20 min-h-screen bg-slate-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-primary-600 to-sky-500 py-16">
      <div class="mx-auto max-w-7xl px-6 text-center">
        <h1 class="text-4xl font-bold text-white mb-4">Find Your Doctor</h1>
        <p class="text-white/80 mb-8">Browse our team of experienced medical professionals</p>
        <div class="max-w-md mx-auto relative">
          <MagnifyingGlassIcon class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />
          <input
            v-model="search"
            type="text"
            placeholder="Search by name or specialization..."
            class="w-full rounded-xl border-0 bg-white py-3 pl-12 pr-4 text-sm shadow-lg focus:outline-none focus:ring-2 focus:ring-primary-300"
          />
        </div>
      </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 py-12">
      <!-- Loading -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 9" :key="i" class="bg-white rounded-2xl p-6 border border-slate-100 animate-pulse">
          <div class="flex items-center gap-4 mb-4">
            <div class="h-16 w-16 rounded-full bg-slate-200" />
            <div class="flex-1 space-y-2">
              <div class="h-4 bg-slate-200 rounded w-3/4" />
              <div class="h-3 bg-slate-100 rounded w-1/2" />
            </div>
          </div>
          <div class="space-y-2">
            <div class="h-3 bg-slate-100 rounded" />
            <div class="h-8 bg-slate-100 rounded-lg mt-4" />
          </div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else-if="!doctors.length" class="text-center py-24">
        <div class="mx-auto mb-4 h-20 w-20 rounded-full bg-slate-100 flex items-center justify-center">
          <MagnifyingGlassIcon class="h-10 w-10 text-slate-300" />
        </div>
        <h3 class="text-lg font-semibold text-slate-700 mb-2">No doctors found</h3>
        <p class="text-slate-400">Try adjusting your search</p>
      </div>

      <!-- Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="doc in doctors"
          :key="doc.id"
          class="bg-white rounded-2xl border border-slate-100 p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
        >
          <div class="flex items-start gap-4 mb-4">
            <div class="h-16 w-16 rounded-full bg-primary-100 flex items-center justify-center text-2xl font-bold text-primary-600 shrink-0">
              {{ doc.user.name.charAt(0) }}
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="font-semibold text-slate-800">Dr. {{ doc.user.name }}</h3>
              <p class="text-sm text-primary-600">{{ doc.specialization?.name }}</p>
              <div class="flex gap-1 mt-1">
                <StarIcon v-for="i in 5" :key="i" class="h-3.5 w-3.5 text-amber-400 fill-amber-400" />
              </div>
            </div>
            <AppBadge :variant="doc.is_available ? 'active' : 'inactive'">
              {{ doc.is_available ? 'Available' : 'Busy' }}
            </AppBadge>
          </div>

          <div class="grid grid-cols-2 gap-2 mb-4">
            <div class="rounded-lg bg-slate-50 px-3 py-2 text-center">
              <p class="text-xs text-slate-400">Experience</p>
              <p class="text-sm font-semibold text-slate-700">{{ doc.experience_years }} yrs</p>
            </div>
            <div class="rounded-lg bg-slate-50 px-3 py-2 text-center">
              <p class="text-xs text-slate-400">Fee</p>
              <p class="text-sm font-semibold text-slate-700">৳{{ doc.consultation_fee }}</p>
            </div>
          </div>

          <div class="flex gap-2">
            <router-link :to="`/doctors/${doc.id}`" class="flex-1">
              <AppButton variant="outline" size="sm" class="w-full">View Profile</AppButton>
            </router-link>
            <AppButton variant="primary" size="sm" class="flex-1" @click="handleBook(doc.id)">
              <CalendarDaysIcon class="h-4 w-4" />
              Book
            </AppButton>
          </div>
        </div>
      </div>

      <AppPagination
        v-if="lastPage > 1"
        :current-page="currentPage"
        :last-page="lastPage"
        :total="total"
        @page-change="(p) => { currentPage = p; fetchDoctors() }"
        class="mt-8"
      />
    </div>
  </div>
</template>