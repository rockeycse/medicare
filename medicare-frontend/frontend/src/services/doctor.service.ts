import api from './api'
import type { PaginationParams } from '@/types/api'

export default {
  async getAll(params?: PaginationParams) {
    const response = await api.get('/doctors', { params })
    return response.data
  },

  async getById(id: number) {
    const response = await api.get(`/doctors/${id}`)
    return response.data
  },

  async create(data: unknown) {
    const response = await api.post('/doctors', data)
    return response.data
  },

  async update(id: number, data: unknown) {
    const response = await api.put(`/doctors/${id}`, data)
    return response.data
  },

  async delete(id: number) {
    const response = await api.delete(`/doctors/${id}`)
    return response.data
  },

  async getSchedules(id: number) {
    const response = await api.get(`/doctors/${id}/schedules`)
    return response.data
  },

  async syncSchedules(id: number, data: unknown) {
    const response = await api.post(`/doctors/${id}/schedules`, data)
    return response.data
  }
}