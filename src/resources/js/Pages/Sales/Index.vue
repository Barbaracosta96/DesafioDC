<template>
  <AppLayout page-title="Vendas">
    <Head title="Vendas" />

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Acompanhamento de Vendas</h2>
        <p class="text-sm text-gray-400 mt-0.5">{{ sales.total }} venda(s) registrada(s)</p>
      </div>
      <Link
        v-if="$page.props.auth.user?.permissions?.includes('create sales')"
        :href="route('sales.create')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-xl transition shadow-sm"
      >
        <PlusIcon class="w-4 h-4" />
        Nova Venda
      </Link>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-50 mb-4">
      <form @submit.prevent="applyFilters" class="flex flex-col sm:flex-row gap-3">
        <div class="flex items-center gap-2 flex-1 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
          <MagnifyingGlassIcon class="w-4 h-4 text-gray-400 flex-shrink-0" />
          <input
            v-model="filterForm.search"
            type="text"
            placeholder="Buscar por cliente..."
            class="flex-1 bg-transparent text-sm outline-none text-gray-700"
          />
        </div>
        <select
          v-model="filterForm.status"
          class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 outline-none"
        >
          <option value="">Todos os status</option>
          <option value="pending">Pendente</option>
          <option value="completed">Concluída</option>
          <option value="cancelled">Cancelada</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-xl transition">
          Filtrar
        </button>
      </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 text-gray-400 text-xs font-semibold uppercase tracking-wide">
              <th class="text-left px-5 py-3.5">ID</th>
              <th class="text-left px-5 py-3.5">Cliente</th>
              <th class="text-left px-5 py-3.5">Vendedor</th>
              <th class="text-right px-5 py-3.5">Valor</th>
              <th class="text-center px-5 py-3.5">Status</th>
              <th class="text-left px-5 py-3.5">Data</th>
              <th class="text-right px-5 py-3.5">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="sale in sales.data"
              :key="sale.id"
              class="hover:bg-gray-50 transition"
            >
              <td class="px-5 py-3.5 text-gray-400 text-xs font-mono">#{{ String(sale.id).padStart(4,'0') }}</td>
              <td class="px-5 py-3.5 font-medium text-gray-700">
                {{ sale.customer?.name || 'Consumidor Final' }}
              </td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">{{ sale.user?.name }}</td>
              <td class="px-5 py-3.5 text-right font-semibold text-gray-800">{{ formatCurrency(sale.total) }}</td>
              <td class="px-5 py-3.5 text-center">
                <span
                  class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                  :class="{
                    'bg-green-100 text-green-700': sale.status === 'completed',
                    'bg-yellow-100 text-yellow-700': sale.status === 'pending',
                    'bg-red-100 text-red-700': sale.status === 'cancelled',
                  }"
                >
                  {{ statusLabel[sale.status] }}
                </span>
              </td>
              <td class="px-5 py-3.5 text-gray-400 text-xs">{{ formatDate(sale.created_at) }}</td>
              <td class="px-5 py-3.5">
                <div class="flex items-center justify-end">
                  <Link
                    :href="route('sales.show', sale.id)"
                    class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </Link>
                </div>
              </td>
            </tr>
            <tr v-if="!sales.data.length">
              <td colspan="7" class="px-5 py-12 text-center">
                <div class="flex flex-col items-center gap-2">
                  <ShoppingCartIcon class="w-10 h-10 text-gray-300" />
                  <p class="text-gray-400 text-sm">Nenhuma venda encontrada</p>
                  <Link :href="route('sales.create')" class="text-sm text-violet-600 font-medium hover:underline">
                    Registrar venda
                  </Link>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="sales.last_page > 1" class="flex items-center justify-between px-5 py-3 border-t border-gray-50">
        <p class="text-xs text-gray-400">
          Mostrando {{ sales.from }}–{{ sales.to }} de {{ sales.total }} resultados
        </p>
        <div class="flex gap-1">
          <Link
            v-for="page in sales.links"
            :key="page.label"
            :href="page.url || '#'"
            class="px-3 py-1.5 text-xs rounded-lg transition"
            :class="page.active ? 'bg-violet-600 text-white font-semibold' : page.url ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-gray-50 text-gray-300 cursor-not-allowed'"
            v-html="page.label"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Pages/Layouts/AppLayout.vue';
import { PlusIcon, MagnifyingGlassIcon, EyeIcon, ShoppingCartIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  sales:   { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
});

const statusLabel = { completed: 'Concluída', pending: 'Pendente', cancelled: 'Cancelada' };

const filterForm = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
});

function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v || 0);
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' });
}

function applyFilters() {
  router.get(route('sales.index'), filterForm, { preserveState: true, replace: true });
}
</script>
