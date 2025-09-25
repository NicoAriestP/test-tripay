<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue'
import { useToast } from 'primevue/usetoast'
import { useProducts } from '@/apis'
import type { Product, ProductFormData } from '@/types/ProductType'

import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import InputNumber from 'primevue/inputnumber'

interface Props {
  visible: boolean
  product?: Product | null
  mode: 'create' | 'edit'
}

interface Emits {
  (e: 'update:visible', value: boolean): void
  (e: 'success'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const toast = useToast()
const { createProduct, updateProduct } = useProducts()

const loading = ref(false)
const form = ref<ProductFormData>({
  name: '',
  sku: '',
  price: 0,
  reference: '',
  category_id: undefined,
})

const errors = ref<Record<string, string>>({})

// Computed properties
const dialogTitle = computed(() => (props.mode === 'create' ? 'Tambah Produk' : 'Edit Produk'))
const submitLabel = computed(() => (props.mode === 'create' ? 'Tambah' : 'Perbarui'))

// Reset form ketika modal dibuka/tutup
const resetForm = () => {
  if (props.mode === 'edit' && props.product) {
    form.value = {
      name: props.product.name,
      sku: props.product.sku,
      price: props.product.price,
      reference: props.product.reference,
      category_id: props.product.category_id,
    }
  } else {
    form.value = {
      name: '',
      sku: '',
      price: 0,
      reference: '',
      category_id: undefined,
    }
  }
  errors.value = {}
}

// Validasi form
const validateForm = (): boolean => {
  errors.value = {}

  if (!form.value.name.trim()) {
    errors.value.name = 'Nama produk harus diisi'
  }

  if (!form.value.sku.trim()) {
    errors.value.sku = 'SKU harus diisi'
  }

  if (!form.value.reference.trim()) {
    errors.value.reference = 'No. Referensi harus diisi'
  }

  if (form.value.price <= 0) {
    errors.value.price = 'Harga harus lebih besar dari 0'
  }

  return Object.keys(errors.value).length === 0
}

// Handle submit form
const handleSubmit = async () => {
  if (!validateForm()) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Mohon perbaiki kesalahan pada form',
    })
    return
  }

  loading.value = true
  try {
    if (props.mode === 'create') {
      await createProduct(form.value)
      toast.add({
        severity: 'success',
        summary: 'Berhasil',
        detail: 'Produk berhasil ditambahkan',
      })
    } else {
      await updateProduct(props.product!.id, form.value)
      toast.add({
        severity: 'success',
        summary: 'Berhasil',
        detail: 'Produk berhasil diperbarui',
      })
    }

    emit('success')
    emit('update:visible', false)
  } catch (error) {
    const message =
      (error as { response?: { data?: { message?: string } } }).response?.data?.message ||
      'Terjadi kesalahan pada server'
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: message,
    })
  } finally {
    loading.value = false
  }
}

// Handle tutup modal
const handleClose = () => {
  emit('update:visible', false)
}

// Watch untuk reset form ketika modal dibuka
watch(
  () => props.visible,
  (newValue) => {
    if (newValue) {
      nextTick(() => {
        resetForm()
      })
    }
  },
)
</script>

<template>
  <Dialog
    :visible="props.visible"
    :modal="true"
    :header="dialogTitle"
    :style="{ width: '32rem' }"
    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    @update:visible="handleClose"
    class="product-form-modal"
  >
    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
          Nama Produk <span class="text-red-500">*</span>
        </label>
        <InputText
          id="name"
          v-model="form.name"
          :class="{ 'p-invalid': errors.name }"
          placeholder="Masukkan nama produk"
          class="w-full"
        />
        <small v-if="errors.name" class="text-red-500 dark:text-red-400">{{ errors.name }}</small>
      </div>

      <div>
        <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
          SKU <span class="text-red-500">*</span>
        </label>
        <InputText
          id="sku"
          v-model="form.sku"
          :class="{ 'p-invalid': errors.sku }"
          placeholder="Masukkan SKU produk"
          class="w-full"
        />
        <small v-if="errors.sku" class="text-red-500 dark:text-red-400">{{ errors.sku }}</small>
      </div>

      <div>
        <label
          for="reference"
          class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1"
        >
          No. Referensi <span class="text-red-500">*</span>
        </label>
        <InputText
          id="reference"
          v-model="form.reference"
          :class="{ 'p-invalid': errors.reference }"
          placeholder="Masukkan no. referensi"
          class="w-full"
        />
        <small v-if="errors.reference" class="text-red-500 dark:text-red-400">{{
          errors.reference
        }}</small>
      </div>

      <div>
        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
          Harga <span class="text-red-500">*</span>
        </label>
        <InputNumber
          id="price"
          v-model="form.price"
          :class="{ 'p-invalid': errors.price }"
          mode="currency"
          currency="IDR"
          locale="id-ID"
          :min="0"
          placeholder="0"
          class="w-full"
        />
        <small v-if="errors.price" class="text-red-500 dark:text-red-400">{{ errors.price }}</small>
      </div>
    </form>

    <template #footer>
      <div class="flex justify-end gap-2">
        <Button
          label="Batal"
          severity="secondary"
          @click="handleClose"
          :disabled="loading"
          class="text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700"
        />
        <Button
          :label="submitLabel"
          :loading="loading"
          @click="handleSubmit"
          class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white"
        />
      </div>
    </template>
  </Dialog>
</template>

<style scoped>
.product-form-modal :deep(.p-dialog) {
  background: white;
  border: 1px solid #e5e7eb;
}

.product-form-modal :deep(.p-dialog-header) {
  background: white;
  color: #111827;
  border-bottom: 1px solid #e5e7eb;
}

.product-form-modal :deep(.p-dialog-content) {
  background: white;
}

.product-form-modal :deep(.p-dialog-footer) {
  background: white;
  border-top: 1px solid #e5e7eb;
}

/* Dark mode styles */
:root.dark .product-form-modal :deep(.p-dialog) {
  background: #1f2937;
  border: 1px solid #374151;
}

:root.dark .product-form-modal :deep(.p-dialog-header) {
  background: #1f2937;
  color: #f9fafb;
  border-bottom: 1px solid #374151;
}

:root.dark .product-form-modal :deep(.p-dialog-content) {
  background: #1f2937;
}

:root.dark .product-form-modal :deep(.p-dialog-footer) {
  background: #1f2937;
  border-top: 1px solid #374151;
}
</style>
