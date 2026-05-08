<script setup lang="ts">
import { ref, onMounted } from 'vue'
import medicalRecordService from '@/services/medical-record.service'
import AppCard from '@/components/ui/AppCard.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppSkeleton from '@/components/ui/AppSkeleton.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import { useToast } from '@/composables/useToast'
import { ArrowDownTrayIcon, TrashIcon, CloudArrowUpIcon, DocumentIcon } from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const toast = useToast()

interface MedRecord {
  id: number
  file_name: string
  file_type: string
  description: string | null
  created_at: string
}

const records = ref<MedRecord[]>([])
const loading = ref(true)
const uploading = ref(false)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const dragOver = ref(false)
const description = ref('')
const fileInput = ref<HTMLInputElement | null>(null)

async function fetchRecords() {
  loading.value = true
  try {
    const res = await medicalRecordService.getAll({ page: currentPage.value, per_page: 10 })
    records.value = res.data || []
    if (res.meta) { lastPage.value = res.meta.last_page; total.value = res.meta.total }
  } finally { loading.value = false }
}

onMounted(fetchRecords)

async function handleUpload(file: File) {
  if (!file) return
  uploading.value = true
  try {
    const formData = new FormData()
    formData.append('file', file)
    if (description.value) formData.append('description', description.value)
    await medicalRecordService.upload(formData)
    toast.success('File uploaded successfully!')
    description.value = ''
    fetchRecords()
  } catch { toast.error('Upload failed') } finally { uploading.value = false }
}

function onDrop(e: DragEvent) {
  dragOver.value = false
  const file = e.dataTransfer?.files[0]
  if (file) handleUpload(file)
}

function onFileSelect(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) handleUpload(file)
}

async function handleDownload(id: number) {
  try {
    const res = await medicalRecordService.download(id)
    const url = URL.createObjectURL(res.data)
    const a = document.createElement('a')
    a.href = url; a.download = 'medical-record'; a.click()
    URL.revokeObjectURL(url)
  } catch { toast.error('Download failed') }
}

async function handleDelete(id: number) {
  if (!confirm('Delete this record?')) return
  try {
    await medicalRecordService.delete(id)
    toast.success('Record deleted')
    fetchRecords()
  } catch { toast.error('Delete failed') }
}

function getIcon(type: string) {
  if (!type) return '📄'
  if (type.includes('pdf')) return '📕'
  if (type.includes('image')) return '🖼️'
  if (type.includes('word')) return '📝'
  return '📄'
}
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="page-title">Medical Records</h1>
      <p class="text-sm text-slate-500 mt-1">Upload and manage your medical documents</p>
    </div>

    <!-- Upload -->
    <AppCard>
      <h3 class="section-title mb-4">Upload New Record</h3>
      <div class="mb-3">
        <label class="label-text mb-1.5 block">Description (optional)</label>
        <input v-model="description" type="text" placeholder="e.g. Blood test report, X-ray..." class="input-base" />
      </div>
      <div
        :class="[
          'relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed p-10 text-center transition-all cursor-pointer',
          dragOver ? 'border-primary-400 bg-primary-50' : 'border-slate-200 hover:border-primary-300 hover:bg-slate-50'
        ]"
        @dragover.prevent="dragOver = true"
        @dragleave="dragOver = false"
        @drop.prevent="onDrop"
        @click="fileInput?.click()"
      >
        <input ref="fileInput" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" @change="onFileSelect" />
        <CloudArrowUpIcon v-if="!uploading" class="h-12 w-12 text-slate-300 mb-3" />
        <div v-else class="mb-3">
          <div class="h-12 w-12 rounded-full border-4 border-primary-500 border-t-transparent animate-spin mx-auto" />
        </div>
        <p class="text-sm font-medium text-slate-600">
          {{ uploading ? 'Uploading...' : 'Drop file here or click to browse' }}
        </p>
        <p class="text-xs text-slate-400 mt-1">Supports PDF, JPG, PNG, DOC (max 10MB)</p>
      </div>
    </AppCard>

    <!-- Records list -->
    <AppCard>
      <h3 class="section-title mb-4">My Records</h3>
      <AppSkeleton v-if="loading" type="table" :rows="5" />
      <div v-else-if="!records.length" class="flex flex-col items-center py-12 gap-2">
        <DocumentIcon class="h-12 w-12 text-slate-300" />
        <p class="text-slate-400 text-sm">No records uploaded yet</p>
      </div>
      <div v-else class="divide-y divide-slate-100">
        <div
          v-for="rec in records" :key="rec.id"
          class="flex items-center gap-4 py-3 px-2 hover:bg-slate-50 rounded-lg transition-colors"
        >
          <div class="text-2xl shrink-0">{{ getIcon(rec.file_type) }}</div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-slate-700 truncate">{{ rec.file_name }}</p>
            <div class="flex items-center gap-3 mt-0.5">
              <span class="text-xs text-slate-400 uppercase">{{ rec.file_type || 'Unknown' }}</span>
              <span class="text-xs text-slate-300">•</span>
              <span class="text-xs text-slate-400">{{ dayjs(rec.created_at).format('MMM D, YYYY') }}</span>
            </div>
            <p v-if="rec.description" class="text-xs text-slate-400 mt-0.5 truncate">{{ rec.description }}</p>
          </div>
          <div class="flex items-center gap-1 shrink-0">
            <button @click="handleDownload(rec.id)" class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-primary-600 hover:bg-primary-50 transition-colors">
              <ArrowDownTrayIcon class="h-3.5 w-3.5" /> Download
            </button>
            <button @click="handleDelete(rec.id)" class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50 transition-colors">
              <TrashIcon class="h-3.5 w-3.5" /> Delete
            </button>
          </div>
        </div>
      </div>
      <AppPagination v-if="lastPage > 1" :current-page="currentPage" :last-page="lastPage" :total="total" @page-change="(p) => { currentPage = p; fetchRecords() }" />
    </AppCard>
  </div>
</template>