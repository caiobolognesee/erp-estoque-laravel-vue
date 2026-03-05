import { createRouter, createWebHistory } from "vue-router";
import ProductsPage from "./pages/ProductsPage.vue";
import PurchasesPage from "./pages/PurchasesPage.vue";
import SalesPage from "./pages/SalesPage.vue";
import HistoryPage from "./pages/HistoryPage.vue";

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", redirect: "/products" },
    { path: "/products", component: ProductsPage },
    { path: "/purchases", component: PurchasesPage },
    { path: "/sales", component: SalesPage },
    { path: "/history", component: HistoryPage },
  ],
});