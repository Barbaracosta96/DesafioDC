<template>
  <AppLayout :titulo="produto ? 'Editar Produto' : 'Novo Produto'">
    <Head :title="produto ? 'Editar Produto' : 'Novo Produto'" />

    <div class="max-w-3xl">
      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <Link :href="route('estoque.index')" class="hover:text-indigo-600">Estoque</Link>
        <span>/</span>
        <span class="text-gray-900 font-medium">{{ produto ? 'Editar' : 'Novo Produto' }}</span>
      </div>

      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">
          {{ produto ? 'Editar Produto' : 'Cadastrar Novo Produto' }}
        </h2>

        <form @submit.prevent="enviar" class="space-y-5">
          <!-- Linha 1: Nome + SKU -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <InputField
              v-model="form.nome"
              label="Nome do Produto"
              placeholder="Ex: Câmera DSLR"
              :erro="form.errors.nome"
              obrigatorio
            />
            <InputField
              v-model="form.codigo_sku"
              label="Código SKU"
              placeholder="Ex: CAM-001"
              :erro="form.errors.codigo_sku"
            />
          </div>

          <!-- Linha 2: Categoria + Unidade -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <SelectField
              v-model="form.categoria_id"
              label="Categoria"
              placeholder="Selecione a categoria"
              :opcoes="categorias"
              :erro="form.errors.categoria_id"
            />
            <SelectField
              v-model="form.unidade"
              label="Unidade de Medida"
              :opcoes="[{value:'un',label:'Unidade (un)'},{value:'kg',label:'Quilograma (kg)'},{value:'L',label:'Litro (L)'},{value:'m',label:'Metro (m)'},{value:'cx',label:'Caixa (cx)'}]"
              :erro="form.errors.unidade"
              obrigatorio
            />
          </div>

          <!-- Linha 3: Preços -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <InputField
              v-model="form.preco_custo"
              label="Preço de Custo (R$)"
              type="number"
              placeholder="0,00"
              :erro="form.errors.preco_custo"
              obrigatorio
            />
            <InputField
              v-model="form.preco_venda"
              label="Preço de Venda (R$)"
              type="number"
              placeholder="0,00"
              :erro="form.errors.preco_venda"
              obrigatorio
            />
          </div>

          <!-- Linha 4: Estoque -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <InputField
              v-model="form.quantidade_estoque"
              label="Quantidade em Estoque"
              type="number"
              placeholder="0"
              :erro="form.errors.quantidade_estoque"
              obrigatorio
            />
            <InputField
              v-model="form.estoque_minimo"
              label="Estoque Mínimo (alerta)"
              type="number"
              placeholder="5"
              :erro="form.errors.estoque_minimo"
              obrigatorio
            />
          </div>

          <!-- Descrição -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Descrição</label>
            <textarea
              v-model="form.descricao"
              rows="3"
              placeholder="Descrição detalhada do produto..."
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
            />
          </div>

          <!-- Status -->
          <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" v-model="form.ativo" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
            <span class="text-sm font-medium text-gray-700">Produto ativo</span>
          </label>

          <!-- Botões -->
          <div class="flex gap-3 pt-2">
            <Botao type="submit" :carregando="form.processing">
              {{ produto ? 'Salvar Alterações' : 'Cadastrar Produto' }}
            </Botao>
            <Botao variant="secundario" :href="route('estoque.index')">
              Cancelar
            </Botao>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Botao from '@/Components/Botao.vue';
import InputField from '@/Components/InputField.vue';
import SelectField from '@/Components/SelectField.vue';

const props = defineProps({
  produto:    { type: Object, default: null },
  categorias: { type: Array,  default: () => [] },
});

const form = useForm({
  nome:               props.produto?.nome               ?? '',
  codigo_sku:         props.produto?.codigo_sku         ?? '',
  categoria_id:       props.produto?.categoria_id       ?? '',
  descricao:          props.produto?.descricao          ?? '',
  preco_custo:        props.produto?.preco_custo        ?? '',
  preco_venda:        props.produto?.preco_venda        ?? '',
  quantidade_estoque: props.produto?.quantidade_estoque ?? 0,
  estoque_minimo:     props.produto?.estoque_minimo     ?? 5,
  unidade:            props.produto?.unidade            ?? 'un',
  ativo:              props.produto?.ativo              ?? true,
});

const enviar = () => {
  if (props.produto) {
    form.put(route('estoque.update', props.produto.id));
  } else {
    form.post(route('estoque.store'));
  }
};
</script>
