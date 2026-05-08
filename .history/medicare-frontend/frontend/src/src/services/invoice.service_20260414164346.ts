import api from './api'
import type { PaginationParams } from '@/types/api'
import type { CreateInvoiceData, PayInvoiceData } from '@/types/invoice'

export default {
  async getAll(params?: PaginationParams) {
    const response = await api.get('/invoices', { params })
    return response.data
  },

  async getById(id: number) {
    const response = await api.get(`/invoices/${id}`)
    return response.data
  },

  async generate(data: CreateInvoiceData) {
    const response = await api.post('/invoices', data)
    return response.data
  },

  async pay(id: number, data: PayInvoiceData) {
    const response = await api.post(`/invoices/${id}/pay`, data)
    return response.data
  },

  async refund(id: number) {
    const response = await api.post(`/invoices/${id}/refund`)
    return response.data
  }
}