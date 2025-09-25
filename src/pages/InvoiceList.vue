<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useToast } from 'primevue/usetoast'
import { useInvoices } from '@/apis'
import type { Invoice } from '@/types/InvoiceType'

import Card from 'primevue/card'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import InvoiceDetailDialog from '@/components/invoice/InvoiceDetailDialog.vue'

const toast = useToast()
const { listInvoices, formatCurrency, formatDate } = useInvoices()

// State
const loading = ref(false)
const invoices = ref<Invoice[]>([])
const searchQuery = ref('')
const showDetailDialog = ref(false)
const selectedInvoice = ref<Invoice | null>(null)

// Methods
const loadInvoices = async () => {
  loading.value = true
  try {
    const response = await listInvoices({
      search: searchQuery.value,
    })

    console.log('API Response:', response)

    // API langsung mengembalikan array invoice, bukan objek dengan success/data
    if (Array.isArray(response)) {
      invoices.value = response
    } else if (response && response.data && Array.isArray(response.data)) {
      invoices.value = response.data
    } else {
      throw new Error('Format response tidak valid')
    }
  } catch (error) {
    console.error('Error loading invoices:', error)
    const message =
      (error as { response?: { data?: { message?: string } } })?.response?.data?.message ||
      'Terjadi kesalahan saat memuat data invoice'
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: message,
    })
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  // Debounce search
  setTimeout(() => {
    loadInvoices()
  }, 500)
}

const viewInvoiceDetails = (invoice: Invoice) => {
  selectedInvoice.value = invoice
  showDetailDialog.value = true
}

const handleCloseDialog = () => {
  showDetailDialog.value = false
  selectedInvoice.value = null
}

const openPaymentLink = (invoice: Invoice) => {
  const checkoutUrl = invoice.raw_response.data.checkout_url
  if (checkoutUrl) {
    window.open(checkoutUrl, '_blank')
  } else {
    toast.add({
      severity: 'warn',
      summary: 'Peringatan',
      detail: 'Link pembayaran tidak tersedia',
    })
  }
}

// Lifecycle
onMounted(() => {
  loadInvoices()
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Daftar Invoice</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
          Kelola dan pantau semua transaksi invoice Anda
        </p>
      </div>

      <!-- Filters and Search -->
      <Card class="mb-6">
        <template #content>
          <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <!-- Search Input -->
            <div class="flex-1">
              <div class="relative">
                <InputText
                  v-model="searchQuery"
                  placeholder="Cari berdasarkan reference, email, atau nama produk..."
                  class="w-full pl-10"
                  @input="handleSearch"
                />
              </div>
            </div>

            <!-- Refresh Button -->
            <Button
              icon="pi pi-refresh"
              :loading="loading"
              @click="loadInvoices"
              outlined
              class="p-2"
            />
          </div>
        </template>
      </Card>

      <!-- Invoice Table -->
      <Card>
        <template #content>
          <DataTable
            :value="invoices"
            :loading="loading"
            paginator
            :rows="10"
            :rowsPerPageOptions="[10, 25, 50]"
            responsiveLayout="scroll"
            :globalFilterFields="['tripay_reference', 'buyer_email', 'product.name']"
            class="p-datatable-gridlines"
          >
            <template #empty>
              <div class="text-center py-8">
                <i class="pi pi-inbox text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400">Tidak ada invoice ditemukan</p>
              </div>
            </template>

            <template #loading>
              <div class="text-center py-8">
                <i class="pi pi-spinner pi-spin text-2xl text-blue-600 dark:text-blue-400 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400">Memuat data invoice...</p>
              </div>
            </template>

            <!-- Invoice ID Column -->
            <Column field="id" header="ID" :sortable="true" class="min-w-[80px]">
              <template #body="{ data }">
                <span class="font-mono text-sm">#{{ data.id }}</span>
              </template>
            </Column>

            <!-- TriPay Reference Column -->
            <Column
              field="tripay_reference"
              header="Reference"
              :sortable="true"
              class="min-w-[180px]"
            >
              <template #body="{ data }">
                <div class="font-mono text-sm text-blue-600 dark:text-blue-400">
                  {{ data.tripay_reference }}
                </div>
              </template>
            </Column>

            <!-- Product Column -->
            <Column field="product.name" header="Produk" :sortable="true" class="min-w-[200px]">
              <template #body="{ data }">
                <div>
                  <div class="font-medium text-gray-900 dark:text-gray-100">
                    {{ data.product.name }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    SKU: {{ data.product.sku }}
                  </div>
                </div>
              </template>
            </Column>

            <!-- Customer Column -->
            <Column header="Customer" class="min-w-[220px]">
              <template #body="{ data }">
                <div>
                  <div class="font-medium text-gray-900 dark:text-gray-100">
                    {{ data.raw_response.data.customer_name }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ data.buyer_email }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ data.buyer_phone }}
                  </div>
                </div>
              </template>
            </Column>

            <!-- Amount Column -->
            <Column
              field="raw_response.data.amount"
              header="Amount"
              :sortable="true"
              class="min-w-[120px]"
            >
              <template #body="{ data }">
                <div class="text-right">
                  <div class="font-semibold text-green-600 dark:text-green-400">
                    {{ formatCurrency(data.raw_response.data.amount) }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    Fee: {{ formatCurrency(data.raw_response.data.total_fee) }}
                  </div>
                </div>
              </template>
            </Column>

            <!-- Payment Method Column -->
            <Column
              field="raw_response.data.payment_method"
              header="Payment"
              :sortable="true"
              class="min-w-[140px]"
            >
              <template #body="{ data }">
                <div>
                  <div class="font-medium text-gray-900 dark:text-gray-100">
                    {{ data.raw_response.data.payment_name }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ data.raw_response.data.payment_method }}
                  </div>
                </div>
              </template>
            </Column>

            <!-- Date Column -->
            <Column field="created_at" header="Tanggal" :sortable="true" class="min-w-[140px]">
              <template #body="{ data }">
                <div class="text-sm text-gray-900 dark:text-gray-100">
                  {{ formatDate(data.created_at) }}
                </div>
              </template>
            </Column>

            <!-- Actions Column -->
            <Column header="Aksi" class="min-w-[100px]">
              <template #body="{ data }">
                <div class="flex gap-2">
                  <!-- View Invoice Button -->
                  <Button
                    icon="pi pi-eye"
                    size="small"
                    outlined
                    v-tooltip.top="'Lihat Detail'"
                    @click="viewInvoiceDetails(data)"
                    class="p-2"
                  />

                  <!-- Payment Link Button -->
                  <Button
                    icon="pi pi-external-link"
                    size="small"
                    severity="info"
                    outlined
                    v-tooltip.top="'Buka Link Pembayaran'"
                    @click="openPaymentLink(data)"
                    class="p-2"
                  />
                </div>
              </template>
            </Column>
          </DataTable>
        </template>
      </Card>
    </div>

    <!-- Invoice Detail Dialog -->
    <InvoiceDetailDialog
      v-model:visible="showDetailDialog"
      :invoice="selectedInvoice"
      @close="handleCloseDialog"
    />
  </div>
</template>

<style scoped>
:deep(.p-datatable .p-datatable-tbody > tr > td) {
  padding: 1rem 0.75rem;
}

:deep(.p-tag) {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
}
</style>
