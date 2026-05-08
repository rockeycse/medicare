import { useUIStore } from '@/stores/ui'

export function useToast() {
  const uiStore = useUIStore()

  return {
    success: (message: string, duration = 4000) =>
      uiStore.addToast(message, 'success', duration),
    error: (message: string, duration = 5000) =>
      uiStore.addToast(message, 'error', duration),
    warning: (message: string, duration = 4000) =>
      uiStore.addToast(message, 'warning', duration),
    info: (message: string, duration = 4000) =>
      uiStore.addToast(message, 'info', duration),
  }
}