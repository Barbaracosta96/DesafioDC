<template>
  <AppLayout page-title="Detalhes da Venda">
    <Head :title="`Venda #${String(sale.id).padStart(4,'0')}`" />

    <div class="max-w-4xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <Link :href="route('sales.index')" class="p-2 rounded-xl hover:bg-gray-100 text-gray-400 transition">
            <ArrowLeftIcon class="w-4 h-4" />
          </Link>
          <div>
            <h2 class="text-xl font-bold text-gray-800">Venda #{{ String(sale.id).padStart(4,'0') }}</h2>
            <p class="text-sm text-gray-400">{{ formatDate(sale.created_at) }}</p>
          </div>
        </div>
        <span
          class="inline-flex px-3 py-1 rounded-full text-sm font-semibold"
          :class="{
            'bg-green-100 text-green-700': sale.status === 'completed',
            'bg-yellow-100 text-yellow-700': sale.status === 'pending',
            'bg-red-100 text-red-700': sale.status === 'cancelled',
          }"
        >
          {{ statusLabel[sale.status] }}
        </span>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Left: items -->
        <div class="lg:col-span-2 space-y-4">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-50">
              <h3 class="text-sm font-semibold text-gray-700">Itens da Venda</h3>
            </div>
            <table class="w-full text-sm">
              <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs font-semibold uppercase tracking-wide">
                  <th class="text-left px-5 py-3">Produto</th>
                  <th class="text-center px-4 py-3">Qtd.</th>
                  <th class="text-right px-4 py-3">Preço Unit.</th>
                  <th class="text-right px-5 py-3">Total</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="item in sale.items" :key="item.id" class="hover:bg-gray-50/50">
                  <td class="px-5 py-3.5">
                    <div>
                      <p class="font-medium text-gray-700">{{ item.product?.name }}</p>
                      <p class="text-xs text-gray-400">SKU: {{ item.product?.sku }}</p>
                    </div>
                  </td>
                  <td class="px-4 py-3.5 text-center text-gray-600">{{ item.quantity }}</td>
                  <td class="px-4 py-3.5 text-right text-gray-600">{{ formatCurrency(item.unit_price) }}</td>
                  <td class="px-5 py-3.5 text-right font-semibold text-gray-800">{{ formatCurrency(item.total_price) }}</td>
                </tr>
              </tbody>
            </table>

            <div class="px-5 py-4 border-t border-gray-50 bg-gray-50/50 space-y-2">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">Subtotal</span>
                <span class="text-gray-700">{{ formatCurrency(sale.subtotal) }}</span>
              </div>
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">Desconto</span>
                <span class="text-red-500">- {{ formatCurrency(sale.discount) }}</span>
              </div>
              <div class="flex items-center justify-between text-sm font-bold pt-2 border-t border-gray-200">
                <span class="text-gray-700">Total</span>
                <span class="text-lg text-violet-700">{{ formatCurrency(sale.total) }}</span>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="sale.notes" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Observações</h3>
            <p class="text-sm text-gray-500">{{ sale.notes }}</p>
          </div>
        </div>

        <!-- Right: meta + actions -->
        <div class="space-y-4">
          <!-- Seller -->
          <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Vendedor</h3>
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full bg-violet-100 flex items-center justify-center text-violet-700 font-bold text-sm">
                {{ (sale.user?.name || '?')[0].toUpperCase() }}
              </div>
              <div>
                <p class="text-sm font-medium text-gray-700">{{ sale.user?.name }}</p>
                <p class="text-xs text-gray-400">{{ sale.user?.email }}</p>
              </div>
            </div>
          </div>

          <!-- Customer -->
          <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Cliente</h3>
            <div v-if="sale.customer">
              <p class="text-sm font-medium text-gray-700">{{ sale.customer.name }}</p>
              <p class="text-xs text-gray-400">{{ sale.customer.email }}</p>
              <p class="text-xs text-gray-400">{{ sale.customer.phone }}</p>
            </div>
            <p v-else class="text-sm text-gray-400 italic">Consumidor Final</p>
          </div>

          <!-- Change status -->
          <div
            v-if="$page.props.auth.user?.permissions?.includes('manage sales') && sale.status !== 'cancelled'"
            class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50"
          >
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Atualizar Status</h3>
            <div class="flex flex-col gap-2">
              <button
                v-if="sale.status === 'pending'"
                @click="updateStatus('completed')"
                :disabled="statusForm.processing"
                class="w-full py-2 bg-green-500 hover:bg-green-600 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition"
              >
                Marcar como Concluída
              </button>
              <button
                v-if="sale.status !== 'cancelled'"
                @click="confirmCancel = true"
                class="w-full py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold rounded-xl transition"
              >
                Cancelar Venda
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancel confirm modal -->
    <Teleport to="body">
      <div v-if="confirmCancel" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
              <ExclamationTriangleIcon class="w-5 h-5 text-red-500" />
            </div>
            <h3 class="text-base font-bold text-gray-800">Cancelar Venda?</h3>
          </div>
          <p class="text-sm text-gray-500 mb-5">O estoque dos produtos será restaurado automaticamente.</p>
          <div class="flex gap-3">
            <button @click="confirmCancel = false" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
              Voltar
            </button>
            <button
              @click="updateStatus('cancelled')"
              :disabled="statusForm.processing"
              class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition"
            >
              Confirmar
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Pages/Layouts/AppLayout.vue';
import { ArrowLeftIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  sale: { type: Object, required: true },
});

const statusLabel = { completed: 'Concluída', pending: 'Pendente', cancelled: 'Cancelada' };
const confirmCancel = ref(false);

const statusForm = useForm({ status: '' });

function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v || 0);
}

function formatDate(date) {
  return new Date(date).toLocaleString('pt-BR', { dateStyle: 'long', timeStyle: 'short' });
}

function updateStatus(status) {
  confirmCancel.value = false;
  statusForm.status = status;
  statusForm.patch(route('sales.status', props.sale.id));
}
</script>
