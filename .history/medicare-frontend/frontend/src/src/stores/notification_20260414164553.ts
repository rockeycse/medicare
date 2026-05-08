import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import notificationService from '@/services/notification.service'

export interface Notification {
  id: number
  type: string
  title: string
  message: string
  read_at: string | null
  created_at: string
  data?: Record<string, unknown>
}

export const useNotificationStore = defineStore('notification', () => {
  const notifications = ref<Notification[]>([])

  const unreadCount = computed(
    () => notifications.value.filter((n) => !n.read_at).length
  )

  async function fetchNotifications() {
    try {
      const response = await notificationService.getAll()
      notifications.value = response.data || []
    } catch {
      notifications.value = []
    }
  }

  async function markAsRead(id: number) {
    try {
      await notificationService.markAsRead(id)
      const n = notifications.value.find((n) => n.id === id)
      if (n) n.read_at = new Date().toISOString()
    } catch {
      // silent
    }
  }

  async function markAllAsRead() {
    try {
      await notificationService.markAllAsRead()
      notifications.value.forEach((n) => {
        if (!n.read_at) n.read_at = new Date().toISOString()
      })
    } catch {
      // silent
    }
  }

  async function deleteNotification(id: number) {
    try {
      await notificationService.delete(id)
      notifications.value = notifications.value.filter((n) => n.id !== id)
    } catch {
      // silent
    }
  }

  function addNotification(notification: Notification) {
    notifications.value.unshift(notification)
  }

  return {
    notifications,
    unreadCount,
    fetchNotifications,
    markAsRead,
    markAllAsRead,
    deleteNotification,
    addNotification
  }
})