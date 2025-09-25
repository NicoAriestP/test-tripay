<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useToast } from 'primevue/usetoast'
import { useProducts } from '@/apis'
import type { Product } from '@/types/ProductType'

import Toast from 'primevue/toast'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import ProductCard from '@/components/product/ProductCard.vue'
import PaymentMethodModal from '@/components/product/PaymentMethodModal.vue'

const toast = useToast()
const { listProducts } = useProducts()

// State
const loading = ref(false)
const products = ref<Product[]>([])
const searchQuery = ref('')
const filteredProducts = ref<Product[]>([])

// Payment Modal State
const showPaymentModal = ref(false)
const selectedProduct = ref<Product | null>(null)

// Methods
const loadProducts = async () => {
  loading.value = true
  try {
    const response = await listProducts()
    products.value = response.data.data
    filteredProducts.value = products.value
  } catch (error) {
    console.error('Error loading products:', error)
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Gagal memuat daftar produk',
    })
  } finally {
    loading.value = false
  }
}

const searchProducts = () => {
  const query = searchQuery.value.toLowerCase().trim()
  if (query === '') {
    filteredProducts.value = products.value
  } else {
    filteredProducts.value = products.value.filter(
      (product) =>
        product.name.toLowerCase().includes(query) ||
        product.sku.toLowerCase().includes(query) ||
        product.reference.toLowerCase().includes(query),
    )
  }
}

const handlePurchase = (product: Product) => {
  selectedProduct.value = product
  showPaymentModal.value = true
}

const handlePaymentSuccess = (checkoutUrl: string) => {
  showPaymentModal.value = false
  selectedProduct.value = null

  toast.add({
    severity: 'success',
    summary: 'Transaksi Berhasil',
    detail: 'Redirecting ke halaman pembayaran...',
  })

  // Redirect ke checkout URL TriPay
  setTimeout(() => {
    window.open(checkoutUrl, '_blank')
  }, 1000)
}

const handlePaymentCancel = () => {
  showPaymentModal.value = false
  selectedProduct.value = null
}

const resetSearch = () => {
  searchQuery.value = ''
  searchProducts()
}

// Lifecycle
onMounted(() => {
  loadProducts()
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <Toast />

    <!-- Hero Section -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
          <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Selamat Datang di Tripay E-Commerce
          </h1>
          <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Platform e-commerce dengan integrasi pembayaran TriPay untuk pengalaman berbelanja yang
            mudah dan aman
          </p>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Search & Filter Section -->
      <div class="mb-8">
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Produk Kami</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Temukan produk yang Anda cari</p>
          </div>

          <!-- Search Input -->
          <div class="w-full sm:w-80">
            <div class="relative">
              <InputText
                v-model="searchQuery"
                @input="searchProducts"
                placeholder="Cari produk, SKU, atau referensi..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Products Grid -->
      <div v-if="loading" class="text-center py-12">
        <i class="pi pi-spinner pi-spin text-4xl text-blue-600 dark:text-blue-400 mb-4"></i>
        <p class="text-gray-600 dark:text-gray-400">Memuat produk...</p>
      </div>

      <div v-else-if="filteredProducts.length === 0" class="text-center py-12">
        <i class="pi pi-info-circle text-4xl text-gray-400 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
          {{ searchQuery ? 'Produk tidak ditemukan' : 'Belum ada produk' }}
        </h3>
        <p class="text-gray-600 dark:text-gray-400">
          {{
            searchQuery
              ? 'Coba gunakan kata kunci lain untuk pencarian'
              : 'Produk akan ditampilkan di sini ketika sudah tersedia'
          }}
        </p>
        <Button
          v-if="searchQuery"
          label="Reset Pencarian"
          icon="pi pi-refresh"
          outlined
          @click="resetSearch"
          class="mt-4"
        />
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <ProductCard
          v-for="product in filteredProducts"
          :key="product.id"
          :product="product"
          @purchase="handlePurchase"
        />
      </div>
    </div>

    <!-- Payment Method Modal -->
    <PaymentMethodModal
      v-model:visible="showPaymentModal"
      :product="selectedProduct"
      @success="handlePaymentSuccess"
      @cancel="handlePaymentCancel"
    />
  </div>
</template>
