<template>
  <AppLayout page-title="Perfil do Cliente">
    <Head :title="customer.name" />

    <div class="max-w-4xl mx-auto">
      <div class="flex items-center gap-3 mb-6">
        <Link :href="route('customers.index')" class="p-2 rounded-xl hover:bg-gray-100 text-gray-400 transition">
          <ArrowLeftIcon class="w-4 h-4" />
        </Link>
        <div class="flex-1">
          <h2 class="text-xl font-bold text-gray-800">{{ customer.name }}</h2>
          <p class="text-sm text-gray-400">Cliente desde {{ formatDate(customer.created_at) }}</p>
        </div>
        <Link
          v-if="$page.props.auth.user?.permissions?.includes('edit customers')"
          :href="route('customers.edit', customer.id)"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-50 hover:bg-violet-100 text-violet-700 text-sm font-semibold rounded-xl transition"
        >
          <PencilSquareIcon class="w-4 h-4" />
          Editar
        </Link>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Info -->
        <div class="space-y-4">
          <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Informações</h3>
            <div class="space-y-3">
              <div v-if="customer.email" class="flex items-center gap-2.5">
                <EnvelopeIcon class="w-4 h-4 text-gray-400 flex-shrink-0" />
                <span class="text-sm text-gray-600">{{ customer.email }}</span>
              </div>
              <div v-if="customer.phone" class="flex items-center gap-2.5">
                <PhoneIcon class="w-4 h-4 text-gray-400 flex-shrink-0" />
                <span class="text-sm text-gray-600">{{ customer.phone }}</span>
              </div>
              <div v-if="customer.document" class="flex items-center gap-2.5">
                <IdentificationIcon class="w-4 h-4 text-gray-400 flex-shrink-0" />
                <span class="text-sm text-gray-600">{{ customer.document }}</span>
              </div>
              <div v-if="customer.address" class="flex items-start gap-2.5">
                <MapPinIcon class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" />
                <div>
                  <p class="text-sm text-gray-600">{{ customer.address }}</p>
                  <p v-if="customer.city || customer.state" class="text-xs text-gray-400">
                    {{ customer.city }}<span v-if="customer.city && customer.state">, </span>{{ customer.state }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Stats -->
          <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Resumo</h3>
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Total de vendas</span>
                <span class="text-sm font-semibold text-gray-700">{{ customer.sales?.length || 0 }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Total gasto</span>
                <span class="text-sm font-bold text-violet-700">{{ formatCurrency(totalSpent) }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">Ticket médio</span>
                <span class="text-sm font-semibold text-gray-700">{{ formatCurrency(avgTicket) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Sales history -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-50">
              <h3 class="text-sm font-semibold text-gray-700">Histórico de Compras</h3>
            </div>
            <table class="w-full text-sm">
              <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs font-semibold uppercase tracking-wide">
                  <th class="text-left px-5 py-3">ID</th>
                  <th class="text-right px-4 py-3">Valor</th>
                  <th class="text-center px-4 py-3">Status</th>
                  <th class="text-left px-5 py-3">Data</th>
                  <th class="text-right px-5 py-3"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="sale in customer.sales" :key="sale.id" class="hover:bg-gray-50/50 transition">
                  <td class="px-5 py-3 text-gray-400 text-xs font-mono">#{{ String(sale.id).padStart(4,'0') }}</td>
                  <td class="px-4 py-3 text-right font-semibold text-gray-800">{{ formatCurrency(sale.total) }}</td>
                  <td class="px-4 py-3 text-center">
                    <span
                      class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold"
                      :class="{
                        'bg-green-100 text-green-700':  sale.status === 'completed',
                        'bg-yellow-100 text-yellow-700': sale.status === 'pending',
                        'bg-red-100 text-red-700':       sale.status === 'cancelled',
                      }"
                    >
                      {{ statusLabel[sale.status] }}
                    </span>
                  </td>
                  <td class="px-5 py-3 text-gray-400 text-xs">{{ formatDate(sale.created_at) }}</td>
                  <td class="px-5 py-3 text-right">
                    <Link :href="route('sales.show', sale.id)" class="text-violet-600 hover:text-violet-700 text-xs font-medium">
                      Ver
                    </Link>
                  </td>
                </tr>
                <tr v-if="!customer.sales?.length">
                  <td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">
                    Nenhuma compra registrada
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Pages/Layouts/AppLayout.vue';
import {
  ArrowLeftIcon, PencilSquareIcon, EnvelopeIcon,
  PhoneIcon, IdentificationIcon, MapPinIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  customer: { type: Object, required: true },
});

const statusLabel = { completed: 'Concluída', pending: 'Pendente', cancelled: 'Cancelada' };

const totalSpent = computed(() =>
  (props.customer.sales || []).filter(s => s.status === 'completed').reduce((a, s) => a + parseFloat(s.total || 0), 0)
);

const avgTicket = computed(() => {
  const completed = (props.customer.sales || []).filter(s => s.status === 'completed');
  return completed.length ? totalSpent.value / completed.length : 0;
});

function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v || 0);
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>
