import { defineStore } from "pinia";
import { api } from "../lib/api";
import type { Purchase, SaleListItem } from "../lib/types";

type HistoryState = {
  purchases: Purchase[];
  sales: SaleListItem[];
  loading: boolean;
  error: string | null;
};

export const useHistoryStore = defineStore("history", {
  state: (): HistoryState => ({
    purchases: [],
    sales: [],
    loading: false,
    error: null,
  }),

  actions: {
    clearError() {
      this.error = null;
    },

    async loadHistory() {
      this.loading = true;
      this.error = null;

      try {
        const [p, s] = await Promise.all([
          api.get<Purchase[]>("/purchases"),
          api.get<SaleListItem[]>("/sales"),
        ]);

        this.purchases = p.data;
        this.sales = s.data;
      } catch (e: any) {
        this.error =
          e?.response?.data?.message ??
          e?.message ??
          "Falha ao buscar historico";
      } finally {
        this.loading = false;
      }
    },
  },
});