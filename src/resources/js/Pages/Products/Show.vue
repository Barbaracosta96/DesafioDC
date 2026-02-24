<template>
  <AppLayout page-title="Detalhes do Produto">
    <Head :title="product.name" />

    <div class="max-w-4xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <Link :href="route('products.index')" class="p-2 rounded-xl hover:bg-gray-100 text-gray-400 transition">
            <ArrowLeftIcon class="w-4 h-4" />
          </Link>
          <div>
            <h2 class="text-xl font-bold text-gray-800">{{ product.name }}</h2>
            <p class="text-sm text-gray-400">SKU: {{ product.sku }}</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span
            class="inline-flex px-3 py-1 rounded-full text-xs font-semibold"
            :class="product.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
          >
            {{ product.status === 'active' ? 'Ativo' : 'Inativo' }}
          </span>
          <Link
            v-if="$page.props.auth.user?.permissions?.includes('edit products')"
            :href="route('products.edit', product.id)"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-violet-50 hover:bg-violet-100 text-violet-700 text-sm font-semibold rounded-xl transition"
          >
            <PencilSquareIcon class="w-4 h-4" />
            Editar
          </Link>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Image + Info -->
        <div class="space-y-4">
          <!-- Image -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-4 flex items-center justify-center aspect-square">
            <img
              v-if="product.image_path"
              :src="`/storage/${product.image_path}`"
              :alt="product.name"
              class="w-full h-full object-contain rounded-xl"
            />
            <div v-else class="flex flex-col items-center gap-2 text-gray-300">
              <PhotoIcon class="w-16 h-16" />
              <span class="text-xs">Sem imagem</span>
            </div>
          </div>

          <!-- Category -->
          <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Categoria</h3>
            <div class="flex items-center gap-2">
              <div
                v-if="product.category?.color"
                class="w-3 h-3 rounded-full"
                :style="`background:${product.category.color}`"
              />
              <span class="text-sm text-gray-700 font-medium">{{ product.category?.name || '—' }}</span>
            </div>
          </div>
        </div>

        <!-- Details -->
        <div class="lg:col-span-2 space-y-4">
          <!-- Pricing -->
          <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Precificação</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
              <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-0.5">Preço de Custo</p>
                <p class="text-lg font-bold text-gray-700">{{ formatCurrency(product.purchase_price) }}</p>
              </div>
              <div class="bg-violet-50 rounded-xl p-4">
                <p class="text-xs text-violet-400 mb-0.5">Preço de Venda</p>
                <p class="text-lg font-bold text-violet-700">{{ formatCurrency(product.sale_price) }}</p>
              </div>
              <div
                class="rounded-xl p-4"
                :class="margin >= 0 ? 'bg-green-50' : 'bg-red-50'"
              >
                <p class="text-xs mb-0.5" :class="margin >= 0 ? 'text-green-400' : 'text-red-400'">Margem</p>
                <p class="text-lg font-bold" :class="margin >= 0 ? 'text-green-700' : 'text-red-700'">
                  {{ margin.toFixed(1) }}%
                </p>
              </div>
            </div>
          </div>

          <!-- Stock -->
          <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Estoque</h3>
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-0.5">Quantidade Atual</p>
                <p
                  class="text-2xl font-bold"
                  :class="product.stock_quantity <= product.min_stock ? 'text-red-600' : 'text-gray-800'"
                >
                  {{ product.stock_quantity }}
                </p>
              </div>
              <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-400 mb-0.5">Estoque Mínimo</p>
                <p class="text-2xl font-bold text-gray-500">{{ product.min_stock }}</p>
              </div>
            </div>
            <div v-if="product.stock_quantity <= product.min_stock" class="mt-3 flex items-center gap-2 px-3 py-2 bg-red-50 rounded-xl">
              <ExclamationTriangleIcon class="w-4 h-4 text-red-500 flex-shrink-0" />
              <p class="text-xs text-red-600 font-medium">Estoque abaixo do mínimo — necessário repor</p>
            </div>
          </div>

          <!-- Description -->
          <div v-if="product.description" class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Descrição</h3>
            <p class="text-sm text-gray-600 leading-relaxed">{{ product.description }}</p>
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
import { ArrowLeftIcon, PencilSquareIcon, PhotoIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  product: { type: Object, required: true },
});

const margin = computed(() => {
  if (!props.product.purchase_price || !props.product.sale_price) return 0;
  return ((props.product.sale_price - props.product.purchase_price) / props.product.sale_price) * 100;
});

function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v || 0);
}
</script>
