<script setup lang="ts">
import { onMounted, ref } from "vue";
import { api } from "../lib/api";
import type { Product } from "../lib/types";
import { formatBRL } from "../utils/currency";

const loading = ref(false);
const error = ref<string | null>(null);

const products = ref<Product[]>([]);
const form = ref({ name: "", sale_price: 0 });

async function fetchProducts() {
  loading.value = true;
  error.value = null;
  try {
    const { data } = await api.get<Product[]>("/products");
    products.value = data;
  } catch (e: any) {
    error.value = e?.message ?? "Falha ao buscar produtos.";
  } finally {
    loading.value = false;
  }
}

async function createProduct() {
  error.value = null;
  try {
    await api.post("/products", {
      name: form.value.name,
      sale_price: Number(form.value.sale_price),
    });
    form.value = { name: "", sale_price: 0 };
    await fetchProducts();
  } catch (e: any) {
    error.value =
      e?.response?.data?.message ??
      JSON.stringify(e?.response?.data?.errors ?? null) ??
      e?.message ??
      "Falha ao criar o produto.";
  }
}

onMounted(fetchProducts);
</script>

<template>
  <div class="d-flex align-center justify-space-between mb-4">
    <div>
      <h2 class="text-h5 mb-1">Produtos</h2>
      <div class="text-body-2 text-medium-emphasis">Crie e veja o estoque de produtos e seus valores</div>
    </div>
    <v-btn :loading="loading" prepend-icon="mdi-refresh" variant="tonal" @click="fetchProducts">
      Recarregar
    </v-btn>
  </div>

  <v-alert v-if="error" type="error" variant="tonal" class="mb-4">
    {{ error }}
  </v-alert>

  <v-card class="mb-6">
    <v-card-title>Criar produto</v-card-title>
    <v-card-text>
      <v-form @submit.prevent="createProduct">
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
            <v-btn color="primary" block type="submit" prepend-icon="mdi-content-save">
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
            <th class="text-left">ID</th>
            <th class="text-left">Nome</th>
            <th class="text-right">Preço de custo</th>
            <th class="text-right">Preço de venda</th>
            <th class="text-right">Quantidade em estoque</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in products" :key="p.id">
            <td>{{ p.id }}</td>
            <td>{{ p.name }}</td>
            <td class="text-right">{{ formatBRL(p.avg_cost) }}</td>
            <td class="text-right">{{ formatBRL(p.sale_price) }}</td>
            <td class="text-right">{{ p.stock }}</td>
          </tr>
        </tbody>
      </v-table>
    </v-card-text>
  </v-card>
</template>