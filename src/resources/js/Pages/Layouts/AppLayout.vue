<template>
  <div class="min-h-screen flex bg-[#f8f8fa]">
    <!-- Sidebar -->
    <aside
      class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-white border-r border-gray-100 shadow-sm transition-transform duration-300"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    >
      <!-- Logo -->
      <div class="flex items-center gap-3 h-16 px-6 border-b border-gray-100">
        <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-violet-600">
          <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z" />
          </svg>
        </div>
        <span class="text-lg font-bold text-gray-800 tracking-wide">Dabang</span>
      </div>

      <!-- Nav -->
      <nav class="flex-1 py-4 overflow-y-auto">
        <ul class="space-y-0.5 px-3">
          <li v-for="item in navItems" :key="item.label">
            <Link
              :href="item.route === '#' ? '#' : route(item.route)"
              :method="item.method || 'get'"
              as="button"
              class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 w-full text-left"
              :class="isActive(item)
                ? 'bg-violet-600 text-white shadow-sm shadow-violet-200'
                : 'text-gray-500 hover:bg-violet-50 hover:text-violet-700'"
            >
              <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
              <span>{{ item.label }}</span>
            </Link>
          </li>
        </ul>

        <!-- Admin section -->
        <div v-if="$page.props.auth.user?.roles?.includes('admin')" class="mt-4 px-3">
          <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Admin</p>
          <ul class="space-y-0.5">
            <li>
              <Link
                :href="route('users.index')"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150"
                :class="isActive('users.index')
                  ? 'bg-violet-600 text-white shadow-sm shadow-violet-200'
                  : 'text-gray-500 hover:bg-violet-50 hover:text-violet-700'"
              >
                <UsersIcon class="w-5 h-5 flex-shrink-0" />
                <span>Usuários</span>
              </Link>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Pro Banner -->
      <div class="mx-3 mb-4 p-4 bg-violet-600 rounded-2xl text-white">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-7 h-7 bg-white/20 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z" />
            </svg>
          </div>
          <span class="font-bold text-sm">Dabang Pro</span>
        </div>
        <p class="text-xs text-violet-200 mb-3">Acesse todos os recursos no Retumbas</p>
        <button class="w-full py-1.5 bg-white text-violet-700 text-xs font-bold rounded-lg hover:bg-violet-50 transition">
          Get Pro
        </button>
      </div>

      <!-- User Info -->
      <div class="p-4 border-t border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-full bg-violet-100 flex items-center justify-center flex-shrink-0">
            <span class="text-sm font-bold text-violet-700">{{ userInitial }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-800 truncate">{{ $page.props.auth.user?.name }}</p>
            <p class="text-xs text-gray-400 capitalize">{{ $page.props.auth.user?.roles?.[0] }}</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Mobile overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-40 bg-black/30 md:hidden"
      @click="sidebarOpen = false"
    />

    <!-- Main content -->
    <div class="flex-1 flex flex-col md:ml-64">
      <!-- Top bar -->
      <header class="sticky top-0 z-30 flex items-center justify-between h-16 px-6 bg-white border-b border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
          <!-- Mobile menu toggle -->
          <button class="md:hidden p-2 rounded-lg hover:bg-gray-100" @click="sidebarOpen = !sidebarOpen">
            <Bars3Icon class="w-5 h-5 text-gray-500" />
          </button>
          <h1 class="text-lg font-bold text-gray-800">{{ pageTitle }}</h1>
        </div>

        <div class="flex items-center gap-3">
          <!-- Search -->
          <div class="hidden md:flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
            <MagnifyingGlassIcon class="w-4 h-4 text-gray-400" />
            <input
              type="text"
              placeholder="Buscar aqui..."
              class="bg-transparent text-sm text-gray-600 outline-none w-40"
            />
          </div>

          <!-- Language -->
          <div class="hidden md:flex items-center gap-1 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600">
            <span>🇧🇷</span>
            <span>PT (BR)</span>
            <ChevronDownIcon class="w-3 h-3" />
          </div>

          <!-- Notifications -->
          <button class="relative p-2 rounded-xl hover:bg-gray-100 transition">
            <BellIcon class="w-5 h-5 text-gray-500" />
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-violet-500 rounded-full"></span>
          </button>

          <!-- User avatar + dropdown -->
          <div class="relative" ref="userMenuRef">
            <button
              class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl hover:bg-gray-100 transition"
              @click="userMenuOpen = !userMenuOpen"
            >
              <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center">
                <span class="text-sm font-bold text-violet-700">{{ userInitial }}</span>
              </div>
              <div class="hidden md:block text-left">
                <p class="text-sm font-semibold text-gray-800 leading-tight">{{ $page.props.auth.user?.name }}</p>
                <p class="text-xs text-gray-400 capitalize leading-tight">{{ $page.props.auth.user?.roles?.[0] }}</p>
              </div>
              <ChevronDownIcon class="w-4 h-4 text-gray-400 hidden md:block" />
            </button>

            <!-- Dropdown -->
            <div
              v-if="userMenuOpen"
              class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
            >
              <Link
                :href="route('dashboard')"
                class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50"
                @click="userMenuOpen = false"
              >
                <HomeIcon class="w-4 h-4" />
                Dashboard
              </Link>
              <hr class="my-1 border-gray-100">
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-500 hover:bg-red-50"
                @click="userMenuOpen = false"
              >
                <ArrowRightOnRectangleIcon class="w-4 h-4" />
                Sair
              </Link>
            </div>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="flex-1 p-6 overflow-y-auto">
        <!-- Flash messages -->
        <div v-if="$page.props.flash?.success" class="mb-4 flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
          <CheckCircleIcon class="w-4 h-4 flex-shrink-0" />
          {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" class="mb-4 flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
          <XCircleIcon class="w-4 h-4 flex-shrink-0" />
          {{ $page.props.flash.error }}
        </div>

        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  HomeIcon,
  ShoppingCartIcon,
  CubeIcon,
  ChartBarIcon,
  ChartPieIcon,
  ChatBubbleLeftRightIcon,
  Cog6ToothIcon,
  ArrowRightOnRectangleIcon,
  UsersIcon,
  BellIcon,
  MagnifyingGlassIcon,
  ChevronDownIcon,
  Bars3Icon,
  CheckCircleIcon,
  XCircleIcon,
} from '@heroicons/vue/24/outline';

// Props
const props = defineProps({
  pageTitle: {
    type: String,
    default: 'Dashboard',
  },
});

const page = usePage();
const sidebarOpen = ref(false);
const userMenuOpen = ref(false);
const userMenuRef = ref(null);

const userInitial = computed(() => {
  const name = page.props.auth?.user?.name || '';
  return name.charAt(0).toUpperCase();
});

const navItems = [
  { route: 'dashboard',      label: 'Dashboard',     icon: HomeIcon },
  { route: '#',              label: 'Leaderboard',   icon: ChartBarIcon },
  { route: 'sales.index',    label: 'Order',         icon: ShoppingCartIcon },
  { route: 'products.index', label: 'Products',      icon: CubeIcon },
  { route: 'sales.index',    label: 'Sales Report',  icon: ChartPieIcon }, // Using ChartPieIcon (need to import) or ChartBarIcon
  { route: '#',              label: 'Messages',      icon: ChatBubbleLeftRightIcon },
  { route: '#',              label: 'Settings',      icon: Cog6ToothIcon },
  { route: 'logout',         label: 'Sign Out',      icon: ArrowRightOnRectangleIcon, method: 'post' },
];

function isActive(item) {
  if (item.route === '#') return false;
  return route().current(item.route) || route().current(item.route + '.*');
}

function handleClickOutside(e) {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target)) {
    userMenuOpen.value = false;
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));
</script>
