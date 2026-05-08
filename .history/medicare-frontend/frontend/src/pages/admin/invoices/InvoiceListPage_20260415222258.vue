<script setup lang="ts">
import { ref, onMounted } from 'vue'
import invoiceService from '@/services/invoice.service'
import type { Invoice } from '@/types/invoice'
import AppCard from '@/components/ui/AppCard.vue'
import AppTable from '@/components/ui/AppTable.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import AppModal from '@/components/ui/AppModal.vue'
import { useToast } from '@/composables/useToast'
import { EyeIcon, ArrowUturnLeftIcon } from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const toast = useToast()
const invoices = ref<Invoice[]>([])
const loading = ref(true)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const viewModal = ref(false)
const selected = ref<Invoice | null>(null)

const columns = [
  { key: 'number', label: 'Invoice #' },
  { key: 'amount', label: 'Amount' },
  { key: 'status', label: 'Status' },
  { key: 'date', label: 'Date' },
  { key: 'actions', label: 'Actions' },
]

async function fetch() {
  loading.value = true
  try {
    const res = await invoiceService.getAll({ page: currentPage.value, per_page: 10 })
    invoices.value = res.data || []
    if (res.meta) { lastPage.value = res.meta.last_page; total.value = res.meta.total }
  } finally { loading.value = false }
}

onMounted(fetch)

async function handleRefund(id: number) {
  try {
    await invoiceService.refund(id)
    toast.success('Invoice refunded')
    viewModal.value = false
    fetch()
  } catch { toast.error('Refund failed') }
}

const tableData = () => invoices.value.map(i => ({
  id: i.id,
  number: i.invoice_number,
  amount: `৳${i.total_amount.toLocaleString()}`,
  status: i.status,
  date: dayjs(i.created_at).format('MMM D, YYYY'),
  _raw: i,
}))
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="page-title">Invoices</h1>
      <p class="text-sm text-slate-500 mt-1">All billing records</p>
    </div>
    <AppCard>
      <AppTable :columns="columns" :data="tableData()" :loading="loading" empty-message="No invoices found">
        <template #status="{ value }">
          <AppBadge :variant="value as string">{{ value }}</AppBadge>
        </template>
        <template #actions="{ row }">
          <div class="flex gap-1">
            <AppButton variant="ghost" size="xs" @click="selected = (row as Record<string,unknown>)._raw as Invoice; viewModal = true">
              <EyeIcon class="h-4 w-4" />
            </AppButton>
            <AppButton v-if="row.status === 'paid'" variant="ghost" size="xs" class="text-purple-500 hover:bg-purple-50" @click="handleRefund(row.id as number)">
              <ArrowUturnLeftIcon class="h-4 w-4" />
            </AppButton>
          </div>
        </template>
      </AppTable>
      <AppPagination v-if="lastPage > 1" :current-page="currentPage" :last-page="lastPage" :total="total" @page-change="(p) => { currentPage = p; fetch() }" />
    </AppCard>

    <AppModal v-model="viewModal" title="Invoice Details" size="md">
      <div v-if="selected" class="space-y-3 text-sm">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
          <span class="font-mono font-semibold text-slate-800">{{ selected.invoice_number }}</span>
          <AppBadge :variant="selected.status">{{ selected.status }}</AppBadge>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Subtotal</p><p class="font-medium">৳{{ selected.subtotal }}</p></div>
          <div class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Discount</p><p class="font-medium">৳{{ selected.discount }}</p></div>
          <div class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Tax</p><p class="font-medium">৳{{ selected.tax }}</p></div>
          <div class="bg-primary-50 rounded-lg p-3"><p class="label-text mb-1">Total</p><p class="font-bold text-primary-700">৳{{ selected.total_amount }}</p></div>
        </div>
        <p v-if="selected.paid_at" class="text-xs text-slate-400">Paid on {{ dayjs(selected.paid_at).format('MMM D, YYYY h:mm A') }}</p>
      </div>
    </AppModal>
  </div>
</template>