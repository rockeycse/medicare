<script setup lang="ts">
import { ref, onMounted } from 'vue'
import doctorService from '@/services/doctor.service'
import type { Doctor } from '@/types/doctor'
import AppCard from '@/components/ui/AppCard.vue'
import AppTable from '@/components/ui/AppTable.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppModal from '@/components/ui/AppModal.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import AppInput from '@/components/ui/AppInput.vue'
import { useToast } from '@/composables/useToast'
import { useRouter } from 'vue-router'
import { PlusIcon, MagnifyingGlassIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline'
import { useDebounceFn } from '@vueuse/core'

const router = useRouter()
const toast = useToast()

const doctors = ref<Doctor[]>([])
const loading = ref(true)
const search = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)

const deleteModal = ref(false)
const deletingId = ref<number | null>(null)
const deleting = ref(false)

const columns = [
  { key: 'name', label: 'Doctor' },
  { key: 'specialization', label: 'Specialization' },
  { key: 'experience', label: 'Experience' },
  { key: 'fee', label: 'Fee' },
  { key: 'status', label: 'Status' },
  { key: 'actions', label: 'Actions' },
]

async function fetchDoctors() {
  loading.value = true
  try {
    const res = await doctorService.getAll({ search: search.value, page: currentPage.value, per_page: 10 })
    doctors.value = res.data || []
    if (res.meta) { lastPage.value = res.meta.last_page; total.value = res.meta.total }
  } finally {
    loading.value = false
  }
}

const debouncedFetch = useDebounceFn(fetchDoctors, 400)

onMounted(fetchDoctors)

function confirmDelete(id: number) {
  deletingId.value = id
  deleteModal.value = true
}

async function handleDelete() {
  if (!deletingId.value) return
  deleting.value = true
  try {
    await doctorService.delete(deletingId.value)
    toast.success('Doctor deleted successfully')
    deleteModal.value = false
    fetchDoctors()
  } catch {
    toast.error('Failed to delete doctor')
  } finally {
    deleting.value = false
  }
}

const tableData = () => doctors.value.map(d => ({
  id: d.id,
  name: d.user.name,
  email: d.user.email,
  specialization: d.specialization?.name,
  experience: `${d.experience_years} yrs`,
  fee: `৳${d.consultation_fee}`,
  status: d.is_available,
  _raw: d,
}))
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="page-title">Doctors</h1>
        <p class="text-sm text-slate-500 mt-1">Manage all registered doctors</p>
      </div>
      <AppButton variant="primary" @click="router.push('/admin/doctors/create')">
        <PlusIcon class="h-4 w-4" />
        Add Doctor
      </AppButton>
    </div>

    <AppCard>
      <div class="mb-4 relative max-w-xs">
        <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
        <input
          v-model="search"
          @input="debouncedFetch"
          type="text"
          placeholder="Search doctors..."
          class="input-base pl-9"
        />
      </div>

      <AppTable :columns="columns" :data="tableData()" :loading="loading" empty-message="No doctors found">
        <template #name="{ row }">
          <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-primary-100 flex items-center justify-center text-sm font-bold text-primary-600 shrink-0">
              {{ (row.name as string).charAt(0) }}
            </div>
            <div>
              <p class="font-medium text-slate-700">Dr. {{ row.name }}</p>
              <p class="text-xs text-slate-400">{{ row.email }}</p>
            </div>
          </div>
        </template>
        <template #status="{ row }">
          <AppBadge :variant="row.status ? 'active' : 'inactive'">
            {{ row.status ? 'Available' : 'Unavailable' }}
          </AppBadge>
        </template>
        <template #actions="{ row }">
          <div class="flex items-center gap-2">
            <AppButton variant="ghost" size="sm" @click="router.push(`/admin/doctors/${row.id}/edit`)">
              <PencilSquareIcon class="h-4 w-4" />
            </AppButton>
            <AppButton variant="ghost" size="sm" class="text-red-500 hover:bg-red-50" @click="confirmDelete(row.id as number)">
              <TrashIcon class="h-4 w-4" />
            </AppButton>
          </div>
        </template>
      </AppTable>

      <AppPagination
        v-if="lastPage > 1"
        :current-page="currentPage"
        :last-page="lastPage"
        :total="total"
        @page-change="(p) => { currentPage = p; fetchDoctors() }"
      />
    </AppCard>

    <!-- Delete confirm modal -->
    <AppModal v-model="deleteModal" title="Delete Doctor" size="sm">
      <p class="text-slate-600">Are you sure you want to delete this doctor? This action cannot be undone.</p>
      <template #footer>
        <div class="flex gap-3 justify-end">
          <AppButton variant="secondary" @click="deleteModal = false">Cancel</AppButton>
          <AppButton variant="danger" :loading="deleting" @click="handleDelete">Delete</AppButton>
        </div>
      </template>
    </AppModal>
  </div>
</template>