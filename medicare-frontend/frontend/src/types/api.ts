export interface ApiResponse<T> {
  success: boolean
  message: string
  data: T
  meta?: PaginationMeta
  errors?: Record<string, string[]>
}

export interface PaginationMeta {
  total: number
  current_page: number
  last_page: number
  per_page?: number
}

export interface PaginationParams {
  page?: number
  per_page?: number
  search?: string
  [key: string]: string | number | undefined
}