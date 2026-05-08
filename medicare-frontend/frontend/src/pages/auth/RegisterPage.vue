<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import AppInput from '@/components/ui/AppInput.vue'
import AppButton from '@/components/ui/AppButton.vue'
import AppAlert from '@/components/ui/AppAlert.vue'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const form = reactive({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  role: 'patient' as 'patient' | 'receptionist',
  terms: false
})

const loading = ref(false)
const showPassword = ref(false)
const showConfirm = ref(false)
const errorMsg = ref('')
const errors = reactive<Record<string, string>>({})

function validate() {
  Object.keys(errors).forEach(k => delete errors[k])
  if (!form.name) errors.name = 'Name is required'
  if (!form.email) errors.email = 'Email is required'
  if (!form.phone) errors.phone = 'Phone is required'
  if (!form.password) errors.password = 'Password is required'
  else if (form.password.length < 8) errors.password = 'Minimum 8 characters'
  if (form.password !== form.password_confirmation)
    errors.password_confirmation = 'Passwords do not match'
  if (!form.terms) errors.terms = 'You must accept the terms'
  return Object.keys(errors).length === 0
}

async function handleRegister() {
  errorMsg.value = ''
  if (!validate()) return
  loading.value = true
  try {
    await authStore.register(form)
    toast.success('Account created successfully!')
    router.push(authStore.dashboardRoute)
  } catch (err: unknown) {
    const e = err as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } }
    const apiErrors = e.response?.data?.errors
    if (apiErrors) {
      Object.entries(apiErrors).forEach(([k, v]) => { errors[k] = v[0] })
    } else {
      errorMsg.value = e.response?.data?.message || 'Registration failed. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex">
    <!-- Left panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-400 relative overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 right-10 h-72 w-72 rounded-full bg-white" />
        <div class="absolute bottom-10 left-10 h-56 w-56 rounded-full bg-white" />
      </div>
      <div class="relative z-10 flex flex-col justify-center px-16 text-white">
        <div class="flex items-center gap-3 mb-12">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M13 7h-2v3H8v2h3v3h2v-3h3v-2h-3z"/>
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
            </svg>
          </div>
          <span class="text-2xl font-bold">MediCare</span>
        </div>
        <h1 class="text-4xl font-bold leading-tight mb-4">
          Join MediCare<br />Today
        </h1>
        <p class="text-white/80 text-lg mb-10">
          Create your account and get access to world-class healthcare management.
        </p>
        <ul class="space-y-4">
          <li v-for="item in ['Book appointments instantly', 'Access your medical records', 'Get digital prescriptions', '24/7 healthcare support']" :key="item"
            class="flex items-center gap-3 text-white/90">
            <div class="h-6 w-6 rounded-full bg-white/20 flex items-center justify-center shrink-0">
              <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
            {{ item }}
          </li>
        </ul>
      </div>
    </div>

    <!-- Right panel -->
    <div class="flex flex-1 items-center justify-center px-6 py-12 bg-slate-50 overflow-y-auto">
      <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-slate-800">Create account</h2>
            <p class="text-slate-500 mt-1 text-sm">Fill in your details to get started</p>
          </div>

          <AppAlert v-if="errorMsg" type="error" class="mb-6">{{ errorMsg }}</AppAlert>

          <form @submit.prevent="handleRegister" class="space-y-4">
            <!-- Role selection -->
            <div>
              <label class="label-text mb-2 block">Account Type</label>
              <div class="grid grid-cols-2 gap-3">
                <button
                  v-for="role in [{ value: 'patient', label: '🏥 Patient' }, { value: 'receptionist', label: '💼 Receptionist' }]"
                  :key="role.value"
                  type="button"
                  @click="form.role = role.value as 'patient' | 'receptionist'"
                  :class="[
                    'rounded-lg border-2 py-2.5 text-sm font-medium transition-all',
                    form.role === role.value
                      ? 'border-primary-500 bg-primary-50 text-primary-700'
                      : 'border-slate-200 text-slate-600 hover:border-slate-300'
                  ]"
                >
                  {{ role.label }}
                </button>
              </div>
            </div>

            <AppInput v-model="form.name" label="Full Name" placeholder="John Doe" :error="errors.name" required />
            <AppInput v-model="form.email" label="Email Address" type="email" placeholder="you@example.com" :error="errors.email" required />
            <AppInput v-model="form.phone" label="Phone Number" type="tel" placeholder="+880 1700-000000" :error="errors.phone" required />

            <div class="flex flex-col gap-1.5">
              <label class="label-text">Password <span class="text-red-500">*</span></label>
              <div class="relative">
                <input v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="Min. 8 characters" class="input-base pr-10" />
                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                  <EyeSlashIcon v-if="showPassword" class="h-4 w-4" />
                  <EyeIcon v-else class="h-4 w-4" />
                </button>
              </div>
              <p v-if="errors.password" class="text-xs text-red-500">{{ errors.password }}</p>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="label-text">Confirm Password <span class="text-red-500">*</span></label>
              <div class="relative">
                <input v-model="form.password_confirmation" :type="showConfirm ? 'text' : 'password'" placeholder="Repeat password" class="input-base pr-10" />
                <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                  <EyeSlashIcon v-if="showConfirm" class="h-4 w-4" />
                  <EyeIcon v-else class="h-4 w-4" />
                </button>
              </div>
              <p v-if="errors.password_confirmation" class="text-xs text-red-500">{{ errors.password_confirmation }}</p>
            </div>

            <div>
              <label class="flex items-start gap-2 cursor-pointer">
                <input type="checkbox" v-model="form.terms" class="mt-0.5 rounded border-slate-300 text-primary-500" />
                <span class="text-sm text-slate-600">
                  I agree to the
                  <a href="#" class="text-primary-600 hover:underline">Terms of Service</a>
                  and
                  <a href="#" class="text-primary-600 hover:underline">Privacy Policy</a>
                </span>
              </label>
              <p v-if="errors.terms" class="text-xs text-red-500 mt-1">{{ errors.terms }}</p>
            </div>

            <AppButton type="submit" variant="primary" size="lg" :loading="loading" class="w-full">
              Create Account
            </AppButton>
          </form>

          <p class="mt-6 text-center text-sm text-slate-500">
            Already have an account?
            <router-link to="/login" class="text-primary-600 font-medium hover:text-primary-700">Sign in</router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>