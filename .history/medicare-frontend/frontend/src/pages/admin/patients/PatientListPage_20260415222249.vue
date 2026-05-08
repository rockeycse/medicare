<script setup lang="ts">
import { ref, onMounted } from 'vue'
import adminService from '@/services/admin.service'
import type { User } from '@/types/user'
import AppCard from '@/components/ui/AppCard.vue'
import AppTable from '@/components/ui/AppTable.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import dayjs from 'dayjs'

const users = ref<User[]>([])
const loading = ref(true)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)

const columns = [
  { key: 'name', label: 'Patient' },
  { key: 'phone', label: 'Phone' },
  { key: 'status', label: 'Status' },
  { key: 'created', label: 'Registered' },
]

async function fetch() {
  loading.value = true
  try {
    const res = await adminService.getUsers({ role: 'patient', page: currentPage.value, per_page: 10 })
    users.value = res.data || []
    if (res.meta) { lastPage.value = res.meta.last_page; total.value = res.meta.total }
  } finally { loading.value = false }
}

onMounted(fetch)

const tableData = () => users.value.map(u => ({
  id: u.id,
  name: u.name,
  email: u.email,
  phone: u.phone || '—',
  status: u.is_active,
  created: dayjs(u.created_at).format('MMM D, YYYY'),
}))
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="page-title">Patients</h1>
      <p class="text-sm text-slate-500 mt-1">All registered patients</p>
    </div>
    <AppCard>
      <AppTable :columns="columns" :data="tableData()" :loading="loading" empty-message="No patients found">
        <template #name="{ row }">
          <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center text-xs font-bold text-emerald-600">
              {{ (row.name as string).charAt(0) }}
            </div>
            <div>
              <p class="font-medium text-slate-700">{{ row.name }}</p>
              <p class="text-xs text-slate-400">{{ row.email }}</p>
            </div>
          </div>
        </template>
        <template #status="{ row }">
          <AppBadge :variant="row.status ? 'active' : 'inactive'">{{ row.status ? 'Active' : 'Inactive' }}</AppBadge>
        </template>
      </AppTable>
      <AppPagination v-if="lastPage > 1" :current-page="currentPage" :last-page="lastPage" :total="total" @page-change="(p) => { currentPage = p; fetch() }" />
    </AppCard>
  </div>
</template>