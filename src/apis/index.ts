import axios from 'axios'
import type { ProductFormData } from '@/types/ProductType'

export function useProducts() {
  const API_PATH = '/api/products'

  async function listProducts() {
    return await axios.get(API_PATH, { params: { search: '' } })
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

  async function listPaymentChannels() {
    return await axios.get(`${API_PATH}/payment-channels`)
  }

  async function createTransaction(product_id: number, channel_code: string) {
    return await axios.post(`${API_PATH}/create`, { product_id, channel_code })
  }

  return {
    listPaymentChannels,
    createTransaction,
  }
}
