<script setup lang="ts">
import { onMounted, ref, computed } from "vue";
import { storeToRefs } from "pinia";
import type { PurchasePayload } from "../lib/types";
import { useProductsStore } from "../stores/products";
import { usePurchasesStore } from "../stores/purchases";

const productsStore = useProductsStore();
const purchasesStore = usePurchasesStore();

const { items: products } = storeToRefs(productsStore);
const { loading, error, success } = storeToRefs(purchasesStore);

const supplier = ref("");
const items = ref<Array<{ product_id: number | null; quantity: number; unit_price: number }>>([
  { product_id: null, quantity: 1, unit_price: 0 },
]);

const productOptions = computed(() =>
  products.value.map((p) => ({
    title: `${p.name} (estoque: ${p.stock}, preço de custo: ${Number(p.avg_cost).toFixed(2)})`,
    value: p.id,
  }))
);

function addRow() {
  items.value.push({ product_id: null, quantity: 1, unit_price: 0 });
}

function removeRow(index: number) {
  items.value.splice(index, 1);
  if (items.value.length === 0) addRow();
}

const canSubmit = computed(() => {
  if (!supplier.value.trim()) return false;
  if (items.value.length === 0) return false;

  return items.value.every(
    (i) =>
      i.product_id !== null &&
      Number(i.quantity) >= 1 &&
      Number.isFinite(Number(i.unit_price)) &&
      Number(i.unit_price) > 0
  );
});

async function handleSubmitPurchase() {
  const payload: PurchasePayload = {
    supplier: supplier.value.trim(),
    items: items.value.map((i) => ({
      product_id: Number(i.product_id),
      quantity: Number(i.quantity),
      unit_price: Number(i.unit_price),
    })),
  };

  try {
    await purchasesStore.submitPurchase(payload);

    supplier.value = "";
    items.value = [{ product_id: null, quantity: 1, unit_price: 0 }];
  } catch {
    // error already handled in store
  }
}

onMounted(async () => {
  await productsStore.fetchProducts();
});
</script>

<template>
  <div class="d-flex align-center justify-space-between mb-4">
    <div>
      <h2 class="text-h5 mb-1">Compras</h2>
      <div class="text-body-2 text-medium-emphasis">Registre as entradas do estoque e atualize os valores</div>
    </div>

    <v-btn :loading="loading" prepend-icon="mdi-refresh" variant="tonal" @click="productsStore.fetchProducts">
      Recarregar produtos
    </v-btn>
  </div>

  <v-alert v-if="error" type="error" variant="tonal" class="mb-4">
    {{ error }}
  </v-alert>

  <v-alert v-if="success" type="success" variant="tonal" class="mb-4">
    {{ success }}
  </v-alert>

  <v-card>
    <v-card-title>Nova Compra</v-card-title>
    <v-card-text>
      <v-form @submit.prevent="handleSubmitPurchase">
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field
              v-model="supplier"
              label="Fornecedor"
              placeholder="Nome do fornecedor"
              prepend-inner-icon="mdi-truck"
            />
          </v-col>

          <v-col cols="12" md="6" class="d-flex justify-end align-center">
            <v-btn variant="tonal" prepend-icon="mdi-plus" class="mr-2" @click="addRow">
              Adicionar item
            </v-btn>

            <v-btn
              color="primary"
              type="submit"
              prepend-icon="mdi-check"
              :loading="loading"
              :disabled="!canSubmit"
            >
              Comprar
            </v-btn>
          </v-col>
        </v-row>

        <v-divider class="my-4" />

        <div class="text-subtitle-1 mb-2">Itens</div>

        <v-row v-for="(row, idx) in items" :key="idx" class="mb-2" align="end">
          <v-col cols="12" md="6">
            <v-select
              v-model="row.product_id"
              :items="productOptions"
              label="Produto"
              placeholder="Selecionar o produto"
              prepend-inner-icon="mdi-package-variant"
            />
          </v-col>

          <v-col cols="6" md="2">
            <v-text-field
              v-model.number="row.quantity"
              label="Quantidade"
              type="number"
              min="1"
              step="1"
              prepend-inner-icon="mdi-counter"
            />
          </v-col>

          <v-col cols="6" md="3">
            <v-text-field
              v-model.number="row.unit_price"
              label="Preço por unidade"
              type="number"
              min="0"
              step="0.01"
              prepend-inner-icon="mdi-currency-usd"
            />
          </v-col>

          <v-col cols="12" md="1" class="d-flex justify-end">
            <v-btn icon="mdi-delete" variant="text" color="error" @click="removeRow(idx)" />
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
  </v-card>
</template>