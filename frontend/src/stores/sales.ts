import { defineStore } from "pinia";
import { api } from "../lib/api";
import type { SalePayload, SaleResponse } from "../lib/types";
import { useProductsStore } from "./products";

type SalesState = {
  loading: boolean;
  error: string | null;
  success: string | null;
  lastSale: SaleResponse | null;
};

export const useSalesStore = defineStore("sales", {
  state: (): SalesState => ({
    loading: false,
    error: null,
    success: null,
    lastSale: null,
  }),

  actions: {
    clearFeedback() {
      this.error = null;
      this.success = null;
    },

    clearLastSale() {
      this.lastSale = null;
    },

    async submitSale(payload: SalePayload) {
      this.loading = true;
      this.error = null;
      this.success = null;
      this.lastSale = null;

      try {
        const { data } = await api.post<SaleResponse>("/sales", payload);

        this.lastSale = data;
        this.success = "Venda registrada com sucesso.";

        const productsStore = useProductsStore();
        await productsStore.fetchProducts();
      } catch (e: any) {
        this.error =
          e?.response?.data?.message ??
          JSON.stringify(e?.response?.data?.errors ?? null) ??
          e?.message ??
          "Falha ao registrar venda.";

        throw e;
      } finally {
        this.loading = false;
      }
    },
  },
});