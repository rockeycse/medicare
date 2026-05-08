import type { Router } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

export function setupGuards(router: Router) {
  router.beforeEach(async (to, _from, next) => {
    const authStore = useAuthStore()

    // Initialize: fetch user if token exists but user not loaded
    if (authStore.token && !authStore.user) {
      await authStore.fetchUser()
    }

    // Guest only routes (login, register)
    if (to.meta.guestOnly && authStore.isAuthenticated) {
      return next(authStore.dashboardRoute)
    }

    // Requires authentication
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
      return next({ name: 'login', query: { redirect: to.fullPath } })
    }

    // Role-based access
    if (to.meta.role && authStore.user?.role !== to.meta.role) {
      return next({ name: 'forbidden' })
    }

    next()
  })
}