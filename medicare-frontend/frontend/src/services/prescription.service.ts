import api from './api'
import type { PaginationParams } from '@/types/api'
import type { BookAppointmentData } from '@/types/appointment'

export default {
  async getAll(params?: PaginationParams) {
    const response = await api.get('/appointments', { params })
    return response.data
  },

  async getById(id: number) {
    const response = await api.get(`/appointments/${id}`)
    return response.data
  },

  async book(data: BookAppointmentData) {
    const response = await api.post('/appointments', data)
    return response.data
  },

  async cancel(id: number) {
    const response = await api.patch(`/appointments/${id}/cancel`)
    return response.data
  },

  async complete(id: number) {
    const response = await api.patch(`/appointments/${id}/complete`)
    return response.data
  }
}