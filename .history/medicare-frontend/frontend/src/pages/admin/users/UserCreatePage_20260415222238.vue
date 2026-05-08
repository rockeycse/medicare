<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import adminService from '@/services/admin.service'
import { useToast } from '@/composables/useToast'
import AppCard from '@/components/ui/AppCard.vue'
import AppInput from '@/components/ui/AppInput.vue'
import AppSelect from '@/components/ui/AppSelect.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppAlert from '@/components/ui/AppAlert.vue'

const router = useRouter()
const toast = useToast()
const loading = ref(false)
const errorMsg = ref('')
const errors = reactive<Record<string, string>>({})

const form = reactive({
  name: '', email: '', phone: '', password: '', password_confirmation: '',
  role: 'patient'
})

const roleOptions = [
  { value: 'patient', label: 'Patient' },
  { value: 'receptionist', label: 'Receptionist' },
  { value: 'admin', label: 'Admin' },
]

async function handleSubmit() {
  errorMsg.value = ''
  loading.value = true
  try {
    await adminService.createUser(form)
    toast.success('User created successfully!')
    router.push('/admin/users')
  } catch (err: unknown) {
    const e = err as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } }
    if (e.response?.data?.errors) {
      Object.entries(e.response.data.errors).forEach(([k, v]) => { errors[k] = v[0] })
    } else {
      errorMsg.value = e.response?.data?.message || 'Failed to create user'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="space-y-6 max-w-xl">
    <div>
      <h1 class="page-title">Create User</h1>
      <p class="text-sm text-slate-500 mt-1">Add a new system user</p>
    </div>

    <AppAlert v-if="errorMsg" type="error">{{ errorMsg }}</AppAlert>

    <AppCard>
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <AppSelect v-model="form.role" label="Role" :options="roleOptions" required />
        <AppInput v-model="form.name" label="Full Name" placeholder="John Doe" :error="errors.name" required />
        <AppInput v-model="form.email" label="Email" type="email" placeholder="user@example.com" :error="errors.email" required />
        <AppInput v-model="form.phone" label="Phone" type="tel" placeholder="+880 1700-000000" :error="errors.phone" />
        <AppInput v-model="form.password" label="Password" type="password" placeholder="Min. 8 characters" :error="errors.password" required />
        <AppInput v-model="form.password_confirmation" label="Confirm Password" type="password" :error="errors.password_confirmation" required />
        <div class="flex gap-3 justify-end pt-4 border-t border-slate-100">
          <AppButton variant="secondary" type="button" @click="router.back()">Cancel</AppButton>
          <AppButton variant="primary" type="submit" :loading="loading">Create User</AppButton>
        </div>
      </form>
    </AppCard>
  </div>
</template>