import type { Doctor } from './doctor'

export interface Patient {
  id: number
  user_id: number
  date_of_birth: string | null
  gender: string | null
  blood_group: string | null
  address: string | null
}

export interface User {
  id: number
  name: string
  email: string
  phone: string | null
  avatar: string | null
  role: 'admin' | 'doctor' | 'patient' | 'receptionist'
  is_active: boolean
  created_at: string
  doctor?: Doctor
  patient?: Patient
}

export interface RegisterData {
  name: string
  email: string
  phone: string
  password: string
  password_confirmation: string
  role: 'patient' | 'receptionist'
}

export interface LoginData {
  email: string
  password: string
}

export interface AuthResponse {
  token: string
  user: User
}