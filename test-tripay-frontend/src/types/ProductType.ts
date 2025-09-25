// Product data from API response
export interface Product {
  id: number
  category_id?: number
  name: string
  sku: string
  price: number
  reference: string
  created_at: string
  updated_at: string
  deleted_at: string | null
}

// Form data for create/update product
export interface ProductFormData {
  name: string
  sku: string
  price: number
  reference: string
  category_id?: number
}
