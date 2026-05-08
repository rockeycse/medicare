import api from './api'
import type { PaginationParams } from '@/types/api'

export default {
  async getUsers(params?: PaginationParams) {
    const response = await api.get('/admin/users', { params })
    return response.data
  },

  async createUser(data: unknown) {
    const response = await api.post('/admin/users', data)
    return response.data
  },

  async getUserById(id: number) {
    const response = await api.get(`/admin/users/${id}`)
    return response.data
  },

  async updateUser(id: number, data: unknown) {
    const response = await api.put(`/admin/users/${id}`, data)
    return response.data
  },

  async deleteUser(id: number) {
    const response = await api.delete(`/admin/users/${id}`)
    return response.data
  },

  async toggleStatus(id: number) {
    const response = await api.patch(`/admin/users/${id}/toggle-status`)
    return response.data
  }
}