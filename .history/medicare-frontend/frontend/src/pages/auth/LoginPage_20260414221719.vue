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

const form = reactive({ email: '', password: '' })
const loading = ref(false)
const showPassword = ref(false)
const errorMsg = ref('')
const errors = reactive<Record<string, string>>({})

async function handleLogin() {
  errorMsg.value = ''
  Object.keys(errors).forEach(k => delete errors[k])

  if (!form.email) { errors.email = 'Email is required'; return }
  if (!form.password) { errors.password = 'Password is required'; return }

  loading.value = true
  try {
    await authStore.login(form.email, form.password)
    toast.success('Welcome back!')
    router.push(authStore.dashboardRoute)
  } catch (err: unknown) {
    const e = err as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } }
    const apiErrors = e.response?.data?.errors
    if (apiErrors) {
      Object.entries(apiErrors).forEach(([k, v]) => { errors[k] = v[0] })
    } else {
      errorMsg.value = e.response?.data?.message || 'Invalid credentials. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex">
    <!-- Left panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-600 via-primary-500 to-sky-400 relative overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-20 h-64 w-64 rounded-full bg-white" />
        <div class="absolute bottom-20 right-20 h-48 w-48 rounded-full bg-white" />
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-96 w-96 rounded-full bg-white" />
      </div>
      <div class="relative z-10 flex flex-col justify-center px-16 text-white">
        <div class="flex items-center gap-3 mb-12">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M13 7h-2v3H8v2h3v3h2v-3h3v-2h-3z"/>
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
            </svg>
          </div>
          <span class="text-2xl font-bold">MediCare</span>
        </div>
        <h1 class="text-4xl font-bold leading-tight mb-4">
          Your Health,<br />Our Priority
        </h1>
        <p class="text-white/80 text-lg leading-relaxed mb-8">
          Access your medical dashboard to manage appointments, prescriptions, and more.
        </p>
        <div class="grid grid-cols-3 gap-4">
          <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
            <div class="text-2xl font-bold">200+</div>
            <div class="text-white/70 text-sm">Doctors</div>
          </div>
          <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
            <div class="text-2xl font-bold">50K+</div>
            <div class="text-white/70 text-sm">Patients</div>
          </div>
          <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center">
            <div class="text-2xl font-bold">24/7</div>
            <div class="text-white/70 text-sm">Support</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right panel -->
    <div class="flex flex-1 items-center justify-center px-6 py-12 bg-slate-50">
      <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
          <div class="mb-8">
            <h2 class="text-2xl font-bold text-slate-800">Sign in</h2>
            <p class="text-slate-500 mt-1 text-sm">Enter your credentials to access your account</p>
          </div>

          <AppAlert v-if="errorMsg" type="error" class="mb-6">{{ errorMsg }}</AppAlert>

          <form @submit.prevent="handleLogin" class="space-y-5">
            <AppInput
              v-model="form.email"
              label="Email Address"
              type="email"
              placeholder="you@example.com"
              :error="errors.email"
              required
            />

            <div class="flex flex-col gap-1.5">
              <label class="label-text">
                Password <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="••••••••"
                  class="input-base pr-10"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                >
                  <EyeSlashIcon v-if="showPassword" class="h-4 w-4" />
                  <EyeIcon v-else class="h-4 w-4" />
                </button>
              </div>
              <p v-if="errors.password" class="text-xs text-red-500">{{ errors.password }}</p>
            </div>

            <div class="flex items-center justify-between">
              <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                <input type="checkbox" class="rounded border-slate-300 text-primary-500" />
                Remember me
              </label>
              <button type="button" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                Forgot password?
              </button>
            </div>

            <AppButton type="submit" variant="primary" size="lg" :loading="loading" class="w-full">
              Sign In
            </AppButton>
          </form>

          <p class="mt-6 text-center text-sm text-slate-500">
            Don't have an account?
            <router-link to="/register" class="text-primary-600 font-medium hover:text-primary-700">
              Register here
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>