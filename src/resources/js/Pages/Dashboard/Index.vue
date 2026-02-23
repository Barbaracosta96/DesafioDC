<template>
  <AppLayout titulo="Dashboard">
    <Head title="Dashboard" />

    <!-- Linha 1: Vendas de Hoje (4 cards) + Visitor Insights -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

      <!-- Bloco: Today Sales -->
      <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="font-bold text-gray-900 text-base">Operações de Hoje</h3>
            <p class="text-xs text-gray-400">Resumo Operacional</p>
          </div>
          <a :href="route('vendas.exportar')" class="flex items-center gap-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg px-3 py-1.5 hover:bg-gray-50 hover:border-indigo-300 hover:text-indigo-600 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Exportar
          </a>
        </div>

        <!-- 4 mini-cards coloridos -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
          <!-- Card 1: Total de Vendas -->
          <div class="rounded-2xl p-4 flex flex-col gap-2 bg-rose-100">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-rose-500 shrink-0">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <p class="text-xl font-bold text-gray-900 leading-tight">{{ estatisticas.receita_mensal || 'R$ 1k' }}</p>
              <p class="text-xs text-gray-500 mt-0.5">Valor Total Operacional</p>
            </div>
            <p class="text-xs font-medium text-emerald-600">+8% <span class="text-gray-400 font-normal">em relacao a ontem</span></p>
          </div>
          <!-- Card 2: Total de Pedidos -->
          <div class="rounded-2xl p-4 flex flex-col gap-2 bg-amber-50">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-amber-400 shrink-0">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <div>
              <p class="text-xl font-bold text-gray-900 leading-tight">{{ estatisticas.vendas_mes ?? 0 }}</p>
              <p class="text-xs text-gray-500 mt-0.5">Total de Ordens</p>
            </div>
            <p class="text-xs font-medium text-emerald-600">+5% <span class="text-gray-400 font-normal">em relacao a ontem</span></p>
          </div>
          <!-- Card 3: Produtos em Estoque -->
          <div class="rounded-2xl p-4 flex flex-col gap-2 bg-emerald-50">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-500 shrink-0">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
            </div>
            <div>
              <p class="text-xl font-bold text-gray-900 leading-tight">{{ estatisticas.total_produtos ?? 0 }}</p>
              <p class="text-xs text-gray-500 mt-0.5">Ativos em Inventário</p>
            </div>
            <p class="text-xs font-medium text-emerald-600">+1,2% <span class="text-gray-400 font-normal">em relacao a ontem</span></p>
          </div>
          <!-- Card 4: Total de Clientes -->
          <div class="rounded-2xl p-4 flex flex-col gap-2 bg-purple-50">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-purple-500 shrink-0">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <div>
              <p class="text-xl font-bold text-gray-900 leading-tight">{{ estatisticas.total_clientes ?? 0 }}</p>
              <p class="text-xs text-gray-500 mt-0.5">Total de Entidades</p>
            </div>
            <p class="text-xs font-medium text-emerald-600">+0,5% <span class="text-gray-400 font-normal">em relacao a ontem</span></p>
          </div>
        </div>
      </div>

      <!-- Grafico: Visitor Insights -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-900 text-base mb-4">Relatorio de Visitas</h3>
        <div class="relative h-36 mb-2">
          <svg viewBox="0 0 280 100" class="w-full h-full" preserveAspectRatio="none">
            <line x1="0" y1="25" x2="280" y2="25" stroke="#f3f4f6" stroke-width="1"/>
            <line x1="0" y1="50" x2="280" y2="50" stroke="#f3f4f6" stroke-width="1"/>
            <line x1="0" y1="75" x2="280" y2="75" stroke="#f3f4f6" stroke-width="1"/>
            <polyline fill="none" stroke="#8b5cf6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
              points="0,70 35,40 70,55 105,20 140,45 175,30 210,60 245,15 280,40"/>
            <polyline fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
              points="0,55 35,65 70,40 105,60 140,30 175,55 210,35 245,50 280,25"/>
            <polyline fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
              points="0,80 35,70 70,80 105,65 140,75 175,60 210,70 245,65 280,55"/>
          </svg>
        </div>
        <div class="flex justify-between text-xs text-gray-400 mb-3 px-1">
          <span>Jan</span><span>Fev</span><span>Mar</span><span>Abr</span><span>Mai</span>
          <span>Jun</span><span>Jul</span><span>Ago</span><span>Set</span>
        </div>
        <div class="flex gap-4 justify-center text-xs text-gray-500">
          <div class="flex items-center gap-1.5"><span class="w-3 h-0.5 rounded bg-purple-500 inline-block"></span>Fieis</div>
          <div class="flex items-center gap-1.5"><span class="w-3 h-0.5 rounded bg-emerald-500 inline-block"></span>Novos</div>
          <div class="flex items-center gap-1.5"><span class="w-3 h-0.5 rounded bg-amber-400 inline-block"></span>Unicos</div>
        </div>
      </div>
    </div>

    <!-- Linha 2: Receita Total + Satisfacao Cliente + Meta vs Realidade -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

      <!-- Receita Total -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-900 text-base mb-4">Valores Operacionais</h3>
        <div class="relative">
          <div class="flex">
            <div class="flex flex-col justify-between text-xs text-gray-400 mr-2 h-40">
              <span>25k</span><span>20k</span><span>15k</span><span>10k</span><span>5k</span><span>0k</span>
            </div>
            <div class="flex-1 flex items-end gap-1" style="height:160px">
              <div v-for="(item, i) in dadosReceitaSemanal" :key="i" class="flex-1 flex gap-0.5 items-end" style="height:100%">
                <div :style="{ height: (item.online * 1.6) + 'px' }" class="flex-1 rounded-t-sm bg-indigo-600" />
                <div :style="{ height: (item.offline * 1.6) + 'px' }" class="flex-1 rounded-t-sm bg-emerald-400" />
              </div>
            </div>
          </div>
          <div class="flex ml-8 mt-1">
            <div v-for="(item, i) in dadosReceitaSemanal" :key="'d'+i" class="flex-1 text-center text-xs text-gray-400">{{ item.dia }}</div>
          </div>
        </div>
        <div class="flex gap-4 mt-3 text-xs text-gray-500">
          <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600 inline-block"></span>Ordens Digitais</div>
          <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block"></span>Ordens Presenciais</div>
        </div>
      </div>

      <!-- Satisfacao do Cliente -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-900 text-base mb-1">Desempenho Operacional</h3>
        <div class="relative h-36 mb-2">
          <svg viewBox="0 0 220 100" class="w-full h-full" preserveAspectRatio="none">
            <defs>
              <linearGradient id="gradGreen" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#10b981" stop-opacity="0.3"/>
                <stop offset="100%" stop-color="#10b981" stop-opacity="0.02"/>
              </linearGradient>
              <linearGradient id="gradBlue" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#6366f1" stop-opacity="0.25"/>
                <stop offset="100%" stop-color="#6366f1" stop-opacity="0.02"/>
              </linearGradient>
            </defs>
            <path d="M0,80 C30,60 60,75 90,50 C120,30 150,65 180,45 C200,35 210,50 220,40 L220,100 L0,100 Z" fill="url(#gradGreen)" />
            <path d="M0,80 C30,60 60,75 90,50 C120,30 150,65 180,45 C200,35 210,50 220,40" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
            <path d="M0,65 C30,45 60,60 90,35 C120,15 150,50 180,28 C200,18 210,35 220,22 L220,100 L0,100 Z" fill="url(#gradBlue)" />
            <path d="M0,65 C30,45 60,60 90,35 C120,15 150,50 180,28 C200,18 210,35 220,22" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="flex justify-between items-center text-xs text-gray-400 px-1">
          <span>Jan</span><span>Fev</span><span>Mar</span><span>Abr</span><span>Mai</span><span>Jun</span>
        </div>
        <div class="flex gap-6 mt-3 text-xs text-gray-500">
          <div>
            <div class="flex items-center gap-1 mb-0.5"><span class="w-2.5 h-0.5 rounded bg-emerald-500 inline-block"></span>Mes Passado</div>
            <p class="font-bold text-gray-800 text-sm">{{ estatisticas.receita_mes_passado || 'R$ 0,00' }}</p>
          </div>
          <div>
            <div class="flex items-center gap-1 mb-0.5"><span class="w-2.5 h-0.5 rounded bg-indigo-500 inline-block"></span>Este Mes</div>
            <p class="font-bold text-gray-800 text-sm">{{ estatisticas.receita_mensal || 'R$ 0,00' }}</p>
          </div>
        </div>
      </div>

      <!-- Meta vs Realidade -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-900 text-base mb-4">Meta vs Realidade</h3>
        <div class="relative" style="height:144px">
          <div class="flex items-end gap-2" style="height:100%">
            <div v-for="(item, i) in metaVsRealidade" :key="i" class="flex-1 flex gap-0.5 items-end" style="height:100%">
              <div :style="{ height: (item.realidade * 1.44) + 'px' }" class="flex-1 rounded-t-md bg-emerald-400" />
              <div :style="{ height: (item.meta * 1.44) + 'px' }" class="flex-1 rounded-t-md bg-amber-400" />
            </div>
          </div>
        </div>
        <div class="flex justify-between mt-1 px-0.5">
          <span v-for="(item, i) in metaVsRealidade" :key="'m'+i" class="flex-1 text-center text-xs text-gray-400">{{ item.mes }}</span>
        </div>
        <div class="flex gap-4 mt-3 text-xs">
          <div class="flex items-center gap-1.5 text-gray-500">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block"></span>
            Realidade <span class="font-bold text-gray-900 ml-1">{{ estatisticas.meta_realidade ?? '0,00' }}</span>
          </div>
          <div class="flex items-center gap-1.5 text-gray-500">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>
            Meta <span class="font-bold text-gray-900 ml-1">{{ estatisticas.meta_alvo ?? '0,00' }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Linha 3: Pedidos Recentes + Top Produtos + Volume vs Servico -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

      <!-- Pedidos Recentes -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
          <h3 class="font-bold text-gray-900 text-base">Ordens Recentes</h3>
          <Link :href="route('vendas.index')" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Ver todos</Link>
        </div>
        <div class="overflow-x-auto max-h-72 overflow-y-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="text-left text-xs font-semibold text-gray-500 px-5 py-3">N</th>
                <th class="text-left text-xs font-semibold text-gray-500 px-3 py-3">Ativo</th>
                <th class="text-left text-xs font-semibold text-gray-500 px-3 py-3">Preco</th>
                <th class="text-left text-xs font-semibold text-gray-500 px-3 py-3">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="venda in vendasRecentes" :key="venda.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3 text-xs font-medium text-gray-700">{{ venda.numero_pedido }}</td>
                <td class="px-3 py-3 text-xs text-gray-600 max-w-24 truncate">{{ venda.cliente }}</td>
                <td class="px-3 py-3 text-xs text-gray-600 whitespace-nowrap">{{ venda.total }}</td>
                <td class="px-3 py-3">
                  <Badge :variant="badgeStatus(venda.status)">{{ venda.status_label }}</Badge>
                </td>
              </tr>
              <tr v-if="!vendasRecentes.length">
                <td colspan="4" class="px-5 py-8 text-center text-xs text-gray-400">Nenhuma ordem registrada ainda.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top Produtos com barra de popularidade -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
          <h3 class="font-bold text-gray-900 text-base">Ativos em Destaque</h3>
          <Link :href="route('estoque.index')" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Ver todos</Link>
        </div>
        <div class="px-5 py-3">
          <div class="grid grid-cols-12 text-xs font-semibold text-gray-400 mb-2 px-1">
            <span class="col-span-1">#</span>
            <span class="col-span-4">Nome</span>
            <span class="col-span-4">Popularidade</span>
            <span class="col-span-3 text-right">Vendas</span>
          </div>
          <div class="space-y-3">
            <div v-for="(produto, i) in topProdutosExibicao" :key="produto.id ?? i" class="grid grid-cols-12 items-center text-xs">
              <span class="col-span-1 text-gray-400 font-medium">{{ String(i + 1).padStart(2, '0') }}</span>
              <span class="col-span-4 text-gray-700 font-medium truncate pr-2">{{ produto.nome }}</span>
              <div class="col-span-4 pr-2">
                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                  <div
                    :style="{ width: produto.popularidade + '%' }"
                    :class="['h-full rounded-full', coresBarraProduto[i % coresBarraProduto.length]]"
                  />
                </div>
              </div>
              <div class="col-span-3 text-right">
                <span :class="['px-2 py-0.5 rounded-md text-xs font-semibold', badgesCorProduto[i % badgesCorProduto.length]]">{{ produto.popularidade }}%</span>
              </div>
            </div>
            <div v-if="!topProdutos.length" class="py-6 text-center text-xs text-gray-400">Nenhum ativo registrado ainda.</div>
          </div>
        </div>
      </div>

      <!-- Volume vs Nivel de Servico -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="font-bold text-gray-900 text-base mb-4">Volume vs Eficiência Operacional</h3>
        <div class="relative mb-3" style="height:160px">
          <div class="flex items-end gap-2" style="height:100%">
            <div v-for="(item, i) in volumeServico" :key="i" class="flex-1 flex gap-0.5 items-end" style="height:100%">
              <div :style="{ height: (item.volume * 1.6) + 'px' }" class="flex-1 rounded-t-md bg-indigo-500" />
              <div :style="{ height: (item.servico * 1.6) + 'px' }" class="flex-1 rounded-t-md bg-emerald-400" />
            </div>
          </div>
        </div>
        <div class="flex gap-5 items-center text-xs text-gray-500">
          <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 inline-block"></span>
            Volume <span class="font-bold text-gray-900 ml-1">{{ estatisticas.volume_total ?? '0' }}</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block"></span>
            Servicos <span class="font-bold text-gray-900 ml-1">{{ estatisticas.servicos_total ?? '0' }}</span>
          </div>
        </div>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
  estatisticas:  { type: Object, required: true },
  vendasRecentes:{ type: Array,  default: () => [] },
  topProdutos:   { type: Array,  default: () => [] },
  vendasPorMes:  { type: Array,  default: () => [] },
  analiseVendas: { type: Array,  default: () => [] },
});

const dadosReceitaSemanal = [
  { dia: 'Seg', online: 60, offline: 40 },
  { dia: 'Ter', online: 80, offline: 55 },
  { dia: 'Qua', online: 70, offline: 45 },
  { dia: 'Qui', online: 90, offline: 65 },
  { dia: 'Sex', online: 75, offline: 50 },
  { dia: 'Sab', online: 85, offline: 60 },
  { dia: 'Dom', online: 95, offline: 70 },
];

const metaVsRealidade = [
  { mes: 'Jan', realidade: 55, meta: 70 },
  { mes: 'Fev', realidade: 65, meta: 80 },
  { mes: 'Mar', realidade: 45, meta: 60 },
  { mes: 'Abr', realidade: 75, meta: 85 },
  { mes: 'Mai', realidade: 60, meta: 75 },
  { mes: 'Jun', realidade: 80, meta: 90 },
  { mes: 'Jul', realidade: 70, meta: 80 },
];

const volumeServico = [
  { volume: 80, servico: 55 },
  { volume: 65, servico: 45 },
  { volume: 90, servico: 60 },
  { volume: 70, servico: 50 },
  { volume: 85, servico: 65 },
  { volume: 75, servico: 40 },
];

const coresBarraProduto = ['bg-indigo-500', 'bg-emerald-500', 'bg-purple-500', 'bg-amber-400'];
const badgesCorProduto  = [
  'bg-indigo-100 text-indigo-700',
  'bg-emerald-100 text-emerald-700',
  'bg-purple-100 text-purple-700',
  'bg-amber-100 text-amber-700',
];

const maxVendas = computed(() => Math.max(...props.topProdutos.map(p => Number(p.vendas) || 0), 1));
const topProdutosExibicao = computed(() =>
  props.topProdutos.slice(0, 4).map(p => ({
    ...p,
    popularidade: Math.round((Number(p.vendas) / maxVendas.value) * 100),
  }))
);

const badgeStatus = (status) => (
  { pendente: 'amarelo', processando: 'azul', concluido: 'verde', cancelado: 'vermelho' }[status] ?? 'cinza'
);
</script>
