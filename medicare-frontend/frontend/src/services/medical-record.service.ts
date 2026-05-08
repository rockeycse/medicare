import api from './api'
import type { PaginationParams } from '@/types/api'

export default {
  async getAll(params?: PaginationParams) {
    const response = await api.get('/medical-records', { params })
    return response.data
  },

  async upload(formData: FormData) {
    const response = await api.post('/medical-records', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    return response.data
  },

  async getById(id: number) {
    const response = await api.get(`/medical-records/${id}`)
    return response.data
  },

  async download(id: number) {
    const response = await api.get(`/medical-records/${id}/download`, {
      responseType: 'blob'
    })
    return response
  },

  async delete(id: number) {
    const response = await api.delete(`/medical-records/${id}`)
    return response.data
  }
}