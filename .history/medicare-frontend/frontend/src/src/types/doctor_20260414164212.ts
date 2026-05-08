import type { User } from './user'

export interface Specialization {
  id: number
  name: string
  slug: string
  description: string | null
}

export interface DoctorSchedule {
  id: number
  day_of_week: string
  start_time: string
  end_time: string
  max_patients: number
  is_available: boolean
}

export interface Doctor {
  id: number
  license_number: string
  bio: string | null
  experience_years: number
  consultation_fee: number
  is_available: boolean
  user: User
  specialization: Specialization
  schedules?: DoctorSchedule[]
}

export interface CreateDoctorData {
  name: string
  email: string
  phone: string
  password: string
  password_confirmation: string
  license_number: string
  specialization_id: number
  experience_years: number
  consultation_fee: number
  bio?: string
}