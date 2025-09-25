import type { Product } from './ProductType'

// TriPay Order Item structure
export interface TriPayOrderItem {
  sku: string
  name: string
  price: number
  quantity: number
  subtotal: number
  image_url: string | null
  product_url: string | null
}

// TriPay Payment Instructions
export interface PaymentInstruction {
  title: string
  steps: string[]
}

// TriPay Raw Response Data
export interface TriPayResponseData {
  amount: number
  status: string
  pay_url: string | null
  pay_code: string
  reference: string
  total_fee: number
  return_url: string | null
  order_items: TriPayOrderItem[]
  callback_url: string
  checkout_url: string
  expired_time: number
  fee_customer: number
  fee_merchant: number
  instructions: PaymentInstruction[]
  merchant_ref: string
  payment_name: string
  customer_name: string
  customer_email: string
  customer_phone: string
  payment_method: string
  amount_received: number
  payment_selection_type: string
}

// TriPay Raw Response
export interface TriPayRawResponse {
  data: TriPayResponseData
  message: string
  success: boolean
}

// Main Invoice interface
export interface Invoice {
  id: number
  product_id: number
  tripay_reference: string
  buyer_email: string
  buyer_phone: string
  raw_response: TriPayRawResponse
  created_at: string
  updated_at: string
  deleted_at: string | null
  product: Product
}

// Invoice list API response
export interface InvoiceListResponse {
  success: boolean
  data: Invoice[]
  message?: string
}
