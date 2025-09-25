<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import Menu from 'primevue/menu'

const router = useRouter()

const menuItems = ref([
  {
    label: 'Home',
    icon: 'pi pi-home',
    command: () => router.push('/'),
  },
  {
    label: 'Produk',
    icon: 'pi pi-box',
    command: () => router.push('/products'),
  },
  {
    label: 'Invoice',
    icon: 'pi pi-file',
    command: () => router.push('/invoices'),
  },
])
</script>

<template>
  <div id="app" class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-blue-600 dark:bg-neutral-900 shadow-lg">
      <div class="p-4">
        <h1 class="text-xl text-center font-bold text-neutral-200 mb-6">Tripay E-Commerce</h1>
        <Menu :model="menuItems" class="w-full" />
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 bg-white dark:bg-gray-800 py-16 px-6">
      <RouterView />
    </div>
  </div>
</template>

<style scoped></style>
