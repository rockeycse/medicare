import type { Doctor } from './doctor'
import type { Patient } from './user'

export interface PrescriptionMedicine {
  id: number
  medicine_name: string
  dosage: string
  frequency: string
  duration: string
  instructions: string | null
}

export interface Prescription {
  id: number
  diagnosis: string
  advice: string | null
  next_visit_date: string | null
  doctor?: Doctor
  patient?: Patient
  medicines: PrescriptionMedicine[]
  created_at: string
}

export interface CreatePrescriptionData {
  appointment_id: number
  diagnosis: string
  advice?: string
  next_visit_date?: string
  medicines: {
    medicine_name: string
    dosage: string
    frequency: string
    duration: string
    instructions?: string
  }[]
}