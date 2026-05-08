import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import authService from '@/services/auth.service'
import type { User, RegisterData } from '@/types/user'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isDoctor = computed(() => user.value?.role === 'doctor')
  const isPatient = computed(() => user.value?.role === 'patient')
  const isReceptionist = computed(() => user.value?.role === 'receptionist')
  const userRole = computed(() => user.value?.role)

  const dashboardRoute = computed(() => {
    switch (user.value?.role) {
      case 'admin': return '/admin/dashboard'
      case 'doctor': return '/doctor/dashboard'
      case 'patient': return '/patient/dashboard'
      case 'receptionist': return '/receptionist/dashboard'
      default: return '/'
    }
  })

  async function login(email: string, password: string) {
    const response = await authService.login(email, password)
    token.value = (response.data as { token: string; user: User }).token
    user.value = (response.data as { token: string; user: User }).user
    localStorage.setItem('token', token.value!)
    return response
  }

  async function register(data: RegisterData) {
    const response = await authService.register(data)
    token.value = (response.data as { token: string; user: User }).token
    user.value = (response.data as { token: string; user: User }).user
    localStorage.setItem('token', token.value!)
    return response
  }

  async function logout() {
    try {
      await authService.logout()
    } catch {
      // ignore errors on logout
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
    }
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      const response = await authService.getMe()
      user.value = response.data as User
    } catch {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    isDoctor,
    isPatient,
    isReceptionist,
    userRole,
    dashboardRoute,
    login,
    register,
    logout,
    fetchUser
  }
})