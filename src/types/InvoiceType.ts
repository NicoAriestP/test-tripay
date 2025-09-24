export interface Invoice {
  id: number
  product_id: number
  tripay_reference: string
  buyer_email: string
  buyer_phone: string
  raw_response: JSON
}
