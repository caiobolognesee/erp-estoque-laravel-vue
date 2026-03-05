<script setup lang="ts">
import { onMounted, ref } from "vue";
import { api } from "../lib/api";
import type { Purchase, SaleListItem } from "../lib/types";

const loading = ref(false);
const error = ref<string | null>(null);

const purchases = ref<Purchase[]>([]);
const sales = ref<SaleListItem[]>([]);

async function load() {
  loading.value = true;
  error.value = null;

  try {
    const [p, s] = await Promise.all([
      api.get<Purchase[]>("/purchases"),
      api.get<SaleListItem[]>("/sales"),
    ]);

    purchases.value = p.data;
    sales.value = s.data;
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? e?.message ?? "Falha ao buscar historico";
  } finally {
    loading.value = false;
  }
}

onMounted(load);
</script>

<template>
  <div class="d-flex align-center justify-space-between mb-4">
    <div>
      <h2 class="text-h5 mb-1">Historico</h2>
      <div class="text-body-2 text-medium-emphasis">Veja o historico de vendas e compras</div>
    </div>

    <v-btn :loading="loading" prepend-icon="mdi-refresh" variant="tonal" @click="load">
      Recarregar
    </v-btn>
  </div>

  <v-alert v-if="error" type="error" variant="tonal" class="mb-4">
    {{ error }}
  </v-alert>

  <v-row>
    <v-col cols="12" md="6">
      <v-card>
        <v-card-title>Compras</v-card-title>
        <v-card-text>
          <div v-if="loading">Carregando...</div>
          <div v-else-if="purchases.length === 0" class="text-medium-emphasis"> Sem compras ainda.</div>

          <v-expansion-panels v-else variant="accordion">
            <v-expansion-panel v-for="p in purchases" :key="p.id">
              <v-expansion-panel-title>
                <div class="d-flex align-center justify-space-between w-100">
                  <div>
                    <strong>#{{ p.id }}</strong> — {{ p.supplier }}
                  </div>
                  <div class="text-body-2 text-medium-emphasis">
                    {{ new Date(p.created_at).toLocaleString() }}
                  </div>
                </div>
              </v-expansion-panel-title>

              <v-expansion-panel-text>
                <v-table>
                  <thead>
                    <tr>
                      <th class="text-left">ID do produto</th>
                      <th class="text-right">Quantidade</th>
                      <th class="text-right">Preço por unidade</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(i, idx) in p.items" :key="idx">
                      <td>{{ i.product_id }}</td>
                      <td class="text-right">{{ i.quantity }}</td>
                      <td class="text-right">{{ Number(i.unit_price).toFixed(2) }}</td>
                    </tr>
                  </tbody>
                </v-table>
              </v-expansion-panel-text>
            </v-expansion-panel>
          </v-expansion-panels>
        </v-card-text>
      </v-card>
    </v-col>

    <v-col cols="12" md="6">
      <v-card>
        <v-card-title>Vendas</v-card-title>
        <v-card-text>
          <div v-if="loading">Carregando...</div>
          <div v-else-if="sales.length === 0" class="text-medium-emphasis">Sem vendas ainda.</div>

          <v-expansion-panels v-else variant="accordion">
            <v-expansion-panel v-for="s in sales" :key="s.id">
              <v-expansion-panel-title>
                <div class="d-flex align-center justify-space-between w-100">
                  <div>
                    <strong>#{{ s.id }}</strong> — {{ s.customer }}
                    <v-chip class="ml-2" size="small" label>Total: {{ Number(s.total).toFixed(2) }}</v-chip>
                    <v-chip class="ml-2" size="small" label>Lucro: {{ Number(s.profit).toFixed(2) }}</v-chip>
                  </div>
                  <div class="text-body-2 text-medium-emphasis">
                    {{ new Date(s.created_at).toLocaleString() }}
                  </div>
                </div>
              </v-expansion-panel-title>

              <v-expansion-panel-text>
                <v-table>
                  <thead>
                    <tr>
                      <th class="text-left">ID do produto</th>
                      <th class="text-right">Quantidade</th>
                      <th class="text-right">Preço por unidade</th>
                      <th class="text-right">Custo da venda</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(i, idx) in s.items" :key="idx">
                      <td>{{ i.product_id }}</td>
                      <td class="text-right">{{ i.quantity }}</td>
                      <td class="text-right">{{ Number(i.unit_price).toFixed(2) }}</td>
                      <td class="text-right">{{ Number(i.cost_at_sale).toFixed(2) }}</td>
                    </tr>
                  </tbody>
                </v-table>
              </v-expansion-panel-text>
            </v-expansion-panel>
          </v-expansion-panels>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>