import { defineStore } from "pinia";
import { api } from "../lib/api";
import type { PurchasePayload } from "../lib/types";
import { useProductsStore } from "./products";

type PurchasesState = {
  loading: boolean;
  error: string | null;
  success: string | null;
};

export const usePurchasesStore = defineStore("purchases", {
  state: (): PurchasesState => ({
    loading: false,
    error: null,
    success: null,
  }),

  actions: {
    clearFeedback() {
      this.error = null;
      this.success = null;
    },

    async submitPurchase(payload: PurchasePayload) {
      this.loading = true;
      this.error = null;
      this.success = null;

      try {
        await api.post("/purchases", payload);
        this.success = "Compra registrada com sucesso.";

        const productsStore = useProductsStore();
        await productsStore.fetchProducts();
      } catch (e: any) {
        this.error =
          e?.response?.data?.message ??
          JSON.stringify(e?.response?.data?.errors ?? null) ??
          e?.message ??
          "Falha ao registrar compra.";

        throw e;
      } finally {
        this.loading = false;
      }
    },
  },
});