<script setup lang="ts">
import { ref, computed } from 'vue'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'

interface DeleteConfirmModalProps {
  visible: boolean
  productName: string
  productId: number | null
}

interface DeleteConfirmModalEmits {
  (e: 'update:visible', value: boolean): void
  (e: 'confirm', productId: number): void
  (e: 'cancel'): void
}

const props = defineProps<DeleteConfirmModalProps>()
const emit = defineEmits<DeleteConfirmModalEmits>()

// State
const isDeleting = ref(false)

// Computed
const isVisible = computed({
  get: () => props.visible,
  set: (value: boolean) => emit('update:visible', value),
})

// Methods
const handleCancel = (closeCallback?: () => void) => {
  if (isDeleting.value) return

  if (closeCallback) {
    closeCallback()
  } else {
    isVisible.value = false
  }
  emit('cancel')
}

const handleConfirm = async () => {
  if (isDeleting.value || !props.productId) return

  isDeleting.value = true

  try {
    emit('confirm', props.productId)
  } catch (error) {
    console.error('Error in delete confirmation:', error)
  } finally {
    // Note: isDeleting akan di-reset oleh parent component
    // setelah operasi delete selesai
  }
}

// Reset deleting state when modal is closed
const resetState = () => {
  isDeleting.value = false
}

// Expose reset method for parent component
defineExpose({
  resetState,
})
</script>

<template>
  <Dialog
    v-model:visible="isVisible"
    modal
    :closable="false"
    :style="{ width: '450px' }"
    class="delete-confirm-modal"
  >
    <template #container="{ closeCallback }">
      <div
        class="p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700"
      >
        <!-- Header with danger icon -->
        <div class="flex items-center gap-4 mb-6">
          <div
            class="flex-shrink-0 w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center"
          >
            <i class="pi pi-exclamation-triangle text-red-600 dark:text-red-400 text-xl" />
          </div>
          <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">
              Konfirmasi Hapus Produk
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Tindakan ini tidak dapat dibatalkan
            </p>
          </div>
        </div>

        <!-- Content -->
        <div class="mb-6">
          <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
            Apakah Anda yakin ingin menghapus produk
            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ productName }}</span
            >?
            <br />
            <span class="text-sm text-gray-500 dark:text-gray-400">
              Data produk akan dihapus secara permanen dari sistem.
            </span>
          </p>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3">
          <Button
            label="Batal"
            icon="pi pi-times"
            outlined
            severity="secondary"
            size="small"
            @click="handleCancel(closeCallback)"
            :disabled="isDeleting"
            class="min-w-20 h-9 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700"
          />
          <Button
            label="Hapus"
            icon="pi pi-trash"
            severity="danger"
            size="small"
            :loading="isDeleting"
            @click="handleConfirm"
            class="min-w-20 h-9 bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-800 border-red-600 dark:border-red-700"
          />
        </div>
      </div>
    </template>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'

interface DeleteConfirmModalProps {
  visible: boolean
  productName: string
  productId: number | null
}

interface DeleteConfirmModalEmits {
  (e: 'update:visible', value: boolean): void
  (e: 'confirm', productId: number): void
  (e: 'cancel'): void
}

const props = defineProps<DeleteConfirmModalProps>()
const emit = defineEmits<DeleteConfirmModalEmits>()

// State
const isDeleting = ref(false)

// Computed
const isVisible = computed({
  get: () => props.visible,
  set: (value: boolean) => emit('update:visible', value),
})

// Methods
const handleCancel = (closeCallback?: () => void) => {
  if (isDeleting.value) return

  if (closeCallback) {
    closeCallback()
  } else {
    isVisible.value = false
  }
  emit('cancel')
}

const handleConfirm = async () => {
  if (isDeleting.value || !props.productId) return

  isDeleting.value = true

  try {
    emit('confirm', props.productId)
  } catch (error) {
    console.error('Error in delete confirmation:', error)
  } finally {
    // Note: isDeleting akan di-reset oleh parent component
    // setelah operasi delete selesai
  }
}

// Reset deleting state when modal is closed
const resetState = () => {
  isDeleting.value = false
}

// Expose reset method for parent component
defineExpose({
  resetState,
})
</script>

<style scoped>
.delete-confirm-modal :deep(.p-dialog) {
  background: white;
  border: 1px solid #e5e7eb;
}

/* Dark mode styles */
:root.dark .delete-confirm-modal :deep(.p-dialog) {
  background: #1f2937;
  border: 1px solid #374151;
}

/* Button hover states for better UX */
.delete-confirm-modal :deep(.p-button-outlined:hover) {
  background-color: #f9fafb;
  border-color: #6b7280;
}

:root.dark .delete-confirm-modal :deep(.p-button-outlined:hover) {
  background-color: #374151;
  border-color: #6b7280;
}

.delete-confirm-modal :deep(.p-button-danger:hover) {
  background-color: #dc2626;
  border-color: #dc2626;
}

:root.dark .delete-confirm-modal :deep(.p-button-danger:hover) {
  background-color: #b91c1c;
  border-color: #b91c1c;
}
</style>
