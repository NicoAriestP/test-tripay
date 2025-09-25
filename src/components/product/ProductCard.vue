<script setup lang="ts">
import { ref } from 'vue'
import type { Product } from '@/types/ProductType'
import Button from 'primevue/button'

interface ProductCardProps {
  product: Product
}

interface ProductCardEmits {
  (e: 'purchase', product: Product): void
}

const props = defineProps<ProductCardProps>()
const emit = defineEmits<ProductCardEmits>()

// State
const purchasing = ref(false)

// Methods
const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount)
}

const formatDate = (dateString: string): string => {
  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  }).format(new Date(dateString))
}

const handlePurchase = () => {
  purchasing.value = true
  emit('purchase', props.product)

  // Reset purchasing state after a short delay to provide visual feedback
  setTimeout(() => {
    purchasing.value = false
  }, 500)
}
</script>

<template>
  <div
    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
  >
    <!-- Product Image -->
    <div class="aspect-w-16 aspect-h-9 bg-gray-100 dark:bg-gray-700">
      <div class="flex items-center justify-center h-48">
        <i class="pi pi-image text-4xl text-gray-400"></i>
      </div>
    </div>

    <!-- Product Info -->
    <div class="p-4">
      <div class="space-y-3">
        <!-- Product Name & SKU -->
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 line-clamp-2">
            {{ product.name }}
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ product.sku }}</p>
        </div>

        <!-- Reference -->
        <div class="text-sm text-gray-600 dark:text-gray-300">
          <span class="font-medium">Ref:</span> {{ product.reference }}
        </div>

        <!-- Price -->
        <div class="flex items-center justify-between">
          <div class="text-2xl font-bold text-green-600 dark:text-green-400">
            {{ formatCurrency(product.price) }}
          </div>

          <!-- Buy Button -->
          <Button
            label="Beli"
            icon="pi pi-shopping-cart"
            size="small"
            :loading="purchasing"
            @click="handlePurchase"
            class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 border-blue-600 hover:border-blue-700 px-4 py-2"
          />
        </div>

        <!-- Additional Info -->
        <div class="pt-3 border-t border-gray-200 dark:border-gray-600">
          <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <span>Dibuat: {{ formatDate(product.created_at) }}</span>
            <span v-if="product.updated_at !== product.created_at">
              Diperbarui: {{ formatDate(product.updated_at) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import type { Product } from '@/types/ProductType'
import Button from 'primevue/button'

interface ProductCardProps {
  product: Product
}

interface ProductCardEmits {
  (e: 'purchase', product: Product): void
}

const props = defineProps<ProductCardProps>()
const emit = defineEmits<ProductCardEmits>()

// State
const purchasing = ref(false)

// Methods
const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount)
}

const formatDate = (dateString: string): string => {
  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  }).format(new Date(dateString))
}

const handlePurchase = () => {
  purchasing.value = true
  emit('purchase', props.product)

  // Reset purchasing state after a short delay to provide visual feedback
  setTimeout(() => {
    purchasing.value = false
  }, 500)
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.aspect-w-16 {
  position: relative;
  width: 100%;
}

.aspect-h-9::before {
  content: '';
  display: block;
  padding-bottom: calc(9 / 16 * 100%);
}

.aspect-w-16 > * {
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
</style>
