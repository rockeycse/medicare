import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notification'
import { useUIStore } from '@/stores/ui'

declare global {
  interface Window {
    Pusher: unknown
    Echo: {
      private: (channel: string) => {
        listen: (event: string, cb: (data: unknown) => void) => { listen: (...args: unknown[]) => unknown }
      }
      disconnect: () => void
    }
  }
}

export function useWebSocket() {
  const authStore = useAuthStore()
  const notificationStore = useNotificationStore()
  const uiStore = useUIStore()

  async function connect() {
    try {
      const [{ default: Echo }, { default: Pusher }] = await Promise.all([
        import('laravel-echo'),
        import('pusher-js')
      ])

      window.Pusher = Pusher

      window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        wssPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: import.meta.env.VITE_API_URL + '/api/v1/broadcasting/auth',
        auth: {
          headers: { Authorization: `Bearer ${authStore.token}` }
        }
      })

      if (authStore.isDoctor && authStore.user?.doctor) {
        window.Echo.private(`doctor.${authStore.user.doctor.id}`)
          .listen('.appointment.booked', (data: unknown) => {
            const d = data as { message?: string }
            uiStore.addToast(d.message || 'New appointment booked!', 'info')
            notificationStore.fetchNotifications()
          })
      }

      if (authStore.isPatient && authStore.user?.patient) {
        window.Echo.private(`patient.${authStore.user.patient.id}`)
          .listen('.appointment.booked', (data: unknown) => {
            const d = data as { message?: string }
            uiStore.addToast(d.message || 'Appointment confirmed!', 'success')
          })
          .listen('.invoice.paid', (data: unknown) => {
            const d = data as { message?: string }
            uiStore.addToast(d.message || 'Payment received!', 'success')
          })
      }
    } catch {
      // WebSocket not critical - fail silently
    }
  }

  function disconnect() {
    try {
      window.Echo?.disconnect()
    } catch {
      // silent
    }
  }

  return { connect, disconnect }
}