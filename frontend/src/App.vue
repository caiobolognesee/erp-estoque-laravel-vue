<script setup lang="ts">
const links = [
  { title: "Produtos", to: "/products", icon: "mdi-package-variant" },
  { title: "Compras", to: "/purchases", icon: "mdi-truck-fast" },
  { title: "Vendas", to: "/sales", icon: "mdi-cash-register" },
  { title: "Historico", to: "/history", icon: "mdi-history" },
];
</script>

<template>
  <v-app>
    <v-app-bar title="Stock ERP" density="comfortable" />

    <v-navigation-drawer permanent width="260">
      <v-list nav>
        <v-list-item
          v-for="l in links"
          :key="l.to"
          :to="l.to"
          :prepend-icon="l.icon"
          :title="l.title"
        />
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <v-container class="py-6">
        <RouterView />
      </v-container>
    </v-main>
  </v-app>
</template>