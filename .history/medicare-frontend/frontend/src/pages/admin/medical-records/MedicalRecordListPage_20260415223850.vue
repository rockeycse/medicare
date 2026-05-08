<script setup lang="ts">
import { ref, onMounted } from 'vue'
import medicalRecordService from '@/services/medical-record.service'
import AppCard from '@/components/ui/AppCard.vue'
import AppTable from '@/components/ui/AppTable.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import { useToast } from '@/composables/useToast'
import { ArrowDownTrayIcon, TrashIcon } from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const toast = useToast()
const records = ref<Record<string, unknown>[]>([])
const loading = ref(true)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)

const columns = [
  { key: 'name', label: 'File Name' },
  { key: 'type', label: 'Type' },
  { key: 'date', label: 'Upload Date' },
  { key: 'actions', label: 'Actions' },
]

async function fetch() {
  loading.value = true
  try {
    const res = await medicalRecordService.getAll({ page: currentPage.value, per_page: 10 })
    records.value = (res.data || []).map((r: Record<string, unknown>) => ({
      id: r.id,
      name: r.file_name || r.name,
      type: r.file_type || r.type || '—',
      date: dayjs(r.created_at as string).format('MMM D, YYYY'),
    }))
    if (res.meta) { lastPage.value = res.meta.last_page; total.value = res.meta.total }
  } finally { loading.value = false }
}

onMounted(fetch)

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
    fetch()
  } catch { toast.error('Delete failed') }
}
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="page-title">Medical Records</h1>
      <p class="text-sm text-slate-500 mt-1">All patient medical records</p>
    </div>
    <AppCard>
      <AppTable :columns="columns" :data="records" :loading="loading" empty-message="No records found">
        <template #actions="{ row }">
          <div class="flex gap-1">
            <AppButton variant="ghost" size="xs" @click="handleDownload(row.id as number)">
              <ArrowDownTrayIcon class="h-4 w-4" />
            </AppButton>
            <AppButton variant="ghost" size="xs" class="text-red-500 hover:bg-red-50" @click="handleDelete(row.id as number)">
              <TrashIcon class="h-4 w-4" />
            </AppButton>
          </div>
        </template>
      </AppTable>
      <AppPagination v-if="lastPage > 1" :current-page="currentPage" :last-page="lastPage" :total="total" @page-change="(p) => { currentPage = p; fetch() }" />
    </AppCard>
  </div>
</template>