import { defineStore } from 'pinia'
import { ref } from 'vue'
import doctorService from '@/services/doctor.service'
import type { Doctor } from '@/types/doctor'
import type { PaginationMeta } from '@/types/api'

export const useDoctorStore = defineStore('doctor', () => {
  const doctors = ref<Doctor[]>([])
  const currentDoctor = ref<Doctor | null>(null)
  const meta = ref<PaginationMeta | null>(null)
  const loading = ref(false)

  async function fetchDoctors(params?: Record<string, unknown>) {
    loading.value = true
    try {
      const response = await doctorService.getAll(params)
      doctors.value = response.data || []
      meta.value = response.meta || null
    } finally {
      loading.value = false
    }
  }

  async function fetchById(id: number) {
    loading.value = true
    try {
      const response = await doctorService.getById(id)
      currentDoctor.value = response.data
      return response.data as Doctor
    } finally {
      loading.value = false
    }
  }

  return { doctors, currentDoctor, meta, loading, fetchDoctors, fetchById }
})