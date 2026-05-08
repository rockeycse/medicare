import { defineStore } from 'pinia'
import { ref } from 'vue'
import appointmentService from '@/services/appointment.service'
import type { Appointment, BookAppointmentData } from '@/types/appointment'
import type { PaginationMeta } from '@/types/api'

export const useAppointmentStore = defineStore('appointment', () => {
  const appointments = ref<Appointment[]>([])
  const currentAppointment = ref<Appointment | null>(null)
  const meta = ref<PaginationMeta | null>(null)
  const loading = ref(false)

  async function fetchAppointments(params?: Record<string, unknown>) {
    loading.value = true
    try {
      const response = await appointmentService.getAll(params)
      appointments.value = response.data || []
      meta.value = response.meta || null
    } finally {
      loading.value = false
    }
  }

  async function fetchById(id: number) {
    loading.value = true
    try {
      const response = await appointmentService.getById(id)
      currentAppointment.value = response.data
      return response.data as Appointment
    } finally {
      loading.value = false
    }
  }

  async function bookAppointment(data: BookAppointmentData) {
    const response = await appointmentService.book(data)
    return response
  }

  async function cancelAppointment(id: number) {
    const response = await appointmentService.cancel(id)
    const idx = appointments.value.findIndex((a) => a.id === id)
    if (idx !== -1) appointments.value[idx].status = 'cancelled'
    return response
  }

  async function completeAppointment(id: number) {
    const response = await appointmentService.complete(id)
    const idx = appointments.value.findIndex((a) => a.id === id)
    if (idx !== -1) appointments.value[idx].status = 'completed'
    return response
  }

  return {
    appointments,
    currentAppointment,
    meta,
    loading,
    fetchAppointments,
    fetchById,
    bookAppointment,
    cancelAppointment,
    completeAppointment
  }
})