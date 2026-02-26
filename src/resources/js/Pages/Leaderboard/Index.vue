<template>
  <AppLayout page-title="Leaderboard">
    <div class="space-y-6">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Leaderboard</h1>
          <p class="text-sm text-gray-500 mt-1">Top performers this month</p>
        </div>
        <div class="flex gap-2">
          <button
            v-for="period in periods"
            :key="period.value"
            @click="activePeriod = period.value"
            :class="activePeriod === period.value
              ? 'bg-violet-600 text-white shadow'
              : 'bg-white text-gray-600 hover:bg-violet-50 border border-gray-200'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all"
          >
            {{ period.label }}
          </button>
        </div>
      </div>

      <!-- Top 3 Podium -->
      <div class="grid grid-cols-3 gap-4">
        <!-- 2nd place -->
        <div class="flex flex-col items-center bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mt-6">
          <div class="relative">
            <img :src="topThree[1].avatar" class="w-16 h-16 rounded-full object-cover border-4 border-gray-300" />
            <span class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-gray-400 text-white text-sm font-bold flex items-center justify-center shadow">2</span>
          </div>
          <p class="mt-4 font-semibold text-gray-800 text-center">{{ topThree[1].name }}</p>
          <p class="text-xs text-gray-400">{{ topThree[1].role }}</p>
          <p class="mt-2 text-xl font-bold text-gray-600">{{ topThree[1].sales }}</p>
          <p class="text-xs text-gray-400">sales</p>
          <div class="mt-3 px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">
            {{ topThree[1].revenue }}
          </div>
        </div>

        <!-- 1st place -->
        <div class="flex flex-col items-center bg-gradient-to-b from-violet-600 to-violet-700 rounded-2xl p-6 shadow-lg text-white">
          <div class="relative">
            <div class="absolute -top-3 left-1/2 -translate-x-1/2 text-2xl">👑</div>
            <img :src="topThree[0].avatar" class="w-20 h-20 rounded-full object-cover border-4 border-yellow-400 mt-2" />
            <span class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-yellow-400 text-yellow-900 text-sm font-bold flex items-center justify-center shadow">1</span>
          </div>
          <p class="mt-4 font-bold text-lg text-center">{{ topThree[0].name }}</p>
          <p class="text-xs text-violet-200">{{ topThree[0].role }}</p>
          <p class="mt-2 text-3xl font-bold">{{ topThree[0].sales }}</p>
          <p class="text-xs text-violet-200">sales</p>
          <div class="mt-3 px-3 py-1 rounded-full bg-white/20 text-white text-xs font-medium">
            {{ topThree[0].revenue }}
          </div>
        </div>

        <!-- 3rd place -->
        <div class="flex flex-col items-center bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mt-6">
          <div class="relative">
            <img :src="topThree[2].avatar" class="w-16 h-16 rounded-full object-cover border-4 border-amber-600" />
            <span class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-amber-600 text-white text-sm font-bold flex items-center justify-center shadow">3</span>
          </div>
          <p class="mt-4 font-semibold text-gray-800 text-center">{{ topThree[2].name }}</p>
          <p class="text-xs text-gray-400">{{ topThree[2].role }}</p>
          <p class="mt-2 text-xl font-bold text-gray-600">{{ topThree[2].sales }}</p>
          <p class="text-xs text-gray-400">sales</p>
          <div class="mt-3 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-medium">
            {{ topThree[2].revenue }}
          </div>
        </div>
      </div>

      <!-- Full Rankings Table -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <h2 class="font-semibold text-gray-800">Full Rankings</h2>
          <span class="text-sm text-gray-400">{{ filteredLeaderboard.length }} sellers</span>
        </div>
        <div class="divide-y divide-gray-50">
          <div
            v-for="(seller, idx) in filteredLeaderboard"
            :key="seller.id"
            class="flex items-center gap-4 px-6 py-4 hover:bg-violet-50/40 transition-colors"
          >
            <!-- Rank -->
            <div class="w-8 text-center">
              <span
                :class="idx < 3 ? ['text-violet-600', 'font-bold', 'text-lg'] : ['text-gray-400', 'text-sm']"
              >#{{ idx + 1 }}</span>
            </div>

            <!-- Avatar -->
            <img :src="seller.avatar" class="w-10 h-10 rounded-full object-cover border-2 border-gray-100" />

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-800 truncate">{{ seller.name }}</p>
              <p class="text-xs text-gray-400 truncate">{{ seller.role }}</p>
            </div>

            <!-- Sales count -->
            <div class="text-center hidden sm:block">
              <p class="font-semibold text-gray-700">{{ seller.sales }}</p>
              <p class="text-xs text-gray-400">sales</p>
            </div>

            <!-- Revenue -->
            <div class="text-center hidden md:block">
              <p class="font-semibold text-gray-700">{{ seller.revenue }}</p>
              <p class="text-xs text-gray-400">revenue</p>
            </div>

            <!-- Trend badge -->
            <div>
              <span
                :class="seller.trend > 0
                  ? 'bg-emerald-50 text-emerald-600'
                  : 'bg-red-50 text-red-500'"
                class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium"
              >
                <span v-if="seller.trend > 0">▲</span>
                <span v-else>▼</span>
                {{ Math.abs(seller.trend) }}%
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Pages/Layouts/AppLayout.vue';

const periods = [
  { label: 'This Month', value: 'month' },
  { label: 'This Week', value: 'week' },
  { label: 'Today', value: 'today' },
];
const activePeriod = ref('month');

const allSellers = [
  { id: 1, name: 'Marcus Thompson', role: 'Senior Sales Manager', sales: 247, revenue: '$48,230', trend: 12.4, avatar: 'https://i.pravatar.cc/150?img=56' },
  { id: 2, name: 'Sarah Johnson', role: 'Account Executive', sales: 198, revenue: '$38,910', trend: 8.1, avatar: 'https://i.pravatar.cc/150?img=47' },
  { id: 3, name: 'Michael Chen', role: 'Sales Representative', sales: 176, revenue: '$29,450', trend: -2.3, avatar: 'https://i.pravatar.cc/150?img=11' },
  { id: 4, name: 'Emma Wilson', role: 'Regional Director', sales: 154, revenue: '$31,800', trend: 5.7, avatar: 'https://i.pravatar.cc/150?img=5' },
  { id: 5, name: 'David Park', role: 'Sales Specialist', sales: 143, revenue: '$24,690', trend: 3.2, avatar: 'https://i.pravatar.cc/150?img=12' },
  { id: 6, name: 'Lisa Torres', role: 'Account Manager', sales: 129, revenue: '$21,340', trend: -1.8, avatar: 'https://i.pravatar.cc/150?img=9' },
  { id: 7, name: 'James Smith', role: 'Inside Sales Rep', sales: 118, revenue: '$19,200', trend: 7.4, avatar: 'https://i.pravatar.cc/150?img=15' },
  { id: 8, name: 'Olivia Brown', role: 'Sr. Account Executive', sales: 104, revenue: '$17,650', trend: 4.0, avatar: 'https://i.pravatar.cc/150?img=25' },
  { id: 9, name: 'Nathan Davis', role: 'Business Dev Rep', sales: 97, revenue: '$15,110', trend: -4.5, avatar: 'https://i.pravatar.cc/150?img=33' },
  { id: 10, name: 'Chloe Martin', role: 'Sales Coordinator', sales: 89, revenue: '$13,780', trend: 9.2, avatar: 'https://i.pravatar.cc/150?img=29' },
];

const topThree = computed(() => allSellers.slice(0, 3));
const filteredLeaderboard = computed(() => allSellers);
</script>
