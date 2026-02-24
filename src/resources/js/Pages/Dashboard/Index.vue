<template>
  <AppLayout page-title="Dashboard">
    <Head title="Dashboard" />

    <!-- Header row -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Painel de Controle</h2>
        <p class="text-sm text-gray-400 mt-0.5">Visão geral do negócio · {{ today }}</p>
      </div>
      <button class="flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-xl transition shadow-sm shadow-violet-200">
        <ArrowDownTrayIcon class="w-4 h-4" />
        <span class="hidden sm:inline">Exportar</span>
      </button>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div
        v-for="stat in statsCards"
        :key="stat.label"
        class="bg-white rounded-2xl p-4 shadow-sm border border-gray-50"
        :style="`background: linear-gradient(135deg, ${stat.bgFrom}, ${stat.bgTo})`"
      >
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center" :style="`background: ${stat.iconBg}`">
            <component :is="stat.icon" class="w-5 h-5" :style="`color: ${stat.iconColor}`" />
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 mb-0.5">{{ stat.value }}</p>
        <p class="text-sm text-gray-500 mb-1">{{ stat.label }}</p>
        <p class="text-xs font-medium" :class="stat.trend > 0 ? 'text-green-500' : 'text-red-500'">
          {{ stat.trend > 0 ? '+' : '' }}{{ stat.trend }}% em relação a ontem
        </p>
      </div>
    </div>

    <!-- Charts row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
      <!-- Revenue chart (2/3 width) -->
      <div class="lg:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="font-bold text-gray-800">Receita Total</h3>
            <p class="text-xs text-gray-400 mt-0.5">Últimos 7 dias</p>
          </div>
          <div class="flex gap-3 text-xs">
            <span class="flex items-center gap-1.5">
              <span class="w-3 h-1 bg-violet-500 rounded-full inline-block"></span>
              Online
            </span>
            <span class="flex items-center gap-1.5">
              <span class="w-3 h-1 bg-green-400 rounded-full inline-block"></span>
              Offline
            </span>
          </div>
        </div>
        <apexchart
          type="bar"
          height="220"
          :options="barChartOptions"
          :series="barChartSeries"
        />
      </div>

      <!-- Visitor insight chart -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
        <div class="mb-4">
          <h3 class="font-bold text-gray-800">Insights de Visitantes</h3>
        </div>
        <apexchart
          type="line"
          height="220"
          :options="lineChartOptions"
          :series="lineChartSeries"
        />
        <div class="flex items-center justify-center gap-4 mt-3 text-xs">
          <span class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 bg-red-400 rounded-full inline-block"></span>
            Fidelizados
          </span>
          <span class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 bg-green-400 rounded-full inline-block"></span>
            Novos
          </span>
          <span class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 bg-violet-400 rounded-full inline-block"></span>
            Únicos
          </span>
        </div>
      </div>
    </div>

    <!-- Bottom row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <!-- Top products -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-bold text-gray-800">Top Produtos</h3>
          <Link :href="route('products.index')" class="text-xs text-violet-600 hover:underline font-medium">
            Ver todos
          </Link>
        </div>
        <div class="space-y-3">
          <div class="grid grid-cols-[1.5rem_1fr_auto] items-center gap-2 text-xs font-semibold text-gray-400 pb-2 border-b border-gray-50">
            <span>#</span>
            <span>Nome</span>
            <span>Vendas</span>
          </div>
          <div
            v-for="(product, idx) in topProducts"
            :key="product.id"
            class="grid grid-cols-[1.5rem_1fr_auto] items-center gap-2"
          >
            <span class="text-xs font-bold text-gray-400">{{ String(idx + 1).padStart(2, '0') }}</span>
            <div>
              <p class="text-sm font-medium text-gray-700 truncate">{{ product.name }}</p>
              <div class="mt-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div
                  class="h-full rounded-full transition-all"
                  :style="`width: ${getPopularityPercent(product, idx)}%; background: ${popularityColors[idx % popularityColors.length]}`"
                ></div>
              </div>
            </div>
            <span
              class="text-xs font-bold px-2 py-0.5 rounded-lg"
              :style="`background: ${popularityColors[idx % popularityColors.length]}20; color: ${popularityColors[idx % popularityColors.length]}`"
            >
              {{ getPopularityPercent(product, idx) }}%
            </span>
          </div>
        </div>
      </div>

      <!-- Customer satisfaction -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
        <h3 class="font-bold text-gray-800 mb-1">Satisfação de Clientes</h3>
        <apexchart
          type="area"
          height="180"
          :options="satisfactionChartOptions"
          :series="satisfactionChartSeries"
        />
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-50">
          <div class="text-center">
            <p class="text-lg font-bold text-gray-800">{{ formatCurrency(stats.lastMonthRevenue) }}</p>
            <p class="text-xs text-gray-400 flex items-center gap-1 justify-center mt-0.5">
              <span class="w-3 h-0.5 bg-violet-400 inline-block"></span>
              Mês passado
            </p>
          </div>
          <div class="text-center">
            <p class="text-lg font-bold text-gray-800">{{ formatCurrency(stats.thisMonthRevenue) }}</p>
            <p class="text-xs text-gray-400 flex items-center gap-1 justify-center mt-0.5">
              <span class="w-3 h-0.5 bg-green-400 inline-block"></span>
              Este mês
            </p>
          </div>
        </div>
      </div>

      <!-- Target vs Reality -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
        <h3 class="font-bold text-gray-800 mb-1">Meta vs Realidade</h3>
        <apexchart
          type="bar"
          height="180"
          :options="targetChartOptions"
          :series="targetChartSeries"
        />
        <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-gray-50">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
              <ChartBarIcon class="w-4 h-4 text-violet-600" />
            </div>
            <div>
              <p class="text-xs text-gray-400">Vendas Reais</p>
              <p class="text-sm font-bold text-gray-800">{{ totalOrders.toLocaleString('pt-BR') }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
              <TrophyIcon class="w-4 h-4 text-yellow-600" />
            </div>
            <div>
              <p class="text-xs text-gray-400">Meta</p>
              <p class="text-sm font-bold text-gray-800">{{ Math.round(totalOrders * 1.3).toLocaleString('pt-BR') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Map and Volume row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4 mb-6">
      <!-- Sales Mapping by Country -->
      <div class="lg:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-gray-50 flex flex-col">
        <h3 class="font-bold text-gray-800 mb-4">Vendas por País</h3>
        <div class="flex-1 bg-gray-50 rounded-xl flex items-center justify-center relative overflow-hidden min-h-[250px]">
           <!-- Simple SVG World Map Placeholder -->
           <svg class="w-full h-full text-gray-200" viewBox="0 0 100 50" preserveAspectRatio="none">
             <path d="M10,10 Q20,5 30,10 T50,15 T70,10 T90,15 V35 Q80,40 70,35 T50,30 T30,35 T10,30 Z" fill="currentColor" />
           </svg>
           <div class="absolute inset-0 flex items-center justify-center">
             <p class="text-gray-400 text-sm">Mapa Interativo (Simulado)</p>
           </div>
           <!-- Floating bubbles for countries -->
           <div class="absolute top-1/4 left-1/4 w-8 h-8 bg-violet-500/20 rounded-full flex items-center justify-center animate-pulse">
             <div class="w-3 h-3 bg-violet-500 rounded-full"></div>
           </div>
           <div class="absolute top-1/3 right-1/3 w-6 h-6 bg-green-500/20 rounded-full flex items-center justify-center animate-pulse animation-delay-500">
             <div class="w-2 h-2 bg-green-500 rounded-full"></div>
           </div>
        </div>
      </div>

      <!-- Volume vs Service Level -->
      <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-50">
        <h3 class="font-bold text-gray-800 mb-1">Volume vs Nível de Serviço</h3>
        <apexchart
          type="bar"
          height="250"
          :options="volumeChartOptions"
          :series="volumeChartSeries"
        />
      </div>
    </div>

    <!-- Recent sales table -->
    <div class="mt-4 bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
      <div class="flex items-center justify-between p-5 border-b border-gray-50">
        <h3 class="font-bold text-gray-800">Vendas Recentes</h3>
        <Link :href="route('sales.index')" class="text-xs text-violet-600 hover:underline font-medium">
          Ver todas
        </Link>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 text-gray-400 text-xs font-semibold uppercase tracking-wide">
              <th class="text-left px-5 py-3">ID</th>
              <th class="text-left px-5 py-3">Cliente</th>
              <th class="text-left px-5 py-3">Valor</th>
              <th class="text-left px-5 py-3">Status</th>
              <th class="text-left px-5 py-3">Data</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="sale in recentSales"
              :key="sale.id"
              class="hover:bg-gray-50 transition"
            >
              <td class="px-5 py-3 text-gray-400 text-xs font-medium">#{{ sale.id }}</td>
              <td class="px-5 py-3 font-medium text-gray-700">{{ sale.customer?.name || 'Consumidor Final' }}</td>
              <td class="px-5 py-3 font-semibold text-gray-800">{{ formatCurrency(sale.total) }}</td>
              <td class="px-5 py-3">
                <span
                  class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                  :class="{
                    'bg-green-100 text-green-700': sale.status === 'completed',
                    'bg-yellow-100 text-yellow-700': sale.status === 'pending',
                    'bg-red-100 text-red-700': sale.status === 'cancelled',
                  }"
                >
                  {{ statusLabel[sale.status] }}
                </span>
              </td>
              <td class="px-5 py-3 text-gray-400 text-xs">{{ formatDate(sale.created_at) }}</td>
            </tr>
            <tr v-if="!recentSales.length">
              <td colspan="5" class="px-5 py-8 text-center text-gray-400 text-sm">Nenhuma venda registrada</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Low stock alert -->
    <div
      v-if="stats.lowStockCount > 0"
      class="mt-4 flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl"
    >
      <ExclamationTriangleIcon class="w-5 h-5 text-amber-500 flex-shrink-0" />
      <p class="text-sm text-amber-700">
        <strong>{{ stats.lowStockCount }} produto(s)</strong> com estoque abaixo do mínimo.
        <Link :href="route('products.index')" class="underline ml-1">Ver produtos</Link>
      </p>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Pages/Layouts/AppLayout.vue';
import VueApexCharts from 'vue-apexcharts';
import {
  CurrencyDollarIcon,
  ShoppingCartIcon,
  CubeIcon,
  UserGroupIcon,
  ArrowDownTrayIcon,
  ChartBarIcon,
  TrophyIcon,
  ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline';

// ApexCharts alias
const apexchart = VueApexCharts;

const props = defineProps({
  stats:          { type: Object, default: () => ({}) },
  revenuePerDay:  { type: Object, default: () => ({}) },
  revenuePerMonth:{ type: Object, default: () => ({}) },
  topProducts:    { type: Array,  default: () => [] },
  recentSales:    { type: Array,  default: () => [] },
});

const today = new Date().toLocaleDateString('pt-BR', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' });

const statusLabel = { completed: 'Concluída', pending: 'Pendente', cancelled: 'Cancelada' };
const popularityColors = ['#7C3AED', '#10B981', '#6366F1', '#F59E0B'];

const totalOrders = computed(() => props.stats.totalOrders || 0);

function formatCurrency(value) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' });
}

function getPopularityPercent(product, idx) {
  const maxSold = Math.max(...props.topProducts.map(p => p.total_sold || 0), 1);
  const percent = Math.round(((product.total_sold || 0) / maxSold) * 100);
  return Math.max(percent, 5 + (4 - idx) * 5);
}

// Stats cards
const statsCards = computed(() => [
  {
    label: 'Total de Vendas',
    value: formatCurrency(props.stats.totalRevenue),
    trend: 8,
    icon: CurrencyDollarIcon,
    bgFrom: '#FFF7ED', bgTo: '#FFFBF5',
    iconBg: '#FED7AA', iconColor: '#C2410C',
  },
  {
    label: 'Total de Pedidos',
    value: props.stats.totalOrders?.toLocaleString('pt-BR') || '0',
    trend: 5,
    icon: ShoppingCartIcon,
    bgFrom: '#FFF0F0', bgTo: '#FFFBF5',
    iconBg: '#FECACA', iconColor: '#DC2626',
  },
  {
    label: 'Produtos Ativos',
    value: props.stats.totalProducts?.toLocaleString('pt-BR') || '0',
    trend: 2,
    icon: CubeIcon,
    bgFrom: '#F5F3FF', bgTo: '#FEFEFF',
    iconBg: '#DDD6FE', iconColor: '#7C3AED',
  },
  {
    label: 'Novos Clientes',
    value: props.stats.totalCustomers?.toLocaleString('pt-BR') || '0',
    trend: 6,
    icon: UserGroupIcon,
    bgFrom: '#F0FDF4', bgTo: '#FEFFFE',
    iconBg: '#BBF7D0', iconColor: '#16A34A',
  },
]);

// Bar chart
const dayLabels = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
const revenueValues = computed(() => {
  const vals = Object.values(props.revenuePerDay);
  while (vals.length < 7) vals.unshift(0);
  return vals.slice(-7).map(Number);
});

const barChartOptions = {
  chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter' },
  plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
  colors: ['#7C3AED', '#10B981'],
  dataLabels: { enabled: false },
  xaxis: { categories: dayLabels, axisBorder: { show: false }, axisTicks: { show: false } },
  yaxis: { labels: { formatter: v => `R$${Math.round(v / 1000)}k` } },
  grid: { borderColor: '#F3F4F6', strokeDashArray: 4 },
  legend: { show: false },
  tooltip: {
    y: { formatter: v => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v) },
  },
};

const barChartSeries = computed(() => [
  { name: 'Online', data: revenueValues.value },
  { name: 'Offline', data: revenueValues.value.map(v => Math.round(v * 0.4)) },
]);

// Line chart (visitor insight)
const months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
const lineChartOptions = {
  chart: { type: 'line', toolbar: { show: false }, fontFamily: 'Inter' },
  stroke: { curve: 'smooth', width: 2.5 },
  colors: ['#EF4444', '#10B981', '#7C3AED'],
  dataLabels: { enabled: false },
  xaxis: { categories: months, axisBorder: { show: false }, axisTicks: { show: false } },
  yaxis: { labels: { show: false } },
  grid: { borderColor: '#F3F4F6', strokeDashArray: 4 },
  legend: { show: false },
  tooltip: { x: { show: true } },
};

const lineChartSeries = [
  { name: 'Fidelizados', data: [300, 280, 320, 290, 340, 360, 380, 400, 350, 320, 290, 310] },
  { name: 'Novos',       data: [200, 220, 280, 250, 300, 320, 290, 350, 280, 260, 240, 270] },
  { name: 'Únicos',      data: [150, 180, 200, 170, 220, 240, 260, 280, 220, 200, 190, 210] },
];

// Satisfaction area chart
const satisfactionChartOptions = {
  chart: { type: 'area', toolbar: { show: false }, fontFamily: 'Inter', sparkline: { enabled: false } },
  stroke: { curve: 'smooth', width: 2 },
  colors: ['#A78BFA', '#6EE7B7'],
  fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.0 } },
  dataLabels: { enabled: false },
  xaxis: { categories: ['Jan','Fev','Mar','Abr','Mai','Jun'], axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '11px' } } },
  yaxis: { labels: { show: false } },
  grid: { borderColor: '#F9FAFB', strokeDashArray: 3 },
  legend: { show: false },
};

const satisfactionChartSeries = [
  { name: 'Mês passado', data: [31, 40, 28, 51, 42, 45] },
  { name: 'Este mês',    data: [11, 32, 45, 32, 34, 52] },
];

// Target vs Reality bar chart
const targetChartOptions = {
  chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'Inter' },
  plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
  colors: ['#7C3AED', '#FCD34D'],
  dataLabels: { enabled: false },
  xaxis: { categories: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul'], axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '10px' } } },
  yaxis: { labels: { show: false } },
  grid: { borderColor: '#F9FAFB', strokeDashArray: 3 },
  legend: { show: false },
};

const targetChartSeries = computed(() => [
  { name: 'Realidade', data: Object.values(props.revenuePerMonth).slice(0, 7).map(Number) },
  { name: 'Meta',      data: Object.values(props.revenuePerMonth).slice(0, 7).map(v => Number(v) * 1.3) },
]);

// Volume vs Service Level Chart
const volumeChartOptions = {
  chart: { type: 'bar', stacked: true, toolbar: { show: false }, fontFamily: 'Inter' },
  plotOptions: { bar: { borderRadius: 4, columnWidth: '30%' } },
  colors: ['#7C3AED', '#E5E7EB'],
  dataLabels: { enabled: false },
  xaxis: { categories: ['Jan','Fev','Mar','Abr','Mai','Jun'], axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '10px' } } },
  yaxis: { show: false },
  grid: { show: false },
  legend: { show: true, position: 'bottom', markers: { radius: 12 } },
};

const volumeChartSeries = [
  { name: 'Volume', data: [110, 120, 130, 100, 90, 150] },
  { name: 'Serviço', data: [50, 40, 30, 60, 70, 10] },
];
</script>
