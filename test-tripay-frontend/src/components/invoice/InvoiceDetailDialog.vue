<script setup lang="ts">
import { computed } from 'vue'
import { useInvoices } from '@/apis'
import type { Invoice } from '@/types/InvoiceType'

import Dialog from 'primevue/dialog'
import Button from 'primevue/button'

interface InvoiceDetailDialogProps {
  visible: boolean
  invoice: Invoice | null
}

interface InvoiceDetailDialogEmits {
  (e: 'update:visible', value: boolean): void
  (e: 'close'): void
}

const props = defineProps<InvoiceDetailDialogProps>()
const emit = defineEmits<InvoiceDetailDialogEmits>()

const { formatCurrency, formatDate } = useInvoices()

// Computed
const isVisible = computed({
  get: () => props.visible,
  set: (value: boolean) => emit('update:visible', value),
})

// Methods
const handleClose = () => {
  emit('close')
}
</script>

<template>
  <Dialog
    v-model:visible="isVisible"
    :header="`Detail Invoice #${invoice?.id}`"
    modal
    class="w-full md:w-[800px]"
  >
    <div v-if="invoice" class="space-y-6">
      <!-- Invoice Header -->
      <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Informasi Invoice</h3>
            <div class="space-y-1 text-sm">
              <div><span class="font-medium">Invoice ID:</span> #{{ invoice.id }}</div>
              <div>
                <span class="font-medium">TriPay Reference:</span>
                {{ invoice.tripay_reference }}
              </div>
              <div>
                <span class="font-medium">Tanggal:</span>
                {{ formatDate(invoice.created_at) }}
              </div>
            </div>
          </div>
          <div>
            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Customer</h3>
            <div class="space-y-1 text-sm">
              <div>
                <span class="font-medium">Nama:</span>
                {{ invoice.raw_response.data.customer_name }}
              </div>
              <div><span class="font-medium">Email:</span> {{ invoice.buyer_email }}</div>
              <div><span class="font-medium">Telepon:</span> {{ invoice.buyer_phone }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Items -->
      <div>
        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Item Pesanan</h3>
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-800">
              <tr>
                <th
                  class="px-4 py-3 text-left text-sm font-medium text-gray-900 dark:text-gray-100"
                >
                  Produk
                </th>
                <th
                  class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-100"
                >
                  Qty
                </th>
                <th
                  class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-100"
                >
                  Harga
                </th>
                <th
                  class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-100"
                >
                  Subtotal
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="item in invoice.raw_response.data.order_items" :key="item.sku">
                <td class="px-4 py-3">
                  <div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">
                      {{ item.name }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ item.sku }}</div>
                  </div>
                </td>
                <td class="px-4 py-3 text-right text-sm text-gray-900 dark:text-gray-100">
                  {{ item.quantity }}
                </td>
                <td class="px-4 py-3 text-right text-sm text-gray-900 dark:text-gray-100">
                  {{ formatCurrency(item.price) }}
                </td>
                <td
                  class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-100"
                >
                  {{ formatCurrency(item.subtotal) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Payment Summary -->
      <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Ringkasan Pembayaran</h3>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
            <span class="font-medium text-gray-900 dark:text-gray-100">{{
              formatCurrency(invoice.raw_response.data.amount)
            }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Biaya Admin</span>
            <span class="font-medium text-gray-900 dark:text-gray-100">{{
              formatCurrency(invoice.raw_response.data.total_fee)
            }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600 dark:text-gray-400">Merchant Fee</span>
            <span class="font-medium text-gray-900 dark:text-gray-100">{{
              formatCurrency(invoice.raw_response.data.fee_merchant)
            }}</span>
          </div>
          <hr class="border-blue-200 dark:border-blue-600" />
          <div class="flex justify-between text-base">
            <span class="font-semibold text-gray-900 dark:text-gray-100">Total yang Diterima</span>
            <span class="font-bold text-blue-600 dark:text-blue-400">{{
              formatCurrency(invoice.raw_response.data.amount_received)
            }}</span>
          </div>
        </div>
      </div>

      <!-- Payment Method Info -->
      <div>
        <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Informasi Pembayaran</h3>
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span class="font-medium text-gray-900 dark:text-gray-100">Metode Pembayaran:</span>
              <div class="mt-1">{{ invoice.raw_response.data.payment_name }}</div>
            </div>
            <div>
              <span class="font-medium text-gray-900 dark:text-gray-100">Kode Pembayaran:</span>
              <div class="mt-1 font-mono">{{ invoice.raw_response.data.pay_code }}</div>
            </div>
            <div>
              <span class="font-medium text-gray-900 dark:text-gray-100">Expired Time:</span>
              <div class="mt-1">
                {{
                  formatDate(new Date(invoice.raw_response.data.expired_time * 1000).toISOString())
                }}
              </div>
            </div>
            <div>
              <span class="font-medium text-gray-900 dark:text-gray-100">Checkout URL:</span>
              <a
                :href="invoice.raw_response.data.checkout_url"
                target="_blank"
                class="mt-1 text-blue-600 dark:text-blue-400 hover:underline break-all text-xs"
              >
                {{ invoice.raw_response.data.checkout_url }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end gap-3">
        <Button label="Tutup" icon="pi pi-times" outlined @click="handleClose" />
      </div>
    </template>
  </Dialog>
</template>
