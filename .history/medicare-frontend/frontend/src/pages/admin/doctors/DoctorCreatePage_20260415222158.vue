<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import doctorService from '@/services/doctor.service'
import { useToast } from '@/composables/useToast'
import AppCard from '@/components/ui/AppCard.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppSelect from '@/components/ui/AppSelect.vue'
import AppTextarea from '@/components/ui/AppTextarea.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppAlert from '@/components/ui/AppAlert.vue'
import type { Specialization } from '@/types/doctor'

const router = useRouter()
const toast = useToast()
const loading = ref(false)
const specializations = ref<Specialization[]>([])
const errorMsg = ref('')
const errors = reactive<Record<string, string>>({})

const form = reactive({
  name: '', email: '', phone: '', password: '', password_confirmation: '',
  license_number: '', specialization_id: '', experience_years: '',
  consultation_fee: '', bio: ''
})

onMounted(async () => {
  try {
    const res = await doctorService.getAll({ per_page: 100 })
    const unique = new Map<number, Specialization>()
    ;(res.data as { specialization: Specialization }[]).forEach((d) => {
      if (d.specialization) unique.set(d.specialization.id, d.specialization)
    })
    specializations.value = [...unique.values()]
  } catch { /* silent */ }
})

const specializationOptions = () =>
  specializations.value.map(s => ({ value: s.id, label: s.name }))

async function handleSubmit() {
  Object.keys(errors).forEach(k => delete errors[k])
  errorMsg.value = ''
  loading.value = true
  try {
    await doctorService.create({
      ...form,
      specialization_id: Number(form.specialization_id),
      experience_years: Number(form.experience_years),
      consultation_fee: Number(form.consultation_fee),
    })
    toast.success('Doctor created successfully!')
    router.push('/admin/doctors')
  } catch (err: unknown) {
    const e = err as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } }
    const apiErrors = e.response?.data?.errors
    if (apiErrors) {
      Object.entries(apiErrors).forEach(([k, v]) => { errors[k] = v[0] })
    } else {
      errorMsg.value = e.response?.data?.message || 'Failed to create doctor'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="space-y-6 max-w-4xl">
    <div>
      <h1 class="page-title">Add New Doctor</h1>
      <p class="text-sm text-slate-500 mt-1">Create a new doctor account</p>
    </div>

    <AppAlert v-if="errorMsg" type="error">{{ errorMsg }}</AppAlert>

    <form @submit.prevent="handleSubmit" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- User info -->
      <AppCard>
        <h3 class="section-title mb-6">Personal Information</h3>
        <div class="space-y-4">
          <AppInput v-model="form.name" label="Full Name" placeholder="Dr. John Doe" :error="errors.name" required />
          <AppInput v-model="form.email" label="Email Address" type="email" placeholder="doctor@example.com" :error="errors.email" required />
          <AppInput v-model="form.phone" label="Phone Number" type="tel" placeholder="+880 1700-000000" :error="errors.phone" />
          <AppInput v-model="form.password" label="Password" type="password" placeholder="Min. 8 characters" :error="errors.password" required />
          <AppInput v-model="form.password_confirmation" label="Confirm Password" type="password" placeholder="Repeat password" :error="errors.password_confirmation" required />
        </div>
      </AppCard>

      <!-- Doctor info -->
      <AppCard>
        <h3 class="section-title mb-6">Professional Information</h3>
        <div class="space-y-4">
          <AppSelect
            v-model="form.specialization_id"
            label="Specialization"
            :options="specializationOptions()"
            placeholder="Select specialization"
            :error="errors.specialization_id"
            required
          />
          <AppInput v-model="form.license_number" label="License Number" placeholder="MD-123456" :error="errors.license_number" required />
          <AppInput v-model="form.experience_years" label="Years of Experience" type="number" placeholder="5" :error="errors.experience_years" required />
          <AppInput v-model="form.consultation_fee" label="Consultation Fee (৳)" type="number" placeholder="500" :error="errors.consultation_fee" required />
          <AppTextarea v-model="form.bio" label="Bio" placeholder="Brief description about the doctor..." :rows="3" />
        </div>
      </AppCard>

      <!-- Actions -->
      <div class="lg:col-span-2 flex gap-3 justify-end">
        <AppButton variant="secondary" type="button" @click="router.back()">Cancel</AppButton>
        <AppButton variant="primary" type="submit" :loading="loading">Create Doctor</AppButton>
      </div>
    </form>
  </div>
</template>