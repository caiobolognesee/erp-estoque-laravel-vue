<script setup lang="ts">
import { onMounted, ref, computed } from "vue";
import { storeToRefs } from "pinia";
import type { SalePayload } from "../lib/types";
import { formatBRL } from "../utils/currency";
import { useProductsStore } from "../stores/products";
import { useSalesStore } from "../stores/sales";

const productsStore = useProductsStore();
const salesStore = useSalesStore();

const { items: products } = storeToRefs(productsStore);
const { loading, error, success, lastSale } = storeToRefs(salesStore);

const customer = ref("");
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

const estimatedTotal = computed(() =>
  items.value.reduce((acc, i) => acc + Number(i.quantity) * Number(i.unit_price), 0)
);

const canSubmit = computed(() => {
  if (!customer.value.trim()) return false;
  if (items.value.length === 0) return false;

  return items.value.every(
    (i) =>
      i.product_id !== null &&
      Number(i.quantity) >= 1 &&
      Number.isFinite(Number(i.unit_price)) &&
      Number(i.unit_price) > 0
  );
});

async function handleSubmitSale() {
  const payload: SalePayload = {
    customer: customer.value.trim(),
    items: items.value.map((i) => ({
      product_id: Number(i.product_id),
      quantity: Number(i.quantity),
      unit_price: Number(i.unit_price),
    })),
  };

  try {
    await salesStore.submitSale(payload);

    customer.value = "";
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
      <h2 class="text-h5 mb-1">Vendas</h2>
      <div class="text-body-2 text-medium-emphasis">Registre as vendas, valide o estoque e calcule o lucro</div>
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

  <v-card class="mb-6">
    <v-card-title>Nova venda</v-card-title>
    <v-card-text>
      <v-form @submit.prevent="handleSubmitSale">
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field
              v-model="customer"
              label="Cliente"
              placeholder="Nome do cliente"
              prepend-inner-icon="mdi-account"
            />
          </v-col>

          <v-col cols="12" md="6" class="d-flex justify-end align-center">
            <v-chip class="mr-3" label>
              Total estimado: <strong class="ml-2">{{ formatBRL(estimatedTotal) }}</strong>
            </v-chip>

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
              Enviar venda
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
              placeholder="Selecionar produto"
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

  <v-card v-if="lastSale">
    <v-card-title>Última venda</v-card-title>
    <v-card-text>
      <v-row>
        <v-col cols="12" md="4">
          <v-list density="compact">
            <v-list-item title="ID da venda" :subtitle="String(lastSale.id)" prepend-icon="mdi-identifier" />
            <v-list-item title="Total" :subtitle="formatBRL(lastSale.total)" prepend-icon="mdi-cash" />
            <v-list-item title="Lucro" :subtitle="formatBRL(lastSale.profit)" prepend-icon="mdi-chart-line" />
          </v-list>
        </v-col>

        <v-col cols="12" md="8">
          <v-table>
            <thead>
              <tr>
                <th class="text-center">ID do produto</th>
                <th class="text-center">Quantidade</th>
                <th class="text-center">Valor por unidade vendida</th>
                <th class="text-center">Preço de custo</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(i, idx) in lastSale.items" :key="idx">
                <td class="text-center">{{ i.product_id }}</td>
                <td class="text-center">{{ i.quantity }}</td>
                <td class="text-center">{{ formatBRL(i.unit_price) }}</td>
                <td class="text-center">{{ formatBRL(i.cost_at_sale) }}</td>
              </tr>
            </tbody>
          </v-table>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>