import axios from 'axios'

export default function useProducts() {
  const API_PATH = '/api/products'

  async function listProducts() {
    return await axios.get(API_PATH, { params: { search: '' } })
  }

  async function createProduct(data: any) {
    return await axios.post(API_PATH, data)
  }

  async function updateProduct(id: number, data: any) {
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
