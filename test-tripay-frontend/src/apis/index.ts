import axios from 'axios'
import type { ProductFormData } from '@/types/ProductType'
import type {
  PaymentChannelsResponse,
  TransactionFormData,
  CreateTransactionResponse,
  GroupedPaymentChannels,
  PaymentChannel,
} from '@/types/TransactionType'
import type { InvoiceListResponse } from '@/types/InvoiceType'

export function useProducts() {
  const API_PATH = '/api/products'

  async function listProducts(params: { search?: string } = {}) {
    return await axios.get(API_PATH, { params })
  }

  async function createProduct(data: ProductFormData) {
    return await axios.post(API_PATH, data)
  }

  async function updateProduct(id: number, data: ProductFormData) {
    return await axios.put(`${API_PATH}/${id}`, data)
  }

  async function deleteProduct(id: number) {
    return await axios.delete(`${API_PATH}/${id}`)
  }

  return {
    listProducts,
    createProduct,
    updateProduct,
    deleteProduct,
  }
}

export function useTransactions() {
  const API_PATH = '/api/transactions'

  async function listPaymentChannels(): Promise<PaymentChannelsResponse> {
    const response = await axios.get(`${API_PATH}/payment-channels`)
    return response.data
  }

  async function createTransaction(data: TransactionFormData): Promise<CreateTransactionResponse> {
    const response = await axios.post(`${API_PATH}/create`, data)
    return response.data
  }

  // Helper function untuk group payment channels berdasarkan group
  function groupPaymentChannels(channels: PaymentChannel[]): GroupedPaymentChannels {
    return channels.reduce((grouped: GroupedPaymentChannels, channel) => {
      const group = channel.group
      if (!grouped[group]) {
        grouped[group] = []
      }
      grouped[group].push(channel)
      return grouped
    }, {})
  }

  // Helper function untuk format currency
  function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
    }).format(amount)
  }

  // Helper function untuk calculate total amount with fees
  function calculateTotalAmount(
    amount: number,
    channel: PaymentChannel,
  ): {
    baseAmount: number
    customerFee: number
    merchantFee: number
    totalFee: number
    totalAmount: number
  } {
    // Customer fee (yang dibayar oleh customer)
    const customerFeeFlat = channel.fee_customer.flat
    const customerFeePercent = (amount * channel.fee_customer.percent) / 100
    const customerFee = customerFeeFlat + customerFeePercent

    // Merchant fee (yang dibayar oleh merchant)
    const merchantFeeFlat = channel.fee_merchant.flat
    const merchantFeePercent = (amount * channel.fee_merchant.percent) / 100
    const merchantFee = merchantFeeFlat + merchantFeePercent

    // Total fee (gabungan merchant + customer fee untuk informasi)
    const totalFeeFlat = channel.total_fee.flat
    const totalFeePercent = (amount * parseFloat(channel.total_fee.percent)) / 100
    const totalFee = totalFeeFlat + totalFeePercent

    // Apply minimum and maximum fee if specified
    let finalCustomerFee = customerFee
    if (channel.minimum_fee && finalCustomerFee < channel.minimum_fee) {
      finalCustomerFee = channel.minimum_fee
    }
    if (channel.maximum_fee && finalCustomerFee > channel.maximum_fee) {
      finalCustomerFee = channel.maximum_fee
    }

    return {
      baseAmount: amount,
      customerFee: finalCustomerFee,
      merchantFee,
      totalFee,
      totalAmount: amount + finalCustomerFee,
    }
  }

  return {
    listPaymentChannels,
    createTransaction,
    groupPaymentChannels,
    formatCurrency,
    calculateTotalAmount,
  }
}

export function useInvoices() {
  const API_PATH = '/api/invoices'

  async function listInvoices(params: { search?: string } = {}): Promise<InvoiceListResponse> {
    const response = await axios.get(API_PATH, { params })
    return response.data
  }

  // Helper function untuk format currency
  function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
    }).format(amount)
  }

  // Helper function untuk format date
  function formatDate(dateString: string): string {
    return new Intl.DateTimeFormat('id-ID', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(new Date(dateString))
  }

  return {
    listInvoices,
    formatCurrency,
    formatDate,
  }
}
