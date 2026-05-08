import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { useToast } from './useToast'

export function useAuth() {
  const authStore = useAuthStore()
  const router = useRouter()
  const toast = useToast()

  async function login(email: string, password: string) {
    try {
      await authStore.login(email, password)
      toast.success('Welcome back!')
      router.push(authStore.dashboardRoute)
    } catch (err: unknown) {
      const error = err as { response?: { data?: { message?: string } } }
      toast.error(error.response?.data?.message || 'Login failed. Please try again.')
      throw err
    }
  }

  async function logout() {
    await authStore.logout()
    toast.info('Logged out successfully')
    router.push('/login')
  }

  return { login, logout, authStore }
}