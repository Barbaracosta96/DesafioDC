<template>
  <AppLayout titulo="Detalhes do Cliente">
    <Head :title="`Cliente - ${cliente.nome}`" />

    <div class="flex items-center gap-3 mb-6">
      <Botao variant="fantasma" size="sm" :href="route('clientes.index')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Voltar
      </Botao>
      <h2 class="text-2xl font-bold text-gray-900">{{ cliente.nome }}</h2>
      <Badge :variant="cliente.tipo === 'pessoa_fisica' ? 'azul' : 'roxo'" class="ml-auto">{{ cliente.tipo === 'pessoa_fisica' ? 'PF' : 'PJ' }}</Badge>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Dados do cliente -->
      <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
          <div class="flex items-center gap-4 mb-5">
            <div class="w-14 h-14 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-700 text-xl font-bold">
              {{ iniciais(cliente.nome) }}
            </div>
            <div>
              <p class="font-semibold text-gray-900">{{ cliente.nome }}</p>
              <p class="text-sm text-gray-500">{{ cliente.tipo === 'pessoa_fisica' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</p>
            </div>
          </div>

          <dl class="space-y-3">
            <div v-if="cliente.email">
              <dt class="text-xs text-gray-400">E-mail</dt>
              <dd class="text-sm text-gray-700">{{ cliente.email }}</dd>
            </div>
            <div v-if="cliente.telefone">
              <dt class="text-xs text-gray-400">Telefone</dt>
              <dd class="text-sm text-gray-700">{{ cliente.telefone }}</dd>
            </div>
            <div v-if="cliente.cpf_cnpj">
              <dt class="text-xs text-gray-400">{{ cliente.tipo === 'pessoa_fisica' ? 'CPF' : 'CNPJ' }}</dt>
              <dd class="text-sm text-gray-700">{{ cliente.cpf_cnpj }}</dd>
            </div>
            <div v-if="cliente.endereco_completo">
              <dt class="text-xs text-gray-400">Endereço</dt>
              <dd class="text-sm text-gray-700">{{ cliente.endereco_completo }}</dd>
            </div>
          </dl>

          <div class="flex gap-3 mt-6">
            <Botao :href="route('clientes.edit', cliente.id)" class="flex-1 justify-center">Editar</Botao>
          </div>
        </div>

        <!-- Cards estatísticas -->
        <div class="grid grid-cols-2 gap-4">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-indigo-600">{{ cliente.total_compras }}</p>
            <p class="text-xs text-gray-500 mt-1">Compras</p>
          </div>
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-lg font-bold text-emerald-600">{{ cliente.valor_total }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Gasto</p>
          </div>
        </div>
      </div>

      <!-- Histórico de compras -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Histórico de Compras</h3>
          </div>
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left text-xs font-medium text-gray-500 px-6 py-3 uppercase">Pedido</th>
                <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">Data</th>
                <th class="text-center text-xs font-medium text-gray-500 px-4 py-3 uppercase">Status</th>
                <th class="text-right text-xs font-medium text-gray-500 px-6 py-3 uppercase">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="venda in vendas" :key="venda.id" class="hover:bg-gray-50">
                <td class="px-6 py-4">
                  <Link :href="route('vendas.show', venda.id)" class="text-sm font-medium text-indigo-600 hover:underline">
                    {{ venda.numero_pedido }}
                  </Link>
                </td>
                <td class="px-4 py-4 text-sm text-gray-500">{{ venda.data_venda }}</td>
                <td class="px-4 py-4 text-center">
                  <Badge :variant="corStatus(venda.status)">{{ venda.status_label }}</Badge>
                </td>
                <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">{{ venda.total }}</td>
              </tr>
              <tr v-if="!vendas.length">
                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-400">Nenhuma compra registrada.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Botao from '@/Components/Botao.vue';
import Badge from '@/Components/Badge.vue';

defineProps({
  cliente: { type: Object, required: true },
  vendas:  { type: Array,  default: () => [] },
});

const iniciais  = (nome) => nome.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
const corStatus = (s) => ({ concluido: 'verde', pendente: 'amarelo', processando: 'azul', cancelado: 'vermelho' }[s] ?? 'cinza');
</script>
