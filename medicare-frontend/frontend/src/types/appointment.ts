import type { Doctor } from './doctor'
import type { Patient } from './user'
import type { Prescription } from './prescription'
import type { Invoice } from './invoice'

export interface Appointment {
  id: number
  appointment_date: string
  appointment_time: string
  serial_number: number
  status: 'pending' | 'confirmed' | 'completed' | 'cancelled'
  type: 'in-person' | 'online'
  symptoms: string | null
  doctor?: Doctor
  patient?: Patient
  prescription?: Prescription
  invoice?: Invoice
  created_at: string
}

export interface BookAppointmentData {
  doctor_id: number
  appointment_date: string
  appointment_time: string
  type: 'in-person' | 'online'
  symptoms?: string
}