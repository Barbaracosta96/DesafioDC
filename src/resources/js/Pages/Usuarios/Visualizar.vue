<template>
  <AppLayout titulo="Detalhes do Usuário">
    <Head :title="`Usuário - ${usuario.name}`" />

    <div class="flex items-center gap-3 mb-6">
      <Botao variant="fantasma" size="sm" :href="route('usuarios.index')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Voltar
      </Botao>
      <h2 class="text-2xl font-bold text-gray-900">{{ usuario.name }}</h2>
      <div class="ml-auto flex items-center gap-2">
        <Badge v-for="r in usuario.roles" :key="r" :variant="badgeRole(r)">{{ r }}</Badge>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Perfil -->
      <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
          <div class="flex flex-col items-center text-center mb-5">
            <div class="w-16 h-16 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-700 text-2xl font-bold mb-3">
              {{ iniciais(usuario.name) }}
            </div>
            <p class="font-semibold text-gray-900">{{ usuario.name }}</p>
            <p class="text-sm text-gray-500">{{ usuario.email }}</p>
            <div class="flex gap-2 mt-2">
              <Badge v-for="r in usuario.roles" :key="r" :variant="badgeRole(r)">{{ nomeRole(r) }}</Badge>
            </div>
          </div>

          <dl class="space-y-3 border-t border-gray-100 pt-4">
            <div>
              <dt class="text-xs text-gray-400">Status</dt>
              <dd class="mt-0.5">
                <Badge :variant="usuario.ativo ? 'verde' : 'cinza'">{{ usuario.ativo ? 'Ativo' : 'Inativo' }}</Badge>
              </dd>
            </div>
            <div>
              <dt class="text-xs text-gray-400">Membro desde</dt>
              <dd class="text-sm text-gray-700">{{ usuario.criado }}</dd>
            </div>
          </dl>

          <div class="flex gap-3 mt-5">
            <Botao :href="route('usuarios.edit', usuario.id)" class="flex-1 justify-center">Editar</Botao>
          </div>
        </div>
      </div>

      <!-- Atividade -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-2xl font-bold text-indigo-600">{{ usuario.total_vendas }}</p>
            <p class="text-xs text-gray-500 mt-1">Vendas realizadas</p>
          </div>
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-2xl font-bold text-emerald-600">{{ usuario.total_movimentacoes }}</p>
            <p class="text-xs text-gray-500 mt-1">Movimentações</p>
          </div>
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center col-span-2 sm:col-span-1">
            <p class="text-lg font-bold text-gray-900">{{ usuario.receita_gerada }}</p>
            <p class="text-xs text-gray-500 mt-1">Receita gerada</p>
          </div>
        </div>

        <!-- Últimas vendas -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Últimas Vendas</h3>
          </div>
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left text-xs font-medium text-gray-500 px-6 py-3 uppercase">Pedido</th>
                <th class="text-left text-xs font-medium text-gray-500 px-4 py-3 uppercase">Cliente</th>
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
                <td class="px-4 py-4 text-sm text-gray-600">{{ venda.cliente }}</td>
                <td class="px-4 py-4 text-center">
                  <Badge :variant="corStatus(venda.status)">{{ venda.status_label }}</Badge>
                </td>
                <td class="px-6 py-4 text-sm text-right font-medium text-gray-900">{{ venda.total }}</td>
              </tr>
              <tr v-if="!vendas.length">
                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-400">Nenhuma venda registrada.</td>
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
  usuario: { type: Object, required: true },
  vendas:  { type: Array,  default: () => [] },
});

const iniciais   = (nome) => nome.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
const nomeRole   = (r) => ({ admin: 'Administrador', editor: 'Editor', usuario: 'Usuário' }[r] ?? r);
const badgeRole  = (r) => ({ admin: 'roxo', editor: 'azul', usuario: 'cinza' }[r] ?? 'cinza');
const corStatus  = (s) => ({ concluido: 'verde', pendente: 'amarelo', processando: 'azul', cancelado: 'vermelho' }[s] ?? 'cinza');
</script>
