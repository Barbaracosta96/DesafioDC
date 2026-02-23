<template>
  <AppLayout titulo="Categorias">
    <Head title="Categorias" />

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Categorias</h2>
        <p class="text-sm text-gray-500 mt-0.5">Organize os produtos por categoria</p>
      </div>
      <Botao @click="abrirModal()">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nova Categoria
      </Botao>
    </div>

    <!-- Grid de categorias -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="cat in categorias" :key="cat.id" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-start justify-between hover:border-indigo-200 transition-colors">
        <div>
          <div class="flex items-center gap-2">
            <p class="font-semibold text-gray-900">{{ cat.nome }}</p>
            <Badge :variant="cat.ativo ? 'verde' : 'cinza'" class="text-xs">{{ cat.ativo ? 'Ativa' : 'Inativa' }}</Badge>
          </div>
          <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ cat.descricao || 'Sem descrição' }}</p>
          <p class="text-xs text-indigo-600 font-medium mt-3">{{ cat.produtos_count }} produto(s)</p>
        </div>
        <div class="flex flex-col gap-1 ml-4 shrink-0">
          <button class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" @click="abrirModal(cat)" title="Editar">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
          <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" @click="confirmarExclusao(cat)" title="Excluir">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>

      <div v-if="!categorias.length" class="col-span-full text-center py-16 text-gray-400 text-sm">
        Nenhuma categoria cadastrada. Clique em "Nova Categoria" para começar.
      </div>
    </div>

    <!-- Modal criar/editar -->
    <Modal :aberto="modalAberto" :titulo="categoriaSelecionada ? 'Editar Categoria' : 'Nova Categoria'" @fechar="fecharModal">
      <div class="space-y-4">
        <InputField label="Nome *" v-model="form.nome" :erro="form.errors.nome" placeholder="Ex: Eletrônicos" />
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Descrição</label>
          <textarea v-model="form.descricao" rows="3" placeholder="Descrição da categoria..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
          <p v-if="form.errors.descricao" class="text-xs text-red-500 mt-1">{{ form.errors.descricao }}</p>
        </div>
        <div class="flex items-center gap-3">
          <button type="button" role="switch" :aria-checked="form.ativo" @click="form.ativo = !form.ativo" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="form.ativo ? 'bg-indigo-600' : 'bg-gray-300'">
            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform" :class="form.ativo ? 'translate-x-6' : 'translate-x-1'" />
          </button>
          <span class="text-sm text-gray-700">{{ form.ativo ? 'Ativa' : 'Inativa' }}</span>
        </div>
      </div>
      <template #footer>
        <Botao variant="secundario" @click="fecharModal">Cancelar</Botao>
        <Botao :carregando="form.processing" @click="salvar">{{ categoriaSelecionada ? 'Salvar' : 'Criar' }}</Botao>
      </template>
    </Modal>

    <!-- Modal exclusão -->
    <Modal :aberto="!!categoriaExcluir" titulo="Excluir Categoria" @fechar="categoriaExcluir = null">
      <p class="text-sm text-gray-600">
        Tem certeza que deseja excluir a categoria <strong>{{ categoriaExcluir?.nome }}</strong>?
        <span v-if="categoriaExcluir?.produtos_count > 0" class="block mt-2 text-amber-600">
          Esta categoria possui {{ categoriaExcluir.produtos_count }} produto(s) associado(s).
        </span>
      </p>
      <template #footer>
        <Botao variant="secundario" @click="categoriaExcluir = null">Cancelar</Botao>
        <Botao variant="perigo" :carregando="excluindo" @click="excluir">Excluir</Botao>
      </template>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Botao from '@/Components/Botao.vue';
import Badge from '@/Components/Badge.vue';
import Modal from '@/Components/Modal.vue';
import InputField from '@/Components/InputField.vue';

const props = defineProps({
  categorias: { type: Array, default: () => [] },
});

// Modal criar/editar
const modalAberto          = ref(false);
const categoriaSelecionada = ref(null);

const form = useForm({ nome: '', descricao: '', ativo: true });

const abrirModal = (cat = null) => {
  categoriaSelecionada.value = cat;
  form.reset();
  if (cat) {
    form.nome       = cat.nome;
    form.descricao  = cat.descricao ?? '';
    form.ativo      = cat.ativo;
  }
  modalAberto.value = true;
};

const fecharModal = () => { modalAberto.value = false; form.reset(); form.clearErrors(); };

const salvar = () => {
  if (categoriaSelecionada.value) {
    form.put(route('categorias.update', categoriaSelecionada.value.id), {
      onSuccess: fecharModal,
    });
  } else {
    form.post(route('categorias.store'), {
      onSuccess: fecharModal,
    });
  }
};

// Exclusão
const categoriaExcluir = ref(null);
const excluindo        = ref(false);
const confirmarExclusao = (c) => { categoriaExcluir.value = c; };
const excluir = () => {
  excluindo.value = true;
  router.delete(route('categorias.destroy', categoriaExcluir.value.id), {
    onFinish: () => { excluindo.value = false; categoriaExcluir.value = null; },
  });
};
</script>
