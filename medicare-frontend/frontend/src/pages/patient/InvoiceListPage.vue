<script setup lang="ts">
import { ref, onMounted } from 'vue'
import invoiceService from '@/services/invoice.service'
import type { Invoice } from '@/types/invoice'
import AppCard from '@/components/ui/AppCard.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppModal from '@/components/ui/AppModal.vue'
import AppSkeleton from '@/components/ui/AppSkeleton.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import AppSelect from '@/components/ui/AppSelect.vue'
import AppInput from '@/components/ui/AppInput.vue'
import { useToast } from '@/composables/useToast'
import { CreditCardIcon, EyeIcon } from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const toast = useToast()
const invoices = ref<Invoice[]>([])
const loading = ref(true)
const currentPage = ref(1)
const lastPage = ref(1)
const total = ref(0)
const viewModal = ref(false)
const payModal = ref(false)
const selected = ref<Invoice | null>(null)
const paying = ref(false)
const payMethod = ref('cash')
const transactionId = ref('')

const methodOptions = [
  { value: 'cash', label: 'Cash' },
  { value: 'card', label: 'Card' },
  { value: 'mobile_banking', label: 'Mobile Banking' },
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

async function handlePay() {
  if (!selected.value) return
  paying.value = true
  try {
    await invoiceService.pay(selected.value.id, {
      method: payMethod.value as 'cash' | 'card' | 'mobile_banking',
      transaction_id: transactionId.value || undefined
    })
    toast.success('Payment successful!')
    payModal.value = false
    fetch()
  } catch { toast.error('Payment failed') } finally { paying.value = false }
}
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="page-title">My Invoices</h1>
      <p class="text-sm text-slate-500 mt-1">View and pay your invoices</p>
    </div>

    <AppCard>
      <AppSkeleton v-if="loading" type="table" :rows="5" />
      <div v-else-if="!invoices.length" class="flex flex-col items-center py-16 gap-2">
        <CreditCardIcon class="h-12 w-12 text-slate-300" />
        <p class="text-slate-400 text-sm">No invoices found</p>
      </div>
      <div v-else class="space-y-3">
        <div
          v-for="inv in invoices" :key="inv.id"
          class="flex items-center gap-4 rounded-xl border border-slate-100 p-4 hover:bg-slate-50 transition-colors"
        >
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-lg">🧾</div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-700">{{ inv.invoice_number }}</p>
            <p class="text-xs text-slate-400 mt-0.5">{{ dayjs(inv.created_at).format('MMM D, YYYY') }}</p>
            <p class="text-base font-bold text-slate-800 mt-1">৳{{ inv.total_amount.toLocaleString() }}</p>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <AppBadge :variant="inv.status">{{ inv.status }}</AppBadge>
            <button @click="selected = inv; viewModal = true" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
              <EyeIcon class="h-4 w-4" />
            </button>
            <AppButton v-if="inv.status === 'unpaid'" size="sm" variant="primary" @click="selected = inv; payModal = true">
              Pay Now
            </AppButton>
          </div>
        </div>
      </div>
      <AppPagination v-if="lastPage > 1" :current-page="currentPage" :last-page="lastPage" :total="total" @page-change="(p) => { currentPage = p; fetch() }" />
    </AppCard>

    <!-- View modal -->
    <AppModal v-model="viewModal" title="Invoice Details" size="md">
      <div v-if="selected" class="space-y-3 text-sm">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
          <span class="font-mono font-bold text-slate-800">{{ selected.invoice_number }}</span>
          <AppBadge :variant="selected.status">{{ selected.status }}</AppBadge>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Subtotal</p><p class="font-medium">৳{{ selected.subtotal }}</p></div>
          <div class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Discount</p><p class="font-medium">৳{{ selected.discount }}</p></div>
          <div class="bg-slate-50 rounded-lg p-3"><p class="label-text mb-1">Tax</p><p class="font-medium">৳{{ selected.tax }}</p></div>
          <div class="bg-primary-50 rounded-lg p-3"><p class="label-text mb-1">Total</p><p class="font-bold text-primary-700 text-lg">৳{{ selected.total_amount }}</p></div>
        </div>
        <AppButton v-if="selected.status === 'unpaid'" variant="primary" class="w-full" @click="viewModal = false; payModal = true">Pay Now</AppButton>
      </div>
    </AppModal>

    <!-- Pay modal -->
    <AppModal v-model="payModal" title="Make Payment" size="sm">
      <div v-if="selected" class="space-y-4">
        <div class="bg-primary-50 rounded-xl p-4 text-center">
          <p class="text-sm text-primary-600">Amount Due</p>
          <p class="text-3xl font-bold text-primary-700 mt-1">৳{{ selected.total_amount.toLocaleString() }}</p>
        </div>
        <AppSelect v-model="payMethod" label="Payment Method" :options="methodOptions" />
        <AppInput v-if="payMethod !== 'cash'" v-model="transactionId" label="Transaction ID" placeholder="Enter transaction ID" />
        <div class="flex gap-3">
          <AppButton variant="secondary" class="flex-1" @click="payModal = false">Cancel</AppButton>
          <AppButton variant="primary" class="flex-1" :loading="paying" @click="handlePay">Confirm Payment</AppButton>
        </div>
      </div>
    </AppModal>
  </div>
</template>