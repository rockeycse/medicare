<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import prescriptionService from '@/services/prescription.service'
import appointmentService from '@/services/appointment.service'
import type { Appointment } from '@/types/appointment'
import AppCard from '@/components/ui/AppCard.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppTextarea from '@/components/ui/AppTextarea.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppSelect from '@/components/ui/AppSelect.vue'
import AppAlert from '@/components/ui/AppAlert.vue'
import { useToast } from '@/composables/useToast'
import { PlusIcon, TrashIcon, PrinterIcon } from '@heroicons/vue/24/outline'
import dayjs from 'dayjs'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const loading = ref(false)
const loadingApts = ref(true)
const submitted = ref(false)
const createdId = ref<number | null>(null)
const appointments = ref<Appointment[]>([])
const errors = reactive<Record<string, string>>({})
const errorMsg = ref('')

interface Medicine {
  medicine_name: string
  dosage: string
  frequency: string
  duration: string
  instructions: string
}

const form = reactive({
  appointment_id: route.query.appointment_id ? String(route.query.appointment_id) : '',
  diagnosis: '',
  advice: '',
  next_visit_date: '',
  medicines: [] as Medicine[]
})

function addMedicine() {
  form.medicines.push({ medicine_name: '', dosage: '', frequency: '', duration: '', instructions: '' })
}

function removeMedicine(idx: number) {
  form.medicines.splice(idx, 1)
}

onMounted(async () => {
  try {
    const res = await appointmentService.getAll({ status: 'completed', per_page: 100 })
    appointments.value = (res.data || []).filter((a: Appointment) => !a.prescription)
  } finally {
    loadingApts.value = false
  }
  addMedicine()
})

const aptOptions = computed(() =>
  appointments.value.map(a => ({
    value: a.id,
    label: `#${a.serial_number} — ${dayjs(a.appointment_date).format('MMM D')} ${a.appointment_time}`
  }))
)

const selectedApt = computed(() =>
  appointments.value.find(a => a.id === Number(form.appointment_id)) ?? null
)

function validate() {
  Object.keys(errors).forEach(k => delete errors[k])
  if (!form.appointment_id) errors.appointment_id = 'Select an appointment'
  if (!form.diagnosis.trim()) errors.diagnosis = 'Diagnosis is required'
  if (!form.medicines.length) errors.medicines = 'Add at least one medicine'
  form.medicines.forEach((m, i) => {
    if (!m.medicine_name) errors[`med_${i}_name`] = 'Name required'
    if (!m.dosage) errors[`med_${i}_dosage`] = 'Dosage required'
    if (!m.frequency) errors[`med_${i}_freq`] = 'Frequency required'
    if (!m.duration) errors[`med_${i}_dur`] = 'Duration required'
  })
  return Object.keys(errors).length === 0
}

async function handleSubmit() {
  if (!validate()) return
  loading.value = true
  errorMsg.value = ''
  try {
    const res = await prescriptionService.create({
      appointment_id: Number(form.appointment_id),
      diagnosis: form.diagnosis,
      advice: form.advice || undefined,
      next_visit_date: form.next_visit_date || undefined,
      medicines: form.medicines.map(m => ({
        medicine_name: m.medicine_name,
        dosage: m.dosage,
        frequency: m.frequency,
        duration: m.duration,
        instructions: m.instructions || undefined,
      }))
    })
    createdId.value = res.data?.id ?? null
    submitted.value = true
    toast.success('Prescription created successfully!')
  } catch (err: unknown) {
    const e = err as { response?: { data?: { message?: string } } }
    errorMsg.value = e.response?.data?.message || 'Failed to create prescription'
  } finally {
    loading.value = false
  }
}

function handlePrint() {
  window.print()
}

const frequencyOptions = [
  { value: 'Once daily', label: 'Once daily' },
  { value: 'Twice daily', label: 'Twice daily' },
  { value: 'Three times daily', label: 'Three times daily' },
  { value: 'Four times daily', label: 'Four times daily' },
  { value: 'Every 8 hours', label: 'Every 8 hours' },
  { value: 'As needed', label: 'As needed' },
]
</script>

<template>
  <div class="space-y-6 max-w-4xl">
    <div>
      <h1 class="page-title">Write Prescription</h1>
      <p class="text-sm text-slate-500 mt-1">Create a prescription for a completed appointment</p>
    </div>

    <!-- Success state -->
    <AppCard v-if="submitted" class="text-center py-8">
      <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
        <svg class="h-8 w-8 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <h2 class="text-xl font-bold text-slate-800 mb-2">Prescription Created!</h2>
      <p class="text-slate-500 mb-6">The prescription has been saved successfully.</p>
      <div class="flex gap-3 justify-center">
        <AppButton variant="outline" @click="handlePrint">
          <PrinterIcon class="h-4 w-4" />
          Print
        </AppButton>
        <AppButton variant="primary" @click="router.push('/doctor/prescriptions')">
          View All Prescriptions
        </AppButton>
      </div>
    </AppCard>

    <template v-else>
      <AppAlert v-if="errorMsg" type="error">{{ errorMsg }}</AppAlert>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Appointment selection -->
        <AppCard>
          <h3 class="section-title mb-4">Select Appointment</h3>
          <AppSelect
            v-model="form.appointment_id"
            label="Completed Appointment"
            :options="aptOptions"
            placeholder="Select appointment..."
            :error="errors.appointment_id"
            required
          />
          <div v-if="selectedApt" class="mt-4 rounded-xl bg-primary-50 border border-primary-100 p-4 text-sm">
            <p class="font-medium text-primary-800">Appointment #{{ selectedApt.serial_number }}</p>
            <p class="text-primary-600 mt-1">
              {{ dayjs(selectedApt.appointment_date).format('MMMM D, YYYY') }} at {{ selectedApt.appointment_time }}
            </p>
            <p v-if="selectedApt.symptoms" class="text-primary-600 mt-1">Symptoms: {{ selectedApt.symptoms }}</p>
          </div>
        </AppCard>

        <!-- Diagnosis -->
        <AppCard>
          <h3 class="section-title mb-4">Diagnosis & Notes</h3>
          <div class="space-y-4">
            <AppTextarea
              v-model="form.diagnosis"
              label="Diagnosis"
              placeholder="Enter the patient's diagnosis..."
              :rows="3"
              :error="errors.diagnosis"
              required
            />
            <AppTextarea
              v-model="form.advice"
              label="Advice / Instructions"
              placeholder="Additional advice for the patient..."
              :rows="2"
            />
            <AppInput
              v-model="form.next_visit_date"
              label="Next Visit Date"
              type="date"
              :hint="'Leave empty if no follow-up needed'"
            />
          </div>
        </AppCard>

        <!-- Medicines -->
        <AppCard>
          <div class="flex items-center justify-between mb-4">
            <h3 class="section-title">Medicines</h3>
            <AppButton variant="outline" size="sm" type="button" @click="addMedicine">
              <PlusIcon class="h-4 w-4" />
              Add Medicine
            </AppButton>
          </div>

          <p v-if="errors.medicines" class="text-xs text-red-500 mb-3">{{ errors.medicines }}</p>

          <div v-if="!form.medicines.length" class="rounded-xl border-2 border-dashed border-slate-200 py-8 text-center text-sm text-slate-400">
            No medicines added yet. Click "Add Medicine" to start.
          </div>

          <div class="space-y-4">
            <div
              v-for="(med, idx) in form.medicines"
              :key="idx"
              class="rounded-xl border border-slate-100 p-4 bg-slate-50 relative"
            >
              <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Medicine {{ idx + 1 }}</span>
                <button
                  type="button"
                  @click="removeMedicine(idx)"
                  class="flex h-6 w-6 items-center justify-center rounded text-red-400 hover:bg-red-50 transition-colors"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <AppInput
                  v-model="med.medicine_name"
                  label="Medicine Name"
                  placeholder="e.g. Paracetamol 500mg"
                  :error="errors[`med_${idx}_name`]"
                  required
                />
                <AppInput
                  v-model="med.dosage"
                  label="Dosage"
                  placeholder="e.g. 1 tablet"
                  :error="errors[`med_${idx}_dosage`]"
                  required
                />
                <AppSelect
                  v-model="med.frequency"
                  label="Frequency"
                  :options="frequencyOptions"
                  placeholder="Select frequency"
                  :error="errors[`med_${idx}_freq`]"
                />
                <AppInput
                  v-model="med.duration"
                  label="Duration"
                  placeholder="e.g. 5 days, 2 weeks"
                  :error="errors[`med_${idx}_dur`]"
                  required
                />
                <div class="sm:col-span-2">
                  <AppInput
                    v-model="med.instructions"
                    label="Special Instructions"
                    placeholder="e.g. Take after meals"
                  />
                </div>
              </div>
            </div>
          </div>
        </AppCard>

        <!-- Actions -->
        <div class="flex gap-3 justify-end">
          <AppButton variant="secondary" type="button" @click="router.back()">Cancel</AppButton>
          <AppButton variant="primary" type="submit" :loading="loading">
            Create Prescription
          </AppButton>
        </div>
      </form>
    </template>
  </div>
</template>