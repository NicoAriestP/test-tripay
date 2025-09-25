import type { RouteRecordRaw } from 'vue-router'

const HomePage = () => import('@/pages/HomePage.vue')
const InvoiceList = () => import('@/pages/InvoiceList.vue')
const ProductManagement = () => import('@/pages/ProductManagement.vue')

/**
 * Application routes configuration
 * Add new routes here when creating new pages in src/pages/
 */
export const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    component: HomePage,
    meta: {
      title: 'Home',
      description: 'Home page of the application',
    },
  },
  {
    path: '/invoices',
    name: 'InvoiceList',
    component: InvoiceList,
    meta: {
      title: 'Invoices',
      description: 'List of invoices',
    },
  },
  {
    path: '/products',
    name: 'ProductManagement',
    component: ProductManagement,
    meta: {
      title: 'Produk',
      description: 'Halaman manajemen produk',
    },
  },
  // Catch-all route for 404 pages
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    redirect: '/',
    meta: {
      title: 'Page Not Found',
    },
  },
]
