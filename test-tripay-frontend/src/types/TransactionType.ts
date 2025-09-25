export interface PaymentChannel {
  group: string
  code: string
  name: string
  type: string
  fee_merchant: {
    flat: number
    percent: number
  }
  fee_customer: {
    flat: number
    percent: number
  }
  total_fee: {
    flat: number
    percent: string
  }
  minimum_fee: number | null
  maximum_fee: number | null
  minimum_amount: number
  maximum_amount: number
  icon_url: string
  active: boolean
}

export interface TransactionFormData {
  method: string
  merchant_ref: string
  amount: number
  customer_name: string
  customer_email: string
  customer_phone: string
  order_items: Array<{
    id: number
    sku: string
    name: string
    price: number
    quantity: number
  }>
}

// Response wrapper untuk array payment channels
export interface PaymentChannelsResponse {
  success: boolean
  message: string
  data: PaymentChannel[]
}

// Grouped payment channels (untuk UI grouping)
export interface GroupedPaymentChannels {
  [groupName: string]: PaymentChannel[]
}

// Transaction response dari API TriPay
export interface TriPayTransaction {
  id: string
  reference: string
  merchant_ref: string
  payment_method: string
  payment_method_code: string
  total_amount: number
  fee_merchant: number
  fee_customer: number
  total_fee: number
  amount_received: number
  checkout_url: string
  status: 'UNPAID' | 'PAID' | 'FAILED' | 'EXPIRED' | 'REFUND'
  paid_at: string | null
  created_at: string
  updated_at: string
  expired_time: string
}

export interface CreateTransactionResponse {
  success: boolean
  message: string
  data: TriPayTransaction
}
