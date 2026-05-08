export interface Payment {
  id: number
  amount: number
  method: 'cash' | 'card' | 'mobile_banking'
  transaction_id: string | null
  status: string
  created_at: string
}

export interface Invoice {
  id: number
  invoice_number: string
  subtotal: number
  discount: number
  tax: number
  total_amount: number
  status: 'unpaid' | 'paid' | 'refunded'
  paid_at: string | null
  payments?: Payment[]
  created_at: string
}

export interface CreateInvoiceData {
  appointment_id: number
  subtotal: number
  discount?: number
  tax?: number
}

export interface PayInvoiceData {
  method: 'cash' | 'card' | 'mobile_banking'
  transaction_id?: string
}