<template>
  <AppLayout page-title="Produtos">
    <Head title="Produtos" />

    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Estoque de Produtos</h2>
        <p class="text-sm text-gray-400 mt-0.5">{{ products.total }} produto(s) cadastrado(s)</p>
      </div>
      <Link
        v-if="$page.props.auth.user?.permissions?.includes('create products')"
        :href="route('products.create')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-xl transition shadow-sm"
      >
        <PlusIcon class="w-4 h-4" />
        Novo Produto
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
            placeholder="Buscar por nome ou SKU..."
            class="flex-1 bg-transparent text-sm outline-none text-gray-700"
          />
        </div>
        <select
          v-model="filterForm.category"
          class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 outline-none focus:ring-2 focus:ring-violet-200"
        >
          <option value="">Todas as categorias</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <select
          v-model="filterForm.status"
          class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 outline-none focus:ring-2 focus:ring-violet-200"
        >
          <option value="">Todos os status</option>
          <option value="active">Ativo</option>
          <option value="inactive">Inativo</option>
        </select>
        <button
          type="submit"
          class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-xl transition"
        >
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
              <th class="text-left px-5 py-3.5">Produto</th>
              <th class="text-left px-5 py-3.5">SKU</th>
              <th class="text-left px-5 py-3.5">Categoria</th>
              <th class="text-right px-5 py-3.5">Preço</th>
              <th class="text-center px-5 py-3.5">Estoque</th>
              <th class="text-center px-5 py-3.5">Status</th>
              <th class="text-right px-5 py-3.5">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="product in products.data"
              :key="product.id"
              class="hover:bg-gray-50 transition"
            >
              <td class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
                    <CubeIcon class="w-5 h-5 text-violet-500" />
                  </div>
                  <div>
                    <p class="font-semibold text-gray-800">{{ product.name }}</p>
                    <p v-if="product.description" class="text-xs text-gray-400 truncate max-w-48">{{ product.description }}</p>
                  </div>
                </div>
              </td>
              <td class="px-5 py-3.5">
                <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">{{ product.sku }}</span>
              </td>
              <td class="px-5 py-3.5">
                <span v-if="product.category" class="px-2.5 py-1 rounded-lg text-xs font-medium bg-violet-100 text-violet-700">
                  {{ product.category.name }}
                </span>
                <span v-else class="text-gray-400 text-xs">—</span>
              </td>
              <td class="px-5 py-3.5 text-right font-semibold text-gray-800">
                {{ formatCurrency(product.sale_price) }}
              </td>
              <td class="px-5 py-3.5 text-center">
                <span
                  class="font-semibold text-sm"
                  :class="product.stock_quantity <= product.min_stock ? 'text-red-500' : 'text-gray-800'"
                >
                  {{ product.stock_quantity }}
                </span>
                <span
                  v-if="product.stock_quantity <= product.min_stock"
                  class="ml-1 px-1.5 py-0.5 bg-red-100 text-red-600 text-xs rounded-md"
                >
                  Baixo
                </span>
              </td>
              <td class="px-5 py-3.5 text-center">
                <span
                  class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                  :class="product.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                >
                  {{ product.status === 'active' ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-2">
                  <Link
                    :href="route('products.show', product.id)"
                    class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition"
                    title="Ver"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </Link>
                  <Link
                    v-if="$page.props.auth.user?.permissions?.includes('edit products')"
                    :href="route('products.edit', product.id)"
                    class="p-1.5 rounded-lg hover:bg-violet-50 text-gray-400 hover:text-violet-600 transition"
                    title="Editar"
                  >
                    <PencilIcon class="w-4 h-4" />
                  </Link>
                  <button
                    v-if="$page.props.auth.user?.permissions?.includes('delete products')"
                    @click="confirmDelete(product)"
                    class="p-1.5 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition"
                    title="Excluir"
                  >
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!products.data.length">
              <td colspan="7" class="px-5 py-12 text-center">
                <div class="flex flex-col items-center gap-2">
                  <CubeIcon class="w-10 h-10 text-gray-300" />
                  <p class="text-gray-400 text-sm">Nenhum produto encontrado</p>
                  <Link
                    :href="route('products.create')"
                    class="text-sm text-violet-600 font-medium hover:underline"
                  >
                    Cadastrar produto
                  </Link>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="products.last_page > 1" class="flex items-center justify-between px-5 py-3 border-t border-gray-50">
        <p class="text-xs text-gray-400">
          Mostrando {{ products.from }}–{{ products.to }} de {{ products.total }} resultados
        </p>
        <div class="flex gap-1">
          <Link
            v-for="page in products.links"
            :key="page.label"
            :href="page.url || '#'"
            class="px-3 py-1.5 text-xs rounded-lg transition"
            :class="page.active
              ? 'bg-violet-600 text-white font-semibold'
              : page.url
                ? 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                : 'bg-gray-50 text-gray-300 cursor-not-allowed'"
            v-html="page.label"
          />
        </div>
      </div>
    </div>

    <!-- Delete confirmation modal -->
    <div
      v-if="productToDelete"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40"
      @click.self="productToDelete = null"
    >
      <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
            <TrashIcon class="w-5 h-5 text-red-500" />
          </div>
          <div>
            <p class="font-bold text-gray-800">Excluir produto</p>
            <p class="text-sm text-gray-400">Esta ação não pode ser desfeita.</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-5">
          Tem certeza que deseja excluir <strong>{{ productToDelete.name }}</strong>?
        </p>
        <div class="flex gap-3">
          <button
            @click="productToDelete = null"
            class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm font-medium hover:bg-gray-50"
          >
            Cancelar
          </button>
          <button
            @click="deleteProduct"
            class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition"
          >
            Excluir
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Pages/Layouts/AppLayout.vue';
import {
  PlusIcon, MagnifyingGlassIcon, EyeIcon, PencilIcon,
  TrashIcon, CubeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  products:   { type: Object, default: () => ({}) },
  categories: { type: Array, default: () => [] },
  filters:    { type: Object, default: () => ({}) },
});

const filterForm = reactive({
  search:   props.filters.search || '',
  category: props.filters.category || '',
  status:   props.filters.status || '',
});

const productToDelete = ref(null);

function formatCurrency(value) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
}

function applyFilters() {
  router.get(route('products.index'), filterForm, { preserveState: true, replace: true });
}

function confirmDelete(product) {
  productToDelete.value = product;
}

function deleteProduct() {
  router.delete(route('products.destroy', productToDelete.value.id), {
    onSuccess: () => { productToDelete.value = null; },
  });
}
</script>
