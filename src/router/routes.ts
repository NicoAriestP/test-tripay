import type { RouteRecordRaw } from 'vue-router'

const HomePage = () => import('@/pages/HomePage.vue')
const InvoiceList = () => import('@/pages/InvoiceList.vue')
const ProductList = () => import('@/pages/ProductList.vue')

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
    name: 'ProductList',
    component: ProductList,
    meta: {
      title: 'Products',
      description: 'List of products',
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
