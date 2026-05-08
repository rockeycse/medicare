<script setup lang="ts">
import { ref, onMounted } from 'vue'
import adminService from '@/services/admin.service'
import type { User } from '@/types/user'
import AppCard from '@/components/ui/AppCard.vue'
import AppTable from '@/components/ui/AppTable.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import AppModal from '@/components/ui/AppModal.vue'
import { useToast } from '@/composables/useToast'
import { useRouter } from 'vue-router'
import { PlusIcon, TrashIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const router = useRouter()
const toast = useToast()
const users = ref<User[]>([])
const loading = ref(true)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const deleteModal = ref(false)
const deletingId = ref<number | null>(null)

const columns = [
  { key: 'name', label: 'User' },
  { key: 'role', label: 'Role' },
  { key: 'status', label: 'Status' },
  { key: 'created', label: 'Joined' },
  { key: 'actions', label: 'Actions' },
]

async function fetchUsers() {
  loading.value = true
  try {
    const res = await adminService.getUsers({ page: currentPage.value, per_page: 10 })
    users.value = res.data || []
    if (res.meta) { lastPage.value = res.meta.last_page; total.value = res.meta.total }
  } finally { loading.value = false }
}

onMounted(fetchUsers)

async function toggleStatus(id: number) {
  try {
    await adminService.toggleStatus(id)
    toast.success('Status updated')
    fetchUsers()
  } catch { toast.error('Failed to update status') }
}

async function handleDelete() {
  if (!deletingId.value) return
  try {
    await adminService.deleteUser(deletingId.value)
    toast.success('User deleted')
    deleteModal.value = false
    fetchUsers()
  } catch { toast.error('Failed to delete user') }
}

const tableData = () => users.value.map(u => ({
  id: u.id,
  name: u.name,
  email: u.email,
  role: u.role,
  status: u.is_active,
  created: dayjs(u.created_at).format('MMM D, YYYY'),
}))
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="page-title">Users</h1>
        <p class="text-sm text-slate-500 mt-1">Manage all system users</p>
      </div>
      <AppButton variant="primary" @click="router.push('/admin/users/create')">
        <PlusIcon class="h-4 w-4" /> Create User
      </AppButton>
    </div>

    <AppCard>
      <AppTable :columns="columns" :data="tableData()" :loading="loading" empty-message="No users found">
        <template #name="{ row }">
          <div class="flex items-center gap-3">
            <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-600">
              {{ (row.name as string).charAt(0) }}
            </div>
            <div>
              <p class="font-medium text-slate-700">{{ row.name }}</p>
              <p class="text-xs text-slate-400">{{ row.email }}</p>
            </div>
          </div>
        </template>
        <template #role="{ value }">
          <AppBadge :variant="value as string">{{ value }}</AppBadge>
        </template>
        <template #status="{ row }">
          <AppBadge :variant="row.status ? 'active' : 'inactive'">
            {{ row.status ? 'Active' : 'Inactive' }}
          </AppBadge>
        </template>
        <template #actions="{ row }">
          <div class="flex items-center gap-1">
            <AppButton variant="ghost" size="xs" @click="toggleStatus(row.id as number)" title="Toggle status">
              <ArrowPathIcon class="h-4 w-4" />
            </AppButton>
            <AppButton variant="ghost" size="xs" class="text-red-500 hover:bg-red-50" @click="deletingId = row.id as number; deleteModal = true">
              <TrashIcon class="h-4 w-4" />
            </AppButton>
          </div>
        </template>
      </AppTable>
      <AppPagination v-if="lastPage > 1" :current-page="currentPage" :last-page="lastPage" :total="total" @page-change="(p) => { currentPage = p; fetchUsers() }" />
    </AppCard>

    <AppModal v-model="deleteModal" title="Delete User" size="sm">
      <p class="text-slate-600">Are you sure you want to delete this user?</p>
      <template #footer>
        <div class="flex gap-3 justify-end">
          <AppButton variant="secondary" @click="deleteModal = false">Cancel</AppButton>
          <AppButton variant="danger" @click="handleDelete">Delete</AppButton>
        </div>
      </template>
    </AppModal>
  </div>
</template>