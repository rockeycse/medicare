import api from './api'

export default {
  async getAll() {
    const response = await api.get('/notifications')
    return response.data
  },

  async markAsRead(id: number) {
    const response = await api.patch(`/notifications/${id}/read`)
    return response.data
  },

  async markAllAsRead() {
    const response = await api.patch('/notifications/read-all')
    return response.data
  },

  async delete(id: number) {
    const response = await api.delete(`/notifications/${id}`)
    return response.data
  }
}