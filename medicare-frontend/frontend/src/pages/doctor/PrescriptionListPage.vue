<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import prescriptionService from '@/services/prescription.service'
import type { Prescription } from '@/types/prescription'
import AppCard from '@/components/ui/AppCard.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppModal from '@/components/ui/AppModal.vue'
import AppSkeleton from '@/components/ui/AppSkeleton.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import { DocumentPlusIcon, EyeIcon } from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const router = useRouter()
const prescriptions = ref<Prescription[]>([])
const loading = ref(true)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const viewModal = ref(false)
const selected = ref<Prescription | null>(null)

async function fetch() {
  loading.value = true
  try {
    const res = await prescriptionService.getAll({ page: currentPage.value, per_page: 10 })
    prescriptions.value = res.data || []
    if (res.meta) { lastPage.value = res.meta.last_page; total.value = res.meta.total }
  } finally { loading.value = false }
}

onMounted(fetch)
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="page-title">Prescriptions</h1>
        <p class="text-sm text-slate-500 mt-1">Your prescription history</p>
      </div>
      <AppButton variant="primary" @click="router.push('/doctor/prescriptions/create')">
        <DocumentPlusIcon class="h-4 w-4" />
        Write New
      </AppButton>
    </div>

    <AppCard>
      <AppSkeleton v-if="loading" type="table" :rows="5" />

      <div v-else-if="!prescriptions.length" class="flex flex-col items-center py-16 gap-3">
        <DocumentPlusIcon class="h-12 w-12 text-slate-300" />
        <p class="text-slate-400 text-sm">No prescriptions yet</p>
        <AppButton variant="primary" size="sm" @click="router.push('/doctor/prescriptions/create')">
          Write First Prescription
        </AppButton>
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="p in prescriptions"
          :key="p.id"
          class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 hover:bg-slate-50 transition-colors"
        >
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100">
            <span class="text-lg">💊</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-700 truncate">{{ p.diagnosis }}</p>
            <div class="flex items-center gap-3 mt-1 text-xs text-slate-400">
              <span>{{ p.medicines.length }} medicine{{ p.medicines.length !== 1 ? 's' : '' }}</span>
              <span>•</span>
              <span>{{ dayjs(p.created_at).format('MMM D, YYYY') }}</span>
            </div>
          </div>
          <button
            @click="selected = p; viewModal = true"
            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 transition-colors"
          >
            <EyeIcon class="h-4 w-4" />
          </button>
        </div>
      </div>

      <AppPagination
        v-if="lastPage > 1"
        :current-page="currentPage"
        :last-page="lastPage"
        :total="total"
        @page-change="(p) => { currentPage = p; fetch() }"
      />
    </AppCard>

    <!-- View modal -->
    <AppModal v-model="viewModal" title="Prescription Details" size="lg">
      <div v-if="selected" class="space-y-4 text-sm">
        <div class="rounded-xl bg-slate-50 p-4">
          <p class="label-text mb-1">Diagnosis</p>
          <p class="text-slate-700 font-medium">{{ selected.diagnosis }}</p>
        </div>

        <div>
          <p class="label-text mb-3">Medicines</p>
          <div class="space-y-2">
            <div
              v-for="med in selected.medicines"
              :key="med.id"
              class="rounded-lg border border-slate-100 p-3 bg-white"
            >
              <div class="flex items-center justify-between">
                <p class="font-semibold text-slate-800">{{ med.medicine_name }}</p>
                <span class="text-xs text-slate-400">{{ med.duration }}</span>
              </div>
              <div class="flex gap-4 mt-1 text-xs text-slate-500">
                <span>Dosage: {{ med.dosage }}</span>
                <span>Frequency: {{ med.frequency }}</span>
              </div>
              <p v-if="med.instructions" class="text-xs text-slate-400 mt-1 italic">{{ med.instructions }}</p>
            </div>
          </div>
        </div>

        <div v-if="selected.advice" class="rounded-xl bg-blue-50 p-4">
          <p class="label-text mb-1 text-blue-700">Advice</p>
          <p class="text-blue-700">{{ selected.advice }}</p>
        </div>

        <div v-if="selected.next_visit_date" class="rounded-xl bg-amber-50 p-4">
          <p class="label-text mb-1 text-amber-700">Next Visit</p>
          <p class="font-medium text-amber-700">{{ dayjs(selected.next_visit_date).format('MMMM D, YYYY') }}</p>
        </div>

        <p class="text-xs text-slate-400">Created on {{ dayjs(selected.created_at).format('MMM D, YYYY h:mm A') }}</p>
      </div>
    </AppModal>
  </div>
</template>