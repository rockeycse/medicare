import { defineStore } from 'pinia'
import { ref } from 'vue'

export interface Toast {
  id: string
  message: string
  type: 'success' | 'error' | 'warning' | 'info'
  duration: number
}

export const useUIStore = defineStore('ui', () => {
  const sidebarCollapsed = ref<boolean>(
    localStorage.getItem('sidebarCollapsed') === 'true'
  )
  const toasts = ref<Toast[]>([])

  function toggleSidebar() {
    sidebarCollapsed.value = !sidebarCollapsed.value
    localStorage.setItem('sidebarCollapsed', String(sidebarCollapsed.value))
  }

  function addToast(
    message: string,
    type: Toast['type'] = 'info',
    duration = 4000
  ) {
    const id = Date.now().toString() + Math.random().toString(36).slice(2)
    const toast: Toast = { id, message, type, duration }
    toasts.value.push(toast)
    setTimeout(() => removeToast(id), duration)
    return id
  }

  function removeToast(id: string) {
    const index = toasts.value.findIndex((t) => t.id === id)
    if (index !== -1) toasts.value.splice(index, 1)
  }

  return { sidebarCollapsed, toasts, toggleSidebar, addToast, removeToast }
})