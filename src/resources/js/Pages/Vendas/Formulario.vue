<template>
  <AppLayout :titulo="venda ? 'Editar Venda' : 'Nova Venda'">
    <Head :title="venda ? 'Editar Venda' : 'Nova Venda'" />

    <div class="max-w-4xl">
      <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <Link :href="route('vendas.index')" class="hover:text-indigo-600">Vendas</Link>
        <span>/</span>
        <span class="text-gray-900 font-medium">{{ venda ? 'Editar Venda' : 'Nova Venda' }}</span>
      </div>

      <div v-if="!venda" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Registrar Nova Venda</h2>

        <form @submit.prevent="enviar" class="space-y-6">
          <!-- Cliente e Pagamento -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <SelectField
              v-model="form.cliente_id"
              label="Cliente"
              placeholder="Selecione o cliente (opcional)"
              :opcoes="clientes.map(c => ({ value: c.id, label: c.nome }))"
              :erro="form.errors.cliente_id"
            />
            <SelectField
              v-model="form.forma_pagamento"
              label="Forma de Pagamento"
              :opcoes="[{value:'dinheiro',label:'Dinheiro'},{value:'cartao_credito',label:'Cartão de Crédito'},{value:'cartao_debito',label:'Cartão de Débito'},{value:'pix',label:'PIX'}]"
              :erro="form.errors.forma_pagamento"
              obrigatorio
            />
          </div>

          <!-- Produtos -->
          <div>
            <div class="flex items-center justify-between mb-3">
              <label class="text-sm font-medium text-gray-700">Produtos <span class="text-red-500">*</span></label>
              <button type="button" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1" @click="adicionarItem">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Adicionar item
              </button>
            </div>

            <div class="space-y-3">
              <div
                v-for="(item, i) in form.itens"
                :key="i"
                class="flex gap-3 items-start bg-gray-50 rounded-xl p-3"
              >
                <div class="flex-1">
                  <select
                    v-model="item.produto_id"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    @change="preencherPreco(item)"
                  >
                    <option value="">Selecione o produto</option>
                    <option v-for="p in produtos" :key="p.id" :value="p.id">
                      {{ p.nome }} ({{ p.codigo_sku }}) — R$ {{ Number(p.preco_venda).toFixed(2) }} — Estoque: {{ p.quantidade_estoque }}
                    </option>
                  </select>
                </div>
                <div class="w-24">
                  <input
                    v-model.number="item.quantidade"
                    type="number"
                    min="1"
                    placeholder="Qtd"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    @input="recalcular"
                  />
                </div>
                <div class="w-28">
                  <input
                    v-model.number="item.preco_unitario"
                    type="number"
                    step="0.01"
                    placeholder="Preço"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    @input="recalcular"
                  />
                </div>
                <div class="w-24 py-2 text-sm text-right font-semibold text-gray-700">
                  R$ {{ (item.quantidade * item.preco_unitario || 0).toFixed(2) }}
                </div>
                <button type="button" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg" @click="removerItem(i)">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <p v-if="form.errors.itens" class="mt-1.5 text-xs text-red-600">{{ form.errors.itens }}</p>
          </div>

          <!-- Totais -->
          <div class="bg-indigo-50 rounded-xl p-4 space-y-2">
            <div class="flex justify-between text-sm text-gray-600">
              <span>Subtotal</span>
              <span>R$ {{ subtotal.toFixed(2) }}</span>
            </div>
            <div class="flex items-center justify-between text-sm text-gray-600">
              <span>Desconto (R$)</span>
              <input
                v-model.number="form.desconto"
                type="number"
                step="0.01"
                min="0"
                class="w-24 px-2 py-1 rounded-lg border border-gray-300 text-sm text-right focus:outline-none focus:ring-2 focus:ring-indigo-500"
                @input="recalcular"
              />
            </div>
            <div class="flex justify-between text-base font-semibold text-gray-900 border-t border-indigo-100 pt-2">
              <span>Total</span>
              <span>R$ {{ total.toFixed(2) }}</span>
            </div>
          </div>

          <!-- Observações -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Observações</label>
            <textarea
              v-model="form.observacoes"
              rows="2"
              placeholder="Observações sobre o pedido..."
              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
            />
          </div>

          <div class="flex gap-3 pt-2">
            <Botao type="submit" :carregando="form.processing">Registrar Venda</Botao>
            <Botao variant="secundario" :href="route('vendas.index')">Cancelar</Botao>
          </div>
        </form>
      </div>

      <!-- Edição (só status) -->
      <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Atualizar Pedido {{ venda.numero_pedido }}</h2>
        <form @submit.prevent="enviarEdicao" class="space-y-5 max-w-md">
          <SelectField
            v-model="editForm.status"
            label="Status do Pedido"
            :opcoes="[{value:'pendente',label:'Pendente'},{value:'processando',label:'Processando'},{value:'concluido',label:'Concluído'},{value:'cancelado',label:'Cancelado'}]"
            obrigatorio
          />
          <SelectField
            v-model="editForm.forma_pagamento"
            label="Forma de Pagamento"
            :opcoes="[{value:'dinheiro',label:'Dinheiro'},{value:'cartao_credito',label:'Cartão de Crédito'},{value:'cartao_debito',label:'Cartão de Débito'},{value:'pix',label:'PIX'}]"
            obrigatorio
          />
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Observações</label>
            <textarea v-model="editForm.observacoes" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none" />
          </div>
          <div class="flex gap-3">
            <Botao type="submit" :carregando="editForm.processing">Salvar</Botao>
            <Botao variant="secundario" :href="route('vendas.index')">Cancelar</Botao>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Botao from '@/Components/Botao.vue';
import SelectField from '@/Components/SelectField.vue';

const props = defineProps({
  venda:    { type: Object, default: null },
  clientes: { type: Array,  default: () => [] },
  produtos: { type: Array,  default: () => [] },
});

// Novo formulário de venda
const form = useForm({
  cliente_id:      '',
  forma_pagamento: '',
  desconto:        0,
  observacoes:     '',
  itens:           [{ produto_id: '', quantidade: 1, preco_unitario: 0 }],
});

const adicionarItem = () => form.itens.push({ produto_id: '', quantidade: 1, preco_unitario: 0 });
const removerItem   = (i) => form.itens.splice(i, 1);
const preencherPreco = (item) => {
  const p = props.produtos.find(p => p.id == item.produto_id);
  if (p) item.preco_unitario = Number(p.preco_venda);
  recalcular();
};
const recalcular = () => {}; // reativo via computed

const subtotal = computed(() => form.itens.reduce((acc, i) => acc + (i.quantidade * i.preco_unitario || 0), 0));
const total    = computed(() => Math.max(subtotal.value - (form.desconto || 0), 0));

const enviar = () => form.post(route('vendas.store'));

// Formulário de edição
const editForm = useForm({
  status:          props.venda?.status          ?? '',
  forma_pagamento: props.venda?.forma_pagamento ?? '',
  observacoes:     props.venda?.observacoes     ?? '',
});
const enviarEdicao = () => editForm.put(route('vendas.update', props.venda?.id));
</script>
