<script setup lang="ts">
import { ref, onMounted, watch, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import { useProducts } from '@/apis'
import type { Product, ProductFormData } from '@/types/ProductType'

import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Toast from 'primevue/toast'

// Import modal components
import ProductFormModal from '@/components/product/ProductFormModal.vue'
import DeleteConfirmModal from '@/components/product/DeleteConfirmModal.vue'

const toast = useToast()
const router = useRouter()
const route = useRoute()
const { listProducts, createProduct, updateProduct, deleteProduct } = useProducts()

// State
const searchQuery = ref('')
const loading = ref(false)
const products = ref<Product[]>([])

// Product Form Modal state
const showFormModal = ref(false)
const modalMode = ref<'create' | 'edit'>('create')
const selectedProduct = ref<Product | null>(null)
const formModalRef = ref<InstanceType<typeof ProductFormModal>>()

// Delete Confirm Modal state
const showDeleteModal = ref(false)
const productToDelete = ref<Product | null>(null)
const deleteModalRef = ref<InstanceType<typeof DeleteConfirmModal>>()

// Methods
const loadProducts = async (search: string = '') => {
  loading.value = true
  try {
    const response = await listProducts({ search })
    products.value = response.data.data
  } catch {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Gagal memuat data produk',
    })
  } finally {
    loading.value = false
  }
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
  }).format(value)
}

// Update URL when search changes
const updateUrl = (search: string) => {
  const query = { ...route.query }

  if (search.trim()) {
    query.search = search
  } else {
    delete query.search
  }

  router.replace({
    path: route.path,
    query,
  })
}

// Initialize search from URL query parameter
const initializeSearch = () => {
  const searchQueryParam = route.query.search as string
  if (searchQueryParam) {
    searchQuery.value = searchQueryParam
  }
}

// Product Form Modal handlers
const openCreateModal = () => {
  modalMode.value = 'create'
  selectedProduct.value = null
  showFormModal.value = true
}

const openEditModal = (product: Product) => {
  modalMode.value = 'edit'
  selectedProduct.value = product
  showFormModal.value = true
}

const handleFormSubmit = async (formData: ProductFormData) => {
  try {
    if (modalMode.value === 'create') {
      await createProduct(formData)
      toast.add({
        severity: 'success',
        summary: 'Berhasil',
        detail: 'Produk berhasil ditambahkan',
      })
    } else if (selectedProduct.value) {
      await updateProduct(selectedProduct.value.id, formData)
      toast.add({
        severity: 'success',
        summary: 'Berhasil',
        detail: 'Produk berhasil diperbarui',
      })
    }

    showFormModal.value = false
    await loadProducts(searchQuery.value)
  } catch (error) {
    const message =
      (error as { response?: { data?: { message?: string } } })?.response?.data?.message ||
      'Terjadi kesalahan pada server'
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: message,
    })
    throw error // Re-throw to let modal handle loading state
  }
}

const handleFormCancel = () => {
  showFormModal.value = false
  selectedProduct.value = null
}

// Delete Confirm Modal handlers
const openDeleteModal = (product: Product) => {
  productToDelete.value = product
  showDeleteModal.value = true
}

const handleDeleteConfirm = async (productId: number) => {
  try {
    await deleteProduct(productId)
    toast.add({
      severity: 'success',
      summary: 'Berhasil',
      detail: 'Produk berhasil dihapus',
    })

    showDeleteModal.value = false
    productToDelete.value = null
    await loadProducts(searchQuery.value)
  } catch (error) {
    const message =
      (error as { response?: { data?: { message?: string } } })?.response?.data?.message ||
      'Terjadi kesalahan pada server'
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: message,
    })
  } finally {
    // Reset delete modal state
    if (deleteModalRef.value) {
      deleteModalRef.value.resetState()
    }
  }
}

const handleDeleteCancel = () => {
  showDeleteModal.value = false
  productToDelete.value = null
  if (deleteModalRef.value) {
    deleteModalRef.value.resetState()
  }
}

// Watchers
watch(searchQuery, (newValue) => {
  loadProducts(newValue)
  updateUrl(newValue)
})

// Lifecycle
onMounted(async () => {
  initializeSearch()
  await nextTick()
  await loadProducts(searchQuery.value)
})
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 shadow-">
      <Toast />

      <!-- Page Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Manajemen Produk</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Kelola data produk dengan mudah</p>
      </div>

      <!-- Actions Bar -->
      <div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between">
        <!-- Search Input -->
        <div class="flex-1 max-w-md">
          <div class="relative">
            <InputText
              v-model="searchQuery"
              placeholder="Cari produk..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
            />
          </div>
        </div>

        <!-- Add Button -->
        <Button
          label="Tambah Produk"
          icon="pi pi-plus"
          severity="success"
          @click="openCreateModal"
          class="bg-green-600 hover:bg-green-700 border-green-600 hover:border-green-700 dark:bg-green-700 dark:hover:bg-green-800"
        />
      </div>

      <!-- Data Table -->
      <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
      >
        <DataTable
          :value="products"
          :loading="loading"
          paginator
          :rows="10"
          :rowsPerPageOptions="[10, 20, 50]"
          tableStyle="min-width: 50rem"
          class="dark:text-gray-100"
        >
          <template #empty>
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
              <i class="pi pi-info-circle text-3xl mb-3"></i>
              <p>Tidak ada data produk ditemukan</p>
            </div>
          </template>

          <Column field="name" header="Nama" sortable class="dark:text-gray-100"></Column>
          <Column field="sku" header="SKU" sortable class="dark:text-gray-100"></Column>
          <Column
            field="reference"
            header="No. Referensi"
            sortable
            class="dark:text-gray-100"
          ></Column>
          <Column field="price" header="Harga" sortable class="dark:text-gray-100">
            <template #body="slotProps">
              <span class="font-semibold text-green-600 dark:text-green-400">
                {{ formatCurrency(slotProps.data.price) }}
              </span>
            </template>
          </Column>
          <Column header="Aksi" style="width: 10%" class="dark:text-gray-100">
            <template #body="slotProps">
              <div class="flex justify-start gap-2">
                <Button
                  icon="pi pi-pencil"
                  size="small"
                  outlined
                  severity="warning"
                  v-tooltip.top="'Edit'"
                  @click="openEditModal(slotProps.data)"
                  class="text-yellow-600 border-yellow-300 hover:bg-yellow-50 dark:text-yellow-400 dark:border-yellow-600 dark:hover:bg-yellow-900/20"
                />
                <Button
                  icon="pi pi-trash"
                  size="small"
                  outlined
                  severity="danger"
                  v-tooltip.top="'Hapus'"
                  @click="openDeleteModal(slotProps.data)"
                  class="text-red-600 border-red-300 hover:bg-red-50 dark:text-red-400 dark:border-red-600 dark:hover:bg-red-900/20"
                />
              </div>
            </template>
          </Column>
        </DataTable>
      </div>

      <!-- Product Form Modal -->
      <ProductFormModal
        ref="formModalRef"
        v-model:visible="showFormModal"
        :mode="modalMode"
        :product="selectedProduct"
        @submit="handleFormSubmit"
        @cancel="handleFormCancel"
      />

      <!-- Delete Confirmation Modal -->
      <DeleteConfirmModal
        ref="deleteModalRef"
        v-model:visible="showDeleteModal"
        :product-name="productToDelete?.name || ''"
        :product-id="productToDelete?.id || null"
        @confirm="handleDeleteConfirm"
        @cancel="handleDeleteCancel"
      />
    </div>
</template>
