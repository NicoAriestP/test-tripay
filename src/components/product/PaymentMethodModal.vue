<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useToast } from 'primevue/usetoast'
import { useTransactions } from '@/apis'
import type { Product } from '@/types/ProductType'
import type {
  PaymentChannel,
  GroupedPaymentChannels,
  TransactionFormData,
} from '@/types/TransactionType'

import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'

interface PaymentMethodModalProps {
  visible: boolean
  product: Product | null
}

interface PaymentMethodModalEmits {
  (e: 'update:visible', value: boolean): void
  (e: 'success', checkoutUrl: string): void
  (e: 'cancel'): void
}

const props = defineProps<PaymentMethodModalProps>()
const emit = defineEmits<PaymentMethodModalEmits>()

const toast = useToast()
const {
  listPaymentChannels,
  createTransaction,
  groupPaymentChannels,
  formatCurrency,
  calculateTotalAmount,
} = useTransactions()

// State
const loadingChannels = ref(false)
const creating = ref(false)
const paymentChannels = ref<PaymentChannel[]>([])
const selectedChannel = ref<PaymentChannel | null>(null)

const customerForm = ref({
  name: '',
  email: '',
  phone: '',
})

const errors = ref<Record<string, string>>({})

// Computed
const isVisible = computed({
  get: () => props.visible,
  set: (value: boolean) => emit('update:visible', value),
})

const groupedChannels = computed<GroupedPaymentChannels | null>(() => {
  if (!paymentChannels.value.length) return null
  return groupPaymentChannels(paymentChannels.value)
})

const canProceed = computed(() => {
  return (
    selectedChannel.value &&
    customerForm.value.name.trim() &&
    customerForm.value.email.trim() &&
    customerForm.value.phone.trim() &&
    !creating.value
  )
})

// Methods
const loadPaymentChannels = async () => {
  loadingChannels.value = true
  try {
    const response = await listPaymentChannels()
    if (response.success) {
      paymentChannels.value = response.data
    } else {
      throw new Error(response.message)
    }
  } catch (error) {
    console.error('Error loading payment channels:', error)
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Gagal memuat metode pembayaran',
    })
  } finally {
    loadingChannels.value = false
  }
}

const selectPaymentMethod = (channel: PaymentChannel) => {
  selectedChannel.value = channel
}

const calculateFee = (channel: PaymentChannel) => {
  if (!props.product) {
    return {
      baseAmount: 0,
      customerFee: 0,
      merchantFee: 0,
      totalFee: 0,
      totalAmount: 0,
    }
  }
  return calculateTotalAmount(props.product.price, channel)
}

const validateForm = (): boolean => {
  errors.value = {}

  if (!customerForm.value.name.trim()) {
    errors.value.name = 'Nama lengkap harus diisi'
  }

  if (!customerForm.value.email.trim()) {
    errors.value.email = 'Email harus diisi'
  } else if (!/\S+@\S+\.\S+/.test(customerForm.value.email)) {
    errors.value.email = 'Format email tidak valid'
  }

  if (!customerForm.value.phone.trim()) {
    errors.value.phone = 'Nomor telepon harus diisi'
  }

  return Object.keys(errors.value).length === 0
}

const handleCreateTransaction = async () => {
  if (!validateForm() || !selectedChannel.value || !props.product) {
    return
  }

  creating.value = true
  try {
    // Calculate total amount with fees
    const feeCalculation = calculateFee(selectedChannel.value)

    // Use product reference as merchant reference
    const merchantRef = props.product.reference

    const requestData: TransactionFormData = {
      method: selectedChannel.value.code,
      merchant_ref: merchantRef,
      amount: feeCalculation.totalAmount,
      customer_name: customerForm.value.name,
      customer_email: customerForm.value.email,
      customer_phone: customerForm.value.phone,
      order_items: [
        {
          id: props.product.id,
          sku: props.product.sku,
          name: props.product.name,
          price: props.product.price,
          quantity: 1,
        },
      ],
    }

    const response = await createTransaction(requestData)

    if (response.success && response.data.checkout_url) {
      toast.add({
        severity: 'success',
        summary: 'Berhasil',
        detail: 'Transaksi berhasil dibuat',
      })

      emit('success', response.data.checkout_url)
      resetForm()
    } else {
      throw new Error(response.message || 'Gagal membuat transaksi')
    }
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
    creating.value = false
  }
}

const handleCancel = () => {
  resetForm()
  emit('cancel')
}

const resetForm = () => {
  customerForm.value = {
    name: '',
    email: '',
    phone: '',
  }
  selectedChannel.value = null
  errors.value = {}
}

const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  img.style.display = 'none'
}

// Watchers
watch(
  () => props.visible,
  (newValue) => {
    if (newValue && !paymentChannels.value.length) {
      loadPaymentChannels()
    }
    if (!newValue) {
      resetForm()
    }
  },
)

// Lifecycle
onMounted(() => {
  if (props.visible) {
    loadPaymentChannels()
  }
})
</script>

<template>
  <Dialog
    v-model:visible="isVisible"
    modal
    :closable="true"
    :style="{ width: '600px' }"
    class="payment-method-modal"
  >
    <template #header>
      <div class="flex items-center gap-3">
        <i class="pi pi-credit-card text-blue-600 dark:text-blue-400"></i>
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            Pilih Metode Pembayaran
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ product?.name }}
          </p>
        </div>
      </div>
    </template>

    <div class="space-y-4">
      <!-- Product Summary -->
      <div
        class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700"
      >
        <div class="flex justify-between items-center mb-2">
          <span class="font-medium text-gray-900 dark:text-gray-100">{{ product?.name }}</span>
          <span class="font-semibold text-green-600 dark:text-green-400">
            {{ formatCurrency(product?.price || 0) }}
          </span>
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">SKU: {{ product?.sku }}</div>
      </div>

      <!-- Customer Information Form -->
      <div class="space-y-3">
        <h4 class="font-medium text-gray-900 dark:text-gray-100">Informasi Pembeli</h4>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Nama Lengkap <span class="text-red-500">*</span>
          </label>
          <InputText
            v-model="customerForm.name"
            :class="{ 'p-invalid': errors.name }"
            placeholder="Masukkan nama lengkap"
            class="w-full"
          />
          <small v-if="errors.name" class="text-red-500">{{ errors.name }}</small>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Email <span class="text-red-500">*</span>
          </label>
          <InputText
            v-model="customerForm.email"
            :class="{ 'p-invalid': errors.email }"
            placeholder="Masukkan email"
            type="email"
            class="w-full"
          />
          <small v-if="errors.email" class="text-red-500">{{ errors.email }}</small>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            No. Telepon <span class="text-red-500">*</span>
          </label>
          <InputText
            v-model="customerForm.phone"
            :class="{ 'p-invalid': errors.phone }"
            placeholder="Masukkan nomor telepon"
            class="w-full"
          />
          <small v-if="errors.phone" class="text-red-500">{{ errors.phone }}</small>
        </div>
      </div>

      <!-- Payment Summary (when payment method is selected) -->
      <div
        v-if="selectedChannel && product"
        class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-700"
      >
        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">Ringkasan Pembayaran</h4>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Harga Produk</span>
            <span class="font-medium text-gray-900 dark:text-gray-100">
              {{ formatCurrency(product.price) }}
            </span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Biaya Admin</span>
            <span class="font-medium text-gray-900 dark:text-gray-100">
              <span v-if="calculateFee(selectedChannel).customerFee > 0">
                {{ formatCurrency(calculateFee(selectedChannel).customerFee) }}
              </span>
              <span v-else class="text-green-600 dark:text-green-400">Gratis</span>
            </span>
          </div>
          <hr class="border-blue-200 dark:border-blue-600" />
          <div class="flex justify-between text-base">
            <span class="font-semibold text-gray-900 dark:text-gray-100">Total Pembayaran</span>
            <span class="font-bold text-blue-600 dark:text-blue-400">
              {{ formatCurrency(calculateFee(selectedChannel).totalAmount) }}
            </span>
          </div>
        </div>
      </div>

      <!-- Payment Methods -->
      <div class="space-y-3">
        <h4 class="font-medium text-gray-900 dark:text-gray-100">Metode Pembayaran</h4>

        <div v-if="loadingChannels" class="text-center py-4">
          <i class="pi pi-spinner pi-spin text-2xl text-blue-600 dark:text-blue-400"></i>
          <p class="text-gray-600 dark:text-gray-400 mt-2">Memuat metode pembayaran...</p>
        </div>

        <div v-else-if="groupedChannels" class="space-y-4 max-h-80 overflow-y-auto">
          <div v-for="(channels, groupName) in groupedChannels" :key="groupName">
            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              {{ String(groupName).toUpperCase() }}
            </h5>

            <div class="grid grid-cols-1 gap-2">
              <div
                v-for="channel in channels.filter((c: PaymentChannel) => c.active)"
                :key="channel.code"
                @click="selectPaymentMethod(channel)"
                :class="[
                  'p-3 border rounded-lg cursor-pointer transition-colors',
                  selectedChannel?.code === channel.code
                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-400'
                    : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700',
                ]"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <img
                      :src="channel.icon_url"
                      :alt="channel.name"
                      class="w-8 h-8 object-contain"
                      @error="handleImageError"
                    />
                    <div>
                      <div class="font-medium text-gray-900 dark:text-gray-100">
                        {{ channel.name }}
                      </div>
                      <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span v-if="calculateFee(channel).customerFee > 0">
                          Fee: {{ formatCurrency(calculateFee(channel).customerFee) }}
                        </span>
                        <span v-else class="text-green-600 dark:text-green-400">
                          Bebas Biaya Admin
                        </span>
                      </div>
                    </div>
                  </div>

                  <div class="text-right">
                    <div class="font-semibold text-gray-900 dark:text-gray-100">
                      {{ formatCurrency(calculateFee(channel).totalAmount) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Total Bayar</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-4 text-gray-500 dark:text-gray-400">
          <i class="pi pi-exclamation-triangle text-2xl mb-2"></i>
          <p>Gagal memuat metode pembayaran</p>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-3">
        <Button
          label="Batal"
          icon="pi pi-times"
          outlined
          severity="secondary"
          @click="handleCancel"
          :disabled="creating"
          class="text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700"
        />
        <Button
          label="Lanjut Bayar"
          icon="pi pi-arrow-right"
          :loading="creating"
          :disabled="!canProceed"
          @click="handleCreateTransaction"
          class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 border-blue-600 hover:border-blue-700"
        />
      </div>
    </template>
  </Dialog>
</template>

<style scoped>
.payment-method-modal :deep(.p-dialog) {
  background: white;
  border: 1px solid #e5e7eb;
}

.payment-method-modal :deep(.p-dialog-header) {
  background: white;
  color: #111827;
  border-bottom: 1px solid #e5e7eb;
  padding: 1.5rem;
}

.payment-method-modal :deep(.p-dialog-content) {
  background: white;
  padding: 1.5rem;
}

.payment-method-modal :deep(.p-dialog-footer) {
  background: white;
  border-top: 1px solid #e5e7eb;
  padding: 1.5rem;
}

/* Dark mode styles */
:root.dark .payment-method-modal :deep(.p-dialog) {
  background: #1f2937;
  border: 1px solid #374151;
}

:root.dark .payment-method-modal :deep(.p-dialog-header) {
  background: #1f2937;
  color: #f9fafb;
  border-bottom: 1px solid #374151;
}

:root.dark .payment-method-modal :deep(.p-dialog-content) {
  background: #1f2937;
}

:root.dark .payment-method-modal :deep(.p-dialog-footer) {
  background: #1f2937;
  border-top: 1px solid #374151;
}
</style>
