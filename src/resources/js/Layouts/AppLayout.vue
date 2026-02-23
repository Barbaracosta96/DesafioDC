<template>
  <div class="flex h-screen overflow-hidden" style="background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 50%, #eff6ff 100%)">
    <!-- Sidebar -->
    <aside
      :class="[sidebarAberta ? 'translate-x-0' : '-translate-x-full', 'fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-xl transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 flex flex-col']"
    >
      <!-- Logo -->
      <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100 shrink-0">
        <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center shadow-md">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <span class="text-xl font-bold text-gray-900">Vendas</span>
      </div>

      <!-- Navegação -->
      <nav class="px-4 py-4 space-y-0.5 overflow-y-auto flex-1">
        <NavItem :href="route('dashboard')" :active="isAtivo('dashboard')" icon="dashboard">
          Dashboard
        </NavItem>
        <NavItem :href="route('estoque.index')" :active="isAtivo('estoque')" icon="estoque">
          Estoque
        </NavItem>
        <NavItem :href="route('vendas.index')" :active="isAtivo('vendas')" icon="vendas">
          Vendas
        </NavItem>
        <NavItem :href="route('clientes.index')" :active="isAtivo('clientes')" icon="clientes">
          Clientes
        </NavItem>
        <NavItem :href="route('categorias.index')" :active="isAtivo('categorias')" icon="categorias">
          Categorias
        </NavItem>
        <template v-if="$page.props.auth.user?.roles?.includes('admin')">
          <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administração</p>
          </div>
          <NavItem :href="route('usuarios.index')" :active="isAtivo('usuarios')" icon="usuarios">
            Usuários
          </NavItem>
        </template>

        <!-- Sair da conta no nav -->
        <div class="pt-2">
          <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="flex items-center gap-3 px-3 py-2.5 w-full rounded-xl text-sm font-medium text-gray-500 hover:bg-red-50 hover:text-red-600 transition-all duration-150"
          >
            <span class="w-5 h-5 shrink-0">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
            </span>
            Sair
          </Link>
        </div>
      </nav>

      <!-- Pro card -->
      <div class="px-4 pb-5 shrink-0">
        <div class="rounded-2xl p-4 text-white" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)">
          <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <p class="text-sm font-bold mb-0.5">Vendas Pro</p>
          <p class="text-xs text-white/75 mb-3">Acesse todos os recursos do sistema completo.</p>
          <button class="w-full bg-white text-indigo-600 text-xs font-bold py-2 rounded-xl hover:bg-indigo-50 transition-colors">
            Obter Pro
          </button>
        </div>
      </div>
    </aside>

    <!-- Overlay mobile -->
    <div
      v-if="sidebarAberta"
      class="fixed inset-0 z-40 bg-black/50 lg:hidden"
      @click="sidebarAberta = false"
    />

    <!-- Conteúdo principal -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Topbar -->
      <header class="bg-white border-b border-gray-100 px-4 lg:px-6 py-3 flex items-center gap-4 shrink-0 shadow-sm">
        <div class="flex items-center gap-3 shrink-0">
          <button
            class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100"
            @click="sidebarAberta = !sidebarAberta"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <h1 class="text-xl font-bold text-gray-900">{{ titulo }}</h1>
        </div>

        <!-- Search bar -->
        <div class="flex-1 max-w-sm hidden md:block">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
            </svg>
            <input
              type="text"
              placeholder="Pesquisar aqui..."
              class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-all"
            />
          </div>
        </div>

        <div class="flex items-center gap-2 ml-auto">
          <!-- Idioma -->
          <button class="hidden sm:flex items-center gap-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg px-2.5 py-1.5 hover:bg-gray-50">
            <span class="text-base leading-none">🇧🇷</span>
            <span>PT (BR)</span>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Notificações -->
          <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
          </button>

          <!-- Usuário -->
          <div class="relative" ref="menuRef">
            <button
              class="flex items-center gap-2.5 px-2 py-1.5 rounded-xl hover:bg-gray-100 transition-colors"
              @click="menuUsuario = !menuUsuario"
            >
              <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                {{ iniciais }}
              </div>
              <div class="hidden sm:block text-left">
                <p class="text-sm font-semibold text-gray-800 leading-tight">{{ $page.props.auth.user?.name }}</p>
                <p class="text-xs text-gray-400 leading-tight capitalize">{{ $page.props.auth.user?.roles?.[0] ?? 'Usuário' }}</p>
              </div>
              <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <div
              v-if="menuUsuario"
              class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50"
            >
              <div class="px-4 py-2.5 border-b border-gray-100">
                <p class="text-sm font-semibold text-gray-900">{{ $page.props.auth.user?.name }}</p>
                <p class="text-xs text-gray-500 capitalize">{{ $page.props.auth.user?.roles?.join(', ') }}</p>
              </div>
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
              >
                Sair da conta
              </Link>
            </div>
          </div>
        </div>
      </header>

      <!-- Flash messages -->
      <div v-if="$page.props.flash?.sucesso || $page.props.flash?.erro" class="px-4 lg:px-6 pt-4">
        <div
          v-if="$page.props.flash?.sucesso"
          class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3"
        >
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="text-sm font-medium">{{ $page.props.flash.sucesso }}</span>
        </div>
        <div
          v-if="$page.props.flash?.erro"
          class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3"
        >
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="text-sm font-medium">{{ $page.props.flash.erro }}</span>
        </div>
      </div>

      <!-- Slot de conteúdo -->
      <main class="flex-1 overflow-y-auto">
        <div class="px-4 lg:px-6 py-6">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import NavItem from '@/Components/NavItem.vue';

defineProps({ titulo: { type: String, default: '' } });

const page = usePage();
const sidebarAberta = ref(false);
const menuUsuario = ref(false);
const menuRef = ref(null);

const iniciais = computed(() => {
  const nome = page.props.auth?.user?.name ?? '';
  return nome.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase();
});

const isAtivo = (rota) => {
  const url = page.url;
  return url === '/' ? rota === 'dashboard' : url.startsWith('/' + rota);
};

const fecharMenu = (e) => {
  if (menuRef.value && !menuRef.value.contains(e.target)) {
    menuUsuario.value = false;
  }
};

onMounted(() => document.addEventListener('click', fecharMenu));
onUnmounted(() => document.removeEventListener('click', fecharMenu));
</script>
