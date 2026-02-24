<template>
  <AppLayout page-title="Nova Venda">
    <Head title="Nova Venda" />

    <div class="max-w-4xl mx-auto">
      <div class="flex items-center gap-3 mb-6">
        <Link :href="route('sales.index')" class="p-2 rounded-xl hover:bg-gray-100 text-gray-400 transition">
          <ArrowLeftIcon class="w-4 h-4" />
        </Link>
        <div>
          <h2 class="text-xl font-bold text-gray-800">Nova Venda</h2>
          <p class="text-sm text-gray-400">Registre os produtos e dados do cliente</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-4">

        <!-- Customer & Notes -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
          <h3 class="text-sm font-semibold text-gray-700 mb-4">Dados do Pedido</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-xs font-medium text-gray-500 mb-1 block">Cliente (opcional)</label>
              <select v-model="form.customer_id" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400">
                <option value="">Consumidor Final</option>
                <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>
            <div>
              <label class="text-xs font-medium text-gray-500 mb-1 block">Desconto (R$)</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">R$</span>
                <input
                  v-model="form.discount"
                  type="number"
                  min="0"
                  step="0.01"
                  placeholder="0,00"
                  class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400"
                />
              </div>
              <p v-if="errors.discount" class="text-red-500 text-xs mt-1">{{ errors.discount }}</p>
            </div>
            <div class="sm:col-span-2">
              <label class="text-xs font-medium text-gray-500 mb-1 block">Observações</label>
              <textarea
                v-model="form.notes"
                rows="2"
                placeholder="Anotações internas ou instruções de entrega..."
                class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 resize-none"
              />
            </div>
          </div>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-gray-700">Produtos</h3>
            <button
              type="button"
              @click="addItem"
              class="inline-flex items-center gap-1.5 text-xs font-semibold text-violet-600 hover:text-violet-700 transition"
            >
              <PlusIcon class="w-3.5 h-3.5" />
              Adicionar item
            </button>
          </div>

          <p v-if="errors.items" class="text-red-500 text-xs mb-3 bg-red-50 px-3 py-2 rounded-lg">{{ errors.items }}</p>

          <div class="space-y-3">
            <div
              v-for="(item, idx) in form.items"
              :key="idx"
              class="flex items-end gap-3 p-3 bg-gray-50 rounded-xl"
            >
              <div class="flex-1">
                <label class="text-xs font-medium text-gray-500 mb-1 block">Produto</label>
                <select
                  v-model="item.product_id"
                  @change="onProductChange(idx)"
                  class="w-full px-3 py-2 border border-gray-200 bg-white rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400"
                >
                  <option value="">Selecione...</option>
                  <option v-for="p in products" :key="p.id" :value="p.id">
                    {{ p.name }} (Estoque: {{ p.stock_quantity }})
                  </option>
                </select>
                <p v-if="errors[`items.${idx}.product_id`]" class="text-red-500 text-xs mt-0.5">
                  {{ errors[`items.${idx}.product_id`] }}
                </p>
              </div>
              <div class="w-28">
                <label class="text-xs font-medium text-gray-500 mb-1 block">Qtd.</label>
                <input
                  v-model.number="item.quantity"
                  type="number"
                  min="1"
                  class="w-full px-3 py-2 border border-gray-200 bg-white rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400"
                />
                <p v-if="errors[`items.${idx}.quantity`]" class="text-red-500 text-xs mt-0.5">
                  {{ errors[`items.${idx}.quantity`] }}
                </p>
              </div>
              <div class="w-36">
                <label class="text-xs font-medium text-gray-500 mb-1 block">Preço Unit.</label>
                <div class="relative">
                  <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-xs">R$</span>
                  <input
                    v-model.number="item.unit_price"
                    type="number"
                    min="0"
                    step="0.01"
                    class="w-full pl-8 pr-2 py-2 border border-gray-200 bg-white rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400"
                  />
                </div>
              </div>
              <div class="w-32 text-right">
                <label class="text-xs font-medium text-gray-500 mb-1 block">Subtotal</label>
                <span class="text-sm font-semibold text-gray-800 block mt-2">
                  {{ formatCurrency((item.quantity || 0) * (item.unit_price || 0)) }}
                </span>
              </div>
              <button
                type="button"
                @click="removeItem(idx)"
                class="p-1.5 rounded-lg hover:bg-red-50 text-gray-300 hover:text-red-400 transition mb-1"
              >
                <TrashIcon class="w-4 h-4" />
              </button>
            </div>

            <div v-if="!form.items.length" class="py-8 text-center text-gray-400 text-sm">
              Nenhum produto adicionado
            </div>
          </div>
        </div>

        <!-- Totals & Submit -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50 flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4">
          <div class="space-y-1 text-sm">
            <div class="flex items-center justify-between gap-16">
              <span class="text-gray-400">Subtotal</span>
              <span class="font-medium text-gray-700">{{ formatCurrency(subtotal) }}</span>
            </div>
            <div class="flex items-center justify-between gap-16">
              <span class="text-gray-400">Desconto</span>
              <span class="font-medium text-red-500">- {{ formatCurrency(form.discount || 0) }}</span>
            </div>
            <div class="flex items-center justify-between gap-16 pt-2 border-t border-gray-100">
              <span class="font-semibold text-gray-700">Total</span>
              <span class="text-lg font-bold text-violet-700">{{ formatCurrency(total) }}</span>
            </div>
          </div>

          <div class="flex gap-3">
            <Link
              :href="route('sales.index')"
              class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition"
            >
              Cancelar
            </Link>
            <button
              type="submit"
              :disabled="form.processing || !form.items.length"
              class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition shadow-sm"
            >
              <span v-if="form.processing">Registrando...</span>
              <span v-else>Registrar Venda</span>
            </button>
          </div>
        </div>

      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Pages/Layouts/AppLayout.vue';
import { ArrowLeftIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  products:  { type: Array, default: () => [] },
  customers: { type: Array, default: () => [] },
  errors:    { type: Object, default: () => ({}) },
});

const form = useForm({
  customer_id: '',
  discount:    0,
  notes:       '',
  items:       [],
});

const subtotal = computed(() =>
  form.items.reduce((acc, it) => acc + (it.quantity || 0) * (it.unit_price || 0), 0)
);

const total = computed(() => Math.max(0, subtotal.value - (parseFloat(form.discount) || 0)));

function addItem() {
  form.items.push({ product_id: '', quantity: 1, unit_price: 0 });
}

function removeItem(idx) {
  form.items.splice(idx, 1);
}

function onProductChange(idx) {
  const item = form.items[idx];
  const product = props.products.find(p => p.id === parseInt(item.product_id));
  if (product) item.unit_price = product.sale_price;
}

function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v || 0);
}

function submit() {
  form.post(route('sales.store'));
}
</script>
