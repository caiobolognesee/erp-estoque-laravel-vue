import { defineStore } from "pinia";
import { api } from "../lib/api";
import type { Product } from "../lib/types";

type CreateProductPayload = {
  name: string;
  sale_price: number;
};

type ProductsState = {
  items: Product[];
  loading: boolean;
  error: string | null;
};

export const useProductsStore = defineStore("products", {
  state: (): ProductsState => ({
    items: [],
    loading: false,
    error: null,
  }),

  actions: {
    clearError() {
      this.error = null;
    },

    async fetchProducts() {
      this.loading = true;
      this.error = null;

      try {
        const { data } = await api.get<Product[]>("/products");
        this.items = data;
      } catch (e: any) {
        this.error = e?.message ?? "Falha ao buscar produtos.";
      } finally {
        this.loading = false;
      }
    },

    async createProduct(payload: CreateProductPayload) {
      this.error = null;

      try {
        await api.post("/products", payload);
        await this.fetchProducts();
      } catch (e: any) {
        this.error =
          e?.response?.data?.message ??
          JSON.stringify(e?.response?.data?.errors ?? null) ??
          e?.message ??
          "Falha ao criar o produto.";

        throw e;
      }
    },
  },
});