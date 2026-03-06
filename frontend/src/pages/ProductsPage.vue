<script setup lang="ts">
import { onMounted, ref } from "vue";
import { storeToRefs } from "pinia";
import { formatBRL } from "../utils/currency";
import { useProductsStore } from "../stores/products";

const productsStore = useProductsStore();
const { items: products, loading, error } = storeToRefs(productsStore);

const form = ref({ name: "", sale_price: 0 });

async function handleCreateProduct() {
  try {
    await productsStore.createProduct({
      name: form.value.name,
      sale_price: Number(form.value.sale_price),
    });

    form.value = { name: "", sale_price: 0 };
  } catch {
    // error already handled in store
  }
}

onMounted(() => {
  productsStore.fetchProducts();
});
</script>

<template>
  <div class="d-flex align-center justify-space-between mb-4">
    <div>
      <h2 class="text-h5 mb-1">Produtos</h2>
      <div class="text-body-2 text-medium-emphasis">Crie e veja o estoque de produtos e seus valores</div>
    </div>
    <v-btn :loading="loading" prepend-icon="mdi-refresh" variant="tonal" @click="productsStore.fetchProducts">
      Recarregar
    </v-btn>
  </div>

  <v-alert v-if="error" type="error" variant="tonal" class="mb-4">
    {{ error }}
  </v-alert>

  <v-card class="mb-6">
    <v-card-title>Criar produto</v-card-title>
    <v-card-text>
      <v-form @submit.prevent="handleCreateProduct">
        <v-row>
          <v-col cols="12" md="7">
            <v-text-field v-model="form.name" label="Nome" placeholder="Nome do produto" />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field
              v-model.number="form.sale_price"
              label="Valor de venda"
              type="number"
              min="0"
              step="0.01"
            />
          </v-col>
          <v-col cols="12" md="2" class="d-flex align-center">
            <v-btn color="primary" :loading="loading" block type="submit" prepend-icon="mdi-content-save" :disabled="!form.name && !form.sale_price">
              Salvar
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
  </v-card>

  <v-card>
    <v-card-title>Lista de produtos</v-card-title>
    <v-card-text>
      <v-table>
        <thead>
          <tr>
            <th class="text-center">ID</th>
            <th class="text-center">Nome</th>
            <th class="text-center">Preço de custo</th>
            <th class="text-center">Preço de venda</th>
            <th class="text-center">Quantidade em estoque</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in products" :key="p.id">
            <td class="text-center">{{ p.id }}</td>
            <td class="text-center">{{ p.name }}</td>
            <td class="text-center">{{ formatBRL(p.avg_cost) }}</td>
            <td class="text-center">{{ formatBRL(p.sale_price) }}</td>
            <td class="text-center">{{ p.stock }}</td>
          </tr>
        </tbody>
      </v-table>
    </v-card-text>
  </v-card>
</template>