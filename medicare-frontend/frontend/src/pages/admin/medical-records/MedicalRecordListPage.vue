<script setup lang="ts">
import { ref, onMounted } from 'vue'
import medicalRecordService from '@/services/medical-record.service'
import AppCard from '@/components/ui/AppCard.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import AppSkeleton from '@/components/ui/AppSkeleton.vue'
import { useToast } from '@/composables/useToast'
import { ArrowDownTrayIcon, TrashIcon, DocumentIcon } from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const toast = useToast()

interface MedicalRecord {
  id: number
  file_name: string
  file_type: string
  description: string | null
  created_at: string
}

const records = ref<MedicalRecord[]>([])
const loading = ref(true)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)

async function fetchRecords() {
  loading.value = true
  try {
    const res = await medicalRecordService.getAll({ page: currentPage.value, per_page: 10 })
    records.value = res.data || []
    if (res.meta) {
      lastPage.value = res.meta.last_page
      total.value = res.meta.total
    }
  } catch {
    toast.error('Failed to load records')
  } finally {
    loading.value = false
  }
}

onMounted(fetchRecords)

async function handleDownload(id: number) {
  try {
    const res = await medicalRecordService.download(id)
    const url = URL.createObjectURL(res.data)
    const a = document.createElement('a')
    a.href = url
    a.download = 'medical-record'
    a.click()
    URL.revokeObjectURL(url)
  } catch {
    toast.error('Download failed')
  }
}

async function handleDelete(id: number) {
  if (!confirm('Delete this record? This cannot be undone.')) return
  try {
    await medicalRecordService.delete(id)
    toast.success('Record deleted')
    fetchRecords()
  } catch {
    toast.error('Delete failed')
  }
}

function getFileIcon(type: string) {
  if (!type) return '📄'
  if (type.includes('pdf')) return '📕'
  if (type.includes('image')) return '🖼️'
  if (type.includes('word') || type.includes('doc')) return '📝'
  return '📄'
}
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="page-title">Medical Records</h1>
      <p class="text-sm text-slate-500 mt-1">All patient medical records</p>
    </div>

    <AppCard>
      <!-- Loading skeleton -->
      <AppSkeleton v-if="loading" type="table" :rows="5" />

      <!-- Empty state -->
      <div v-else-if="!records.length" class="flex flex-col items-center justify-center py-16 gap-3">
        <DocumentIcon class="h-12 w-12 text-slate-300" />
        <p class="text-slate-400 text-sm">No medical records found</p>
      </div>

      <!-- Records list -->
      <div v-else class="divide-y divide-slate-100">
        <div
          v-for="record in records"
          :key="record.id"
          class="flex items-center gap-4 py-3 hover:bg-slate-50 rounded-lg px-2 transition-colors"
        >
          <div class="text-2xl shrink-0">{{ getFileIcon(record.file_type) }}</div>

          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-slate-700 truncate">{{ record.file_name }}</p>
            <div class="flex items-center gap-3 mt-0.5">
              <span class="text-xs text-slate-400 uppercase">{{ record.file_type || 'Unknown' }}</span>
              <span class="text-xs text-slate-300">•</span>
              <span class="text-xs text-slate-400">{{ dayjs(record.created_at).format('MMM D, YYYY') }}</span>
            </div>
            <p v-if="record.description" class="text-xs text-slate-400 mt-0.5 truncate">{{ record.description }}</p>
          </div>

          <div class="flex items-center gap-1 shrink-0">
            <button
              @click="handleDownload(record.id)"
              class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-primary-600 hover:bg-primary-50 transition-colors"
            >
              <ArrowDownTrayIcon class="h-3.5 w-3.5" />
              Download
            </button>
            <button
              @click="handleDelete(record.id)"
              class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50 transition-colors"
            >
              <TrashIcon class="h-3.5 w-3.5" />
              Delete
            </button>
          </div>
        </div>
      </div>

      <AppPagination
        v-if="lastPage > 1"
        :current-page="currentPage"
        :last-page="lastPage"
        :total="total"
        @page-change="(p) => { currentPage = p; fetchRecords() }"
      />
    </AppCard>
  </div>
</template>