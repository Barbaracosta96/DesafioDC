<template>
  <AppLayout :page-title="isEditing ? 'Editar Produto' : 'Novo Produto'">
    <Head :title="isEditing ? 'Editar Produto' : 'Novo Produto'" />

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
      <Link :href="route('products.index')" class="hover:text-violet-600">Produtos</Link>
      <ChevronRightIcon class="w-4 h-4" />
      <span class="text-gray-600 font-medium">{{ isEditing ? 'Editar' : 'Novo' }}</span>
    </div>

    <form @submit.prevent="submit" enctype="multipart/form-data">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Main fields -->
        <div class="lg:col-span-2 space-y-5">
          <!-- Basic info card -->
          <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="font-bold text-gray-800 mb-4">Informações Básicas</h3>
            <div class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome do Produto *</label>
                  <input
                    v-model="form.name"
                    type="text"
                    placeholder="Ex: Home Decor Range"
                    class="w-full px-4 py-3 bg-gray-50 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-200 focus:border-violet-400"
                    :class="form.errors.name ? 'border-red-300' : 'border-gray-200'"
                  />
                  <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">SKU</label>
                  <input
                    v-model="form.sku"
                    type="text"
                    placeholder="Ex: PROD-001"
                    class="w-full px-4 py-3 bg-gray-50 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-200 focus:border-violet-400 font-mono"
                    :class="form.errors.sku ? 'border-red-300' : 'border-gray-200'"
                  />
                  <p v-if="form.errors.sku" class="mt-1 text-xs text-red-500">{{ form.errors.sku }}</p>
                  <p class="mt-1 text-xs text-gray-400">Deixe em branco para gerar automaticamente</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">Categoria</label>
                  <select
                    v-model="form.category_id"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-200 focus:border-violet-400"
                  >
                    <option value="">Sem categoria</option>
                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                  </select>
                </div>

                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">Descrição</label>
                  <textarea
                    v-model="form.description"
                    rows="3"
                    placeholder="Descreva o produto..."
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-200 focus:border-violet-400 resize-none"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Pricing & Stock -->
          <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="font-bold text-gray-800 mb-4">Preço e Estoque</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Preço de Custo</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">R$</span>
                  <input
                    v-model="form.purchase_price"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0,00"
                    class="w-full pl-9 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-200 focus:border-violet-400"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Preço de Venda *</label>
                <div class="relative">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">R$</span>
                  <input
                    v-model="form.sale_price"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0,00"
                    class="w-full pl-9 pr-4 py-3 bg-gray-50 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-200 focus:border-violet-400"
                    :class="form.errors.sale_price ? 'border-red-300' : 'border-gray-200'"
                  />
                </div>
                <p v-if="form.errors.sale_price" class="mt-1 text-xs text-red-500">{{ form.errors.sale_price }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Qtd. em Estoque *</label>
                <input
                  v-model="form.stock_quantity"
                  type="number"
                  min="0"
                  placeholder="0"
                  class="w-full px-4 py-3 bg-gray-50 border rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-200 focus:border-violet-400"
                  :class="form.errors.stock_quantity ? 'border-red-300' : 'border-gray-200'"
                />
                <p v-if="form.errors.stock_quantity" class="mt-1 text-xs text-red-500">{{ form.errors.stock_quantity }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Estoque Mínimo</label>
                <input
                  v-model="form.min_stock"
                  type="number"
                  min="0"
                  placeholder="5"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-200 focus:border-violet-400"
                />
                <p class="mt-1 text-xs text-gray-400">Alerta de reposição</p>
              </div>
            </div>

            <!-- Margin indicator -->
            <div
              v-if="form.purchase_price > 0 && form.sale_price > 0"
              class="mt-4 p-3 bg-violet-50 rounded-xl"
            >
              <p class="text-sm text-violet-700">
                Margem de lucro:
                <strong>{{ marginPercent }}%</strong>
                ({{ formatCurrency(form.sale_price - form.purchase_price) }} por unidade)
              </p>
            </div>
          </div>
        </div>

        <!-- Sidebar: Image + Status -->
        <div class="space-y-5">
          <!-- Image -->
          <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="font-bold text-gray-800 mb-4">Imagem do Produto</h3>
            <div
              class="relative flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 cursor-pointer hover:border-violet-300 transition overflow-hidden"
              @click="$refs.imageInput.click()"
            >
              <img
                v-if="imagePreview"
                :src="imagePreview"
                class="absolute inset-0 w-full h-full object-cover"
                alt="Preview"
              />
              <div v-if="!imagePreview" class="flex flex-col items-center gap-2">
                <PhotoIcon class="w-8 h-8 text-gray-300" />
                <p class="text-xs text-gray-400 text-center">Clique para upload<br>PNG, JPG até 2MB</p>
              </div>
              <div v-if="imagePreview" class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 hover:opacity-100 transition">
                <p class="text-white text-xs font-medium">Trocar imagem</p>
              </div>
            </div>
            <input
              ref="imageInput"
              type="file"
              accept="image/*"
              class="hidden"
              @change="handleImageChange"
            />
          </div>

          <!-- Status -->
          <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
            <h3 class="font-bold text-gray-800 mb-4">Status</h3>
            <div class="space-y-2">
              <label
                v-for="option in statusOptions"
                :key="option.value"
                class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition"
                :class="form.status === option.value ? 'border-violet-300 bg-violet-50' : 'border-gray-100 hover:bg-gray-50'"
              >
                <input
                  type="radio"
                  v-model="form.status"
                  :value="option.value"
                  class="hidden"
                />
                <div
                  class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                  :class="form.status === option.value ? 'border-violet-600' : 'border-gray-300'"
                >
                  <div
                    v-if="form.status === option.value"
                    class="w-2 h-2 rounded-full bg-violet-600"
                  ></div>
                </div>
                <div>
                  <p class="text-sm font-medium text-gray-700">{{ option.label }}</p>
                  <p class="text-xs text-gray-400">{{ option.desc }}</p>
                </div>
              </label>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-col gap-2">
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-3 bg-violet-600 hover:bg-violet-700 disabled:opacity-60 text-white font-semibold rounded-xl transition text-sm shadow-sm"
            >
              {{ form.processing ? 'Salvando...' : (isEditing ? 'Atualizar Produto' : 'Criar Produto') }}
            </button>
            <Link
              :href="route('products.index')"
              class="w-full py-3 text-center border border-gray-200 hover:bg-gray-50 text-gray-600 font-medium rounded-xl transition text-sm"
            >
              Cancelar
            </Link>
          </div>
        </div>
      </div>
    </form>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Pages/Layouts/AppLayout.vue';
import { ChevronRightIcon, PhotoIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  product:    { type: Object, default: null },
  categories: { type: Array, default: () => [] },
});

const isEditing = computed(() => !!props.product);

const form = useForm({
  category_id:    props.product?.category_id || '',
  name:           props.product?.name || '',
  sku:            props.product?.sku || '',
  description:    props.product?.description || '',
  purchase_price: props.product?.purchase_price || '',
  sale_price:     props.product?.sale_price || '',
  stock_quantity: props.product?.stock_quantity ?? 0,
  min_stock:      props.product?.min_stock ?? 5,
  status:         props.product?.status || 'active',
  image:          null,
  _method:        isEditing.value ? 'PUT' : undefined,
});

const imagePreview = ref(
  props.product?.image_path ? `/storage/${props.product.image_path}` : null
);

const statusOptions = [
  { value: 'active',   label: 'Ativo',   desc: 'Disponível para venda' },
  { value: 'inactive', label: 'Inativo', desc: 'Não disponível para venda' },
];

const marginPercent = computed(() => {
  if (!form.purchase_price || !form.sale_price) return 0;
  return Math.round(((form.sale_price - form.purchase_price) / form.sale_price) * 100);
});

function formatCurrency(value) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
}

function handleImageChange(event) {
  const file = event.target.files[0];
  if (!file) return;
  form.image = file;
  imagePreview.value = URL.createObjectURL(file);
}

function submit() {
  if (isEditing.value) {
    form.post(route('products.update', props.product.id));
  } else {
    form.post(route('products.store'));
  }
}
</script>
