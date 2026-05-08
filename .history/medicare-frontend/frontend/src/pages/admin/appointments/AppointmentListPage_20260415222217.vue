<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import doctorService from '@/services/doctor.service'
import { useToast } from '@/composables/useToast'
import AppCard from '@/components/ui/AppCard.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppSelect from '@/components/ui/AppSelect.vue'
import AppTextarea from '@/components/ui/AppTextarea.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppSkeleton from '@/components/ui/AppSkeleton.vue'
import type { Specialization } from '@/types/doctor'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const loading = ref(true)
const saving = ref(false)
const specializations = ref<Specialization[]>([])
const errors = reactive<Record<string, string>>({})

const form = reactive({
  experience_years: '',
  consultation_fee: '',
  bio: '',
  specialization_id: '',
  is_available: true,
})

onMounted(async () => {
  try {
    const [docRes, allRes] = await Promise.all([
      doctorService.getById(Number(route.params.id)),
      doctorService.getAll({ per_page: 100 })
    ])
    const doc = docRes.data
    form.experience_years = String(doc.experience_years)
    form.consultation_fee = String(doc.consultation_fee)
    form.bio = doc.bio || ''
    form.specialization_id = String(doc.specialization?.id || '')
    form.is_available = doc.is_available

    const unique = new Map<number, Specialization>()
    ;(allRes.data as { specialization: Specialization }[]).forEach((d) => {
      if (d.specialization) unique.set(d.specialization.id, d.specialization)
    })
    specializations.value = [...unique.values()]
  } finally {
    loading.value = false
  }
})

async function handleSubmit() {
  saving.value = true
  try {
    await doctorService.update(Number(route.params.id), {
      ...form,
      specialization_id: Number(form.specialization_id),
      experience_years: Number(form.experience_years),
      consultation_fee: Number(form.consultation_fee),
    })
    toast.success('Doctor updated successfully!')
    router.push('/admin/doctors')
  } catch (err: unknown) {
    const e = err as { response?: { data?: { errors?: Record<string, string[]> } } }
    if (e.response?.data?.errors) {
      Object.entries(e.response.data.errors).forEach(([k, v]) => { errors[k] = v[0] })
    } else {
      toast.error('Failed to update doctor')
    }
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="space-y-6 max-w-2xl">
    <div>
      <h1 class="page-title">Edit Doctor</h1>
      <p class="text-sm text-slate-500 mt-1">Update doctor's professional information</p>
    </div>

    <AppCard v-if="loading"><AppSkeleton type="card" :lines="5" /></AppCard>

    <AppCard v-else>
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <AppSelect
          v-model="form.specialization_id"
          label="Specialization"
          :options="specializations.map(s => ({ value: s.id, label: s.name }))"
          :error="errors.specialization_id"
        />
        <AppInput v-model="form.experience_years" label="Years of Experience" type="number" :error="errors.experience_years" />
        <AppInput v-model="form.consultation_fee" label="Consultation Fee (৳)" type="number" :error="errors.consultation_fee" />
        <AppTextarea v-model="form.bio" label="Bio" :rows="4" />
        <div class="flex items-center gap-3">
          <label class="label-text">Availability</label>
          <button
            type="button"
            @click="form.is_available = !form.is_available"
            :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.is_available ? 'bg-primary-500' : 'bg-slate-200']"
          >
            <span :class="['inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform', form.is_available ? 'translate-x-6' : 'translate-x-1']" />
          </button>
          <span class="text-sm text-slate-600">{{ form.is_available ? 'Available' : 'Unavailable' }}</span>
        </div>
        <div class="flex gap-3 justify-end pt-4 border-t border-slate-100">
          <AppButton variant="secondary" type="button" @click="router.back()">Cancel</AppButton>
          <AppButton variant="primary" type="submit" :loading="saving">Save Changes</AppButton>
        </div>
      </form>
    </AppCard>
  </div>
</template>