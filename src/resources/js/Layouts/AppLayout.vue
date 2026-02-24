<template>
  <div class="flex h-screen overflow-hidden" style="background: linear-gradient(135deg, #e8f8f0 0%, #f8fafc 40%, #ede9fe 100%)">
    <!-- Sidebar -->
    <aside
      :class="[sidebarAberta ? 'translate-x-0' : '-translate-x-full', 'fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 flex flex-col']"
    >
      <!-- Logo -->
      <div class="flex items-center gap-3 px-6 h-[66px] border-b border-gray-100 shrink-0">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, #0c3b6e, #1a5fa8);">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <div>
          <span class="text-lg font-extrabold text-gray-900 tracking-widest">CINDEC</span>
          <p class="text-xs text-gray-400 leading-none" style="font-size: 9px; letter-spacing: 0.05em;">Defesa Civil — MG</p>
        </div>
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
        <div class="rounded-2xl p-4 text-white" style="background: linear-gradient(135deg, #0c3b6e 0%, #1a5fa8 100%)">
          <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <p class="text-sm font-bold mb-0.5">CINDEC Avançado</p>
          <p class="text-xs text-white/75 mb-3">Acesse todos os modulos operacionais completos.</p>
          <button class="w-full bg-white text-blue-800 text-xs font-bold py-2 rounded-xl hover:bg-blue-50 transition-colors">
            Ativar Modulo
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
      <header class="bg-white border-b border-gray-100 px-4 lg:px-6 h-[66px] flex items-center gap-4 shrink-0">
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

        <!-- Spacer -->
        <div class="flex-1" />

        <div class="flex items-center gap-3 ml-auto">
          <!-- Search bar -->
          <div class="hidden md:block">
            <div class="relative">
              <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
              </svg>
              <input
                type="text"
                placeholder="Pesquisar..."
                class="w-52 pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-full text-sm text-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 transition-all"
              />
            </div>
          </div>

          <!-- Idioma -->
          <div class="relative hidden sm:block" ref="langRef">
            <button
              class="flex items-center gap-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg px-2.5 py-1.5 hover:bg-gray-50 transition-colors"
              @click="langMenuOpen = !langMenuOpen"
            >
              <img
                :src="selectedLang === 'en' ? 'https://flagcdn.com/w20/us.png' : 'https://flagcdn.com/w20/br.png'"
                :alt="selectedLang === 'en' ? 'US' : 'BR'"
                class="w-5 h-3.5 rounded-sm object-cover"
              />
              <span>{{ selectedLang === 'en' ? 'Eng (US)' : 'PT (BR)' }}</span>
              <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div
              v-if="langMenuOpen"
              class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50"
            >
              <button
                @click="selectedLang = 'en'; langMenuOpen = false"
                class="flex items-center gap-2 w-full px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors"
                :class="selectedLang === 'en' ? 'font-semibold text-indigo-600' : ''"
              >
                <img src="https://flagcdn.com/w20/us.png" alt="US" class="w-5 h-3.5 rounded-sm object-cover" />
                Eng (US)
              </button>
              <button
                @click="selectedLang = 'pt'; langMenuOpen = false"
                class="flex items-center gap-2 w-full px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors"
                :class="selectedLang === 'pt' ? 'font-semibold text-indigo-600' : ''"
              >
                <img src="https://flagcdn.com/w20/br.png" alt="BR" class="w-5 h-3.5 rounded-sm object-cover" />
                PT (BR)
              </button>
            </div>
          </div>

          <!-- Notificações -->
          <div class="relative" ref="notifRef">
            <button
              class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors"
              @click="notifOpen = !notifOpen"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
            </button>

            <!-- Dropdown de Notificações -->
            <div
              v-if="notifOpen"
              class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden"
            >
              <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                <h4 class="text-sm font-bold text-gray-900">Notificações</h4>
                <span class="text-xs bg-rose-500 text-white rounded-full px-2 py-0.5 font-semibold">3 novas</span>
              </div>
              <div class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
                <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors cursor-pointer">
                  <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-900">Nova aquisição registrada</p>
                    <p class="text-xs text-gray-500 mt-0.5">Ordem #OP-001 foi criada com sucesso</p>
                    <p class="text-xs text-gray-400 mt-1">há 5 minutos</p>
                  </div>
                  <span class="w-2 h-2 bg-rose-500 rounded-full mt-1 shrink-0"></span>
                </div>
                <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors cursor-pointer">
                  <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-900">Estoque crítico detectado</p>
                    <p class="text-xs text-gray-500 mt-0.5">3 ativos tecnológicos com estoque crítico</p>
                    <p class="text-xs text-gray-400 mt-1">há 20 minutos</p>
                  </div>
                  <span class="w-2 h-2 bg-amber-500 rounded-full mt-1 shrink-0"></span>
                </div>
                <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors cursor-pointer">
                  <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-900">Nova entidade cadastrada</p>
                    <p class="text-xs text-gray-500 mt-0.5">Uma nova entidade foi registrada no sistema</p>
                    <p class="text-xs text-gray-400 mt-1">há 1 hora</p>
                  </div>
                  <span class="w-2 h-2 bg-emerald-500 rounded-full mt-1 shrink-0"></span>
                </div>
                <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors cursor-pointer opacity-60">
                  <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-900">Operação concluída</p>
                    <p class="text-xs text-gray-500 mt-0.5">Ordem #OP-995 foi finalizada</p>
                    <p class="text-xs text-gray-400 mt-1">há 3 horas</p>
                  </div>
                </div>
              </div>
              <div class="px-4 py-2.5 border-t border-gray-100 text-center">
                <button class="text-xs text-indigo-600 font-semibold hover:text-indigo-800 transition-colors" @click="notifOpen = false">
                  Ver todas as notificações
                </button>
              </div>
            </div>
          </div>

          <!-- Usuário -->
          <div class="relative" ref="menuRef">
            <button
              class="flex items-center gap-2.5 px-2 py-1.5 rounded-xl hover:bg-gray-100 transition-colors"
              @click="menuUsuario = !menuUsuario"
            >
              <div class="w-9 h-9 rounded-full overflow-hidden ring-2 ring-indigo-200 shadow-sm shrink-0">
                <img
                  src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=80&h=80&fit=crop&crop=face&q=80"
                  alt="Operadora"
                  class="w-full h-full object-cover"
                  loading="eager"
                  decoding="async"
                  @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='flex'"
                />
                <span
                  class="text-white text-sm font-bold w-full h-full items-center justify-center select-none bg-gradient-to-br from-indigo-500 to-purple-600"
                  style="display:none"
                >{{ iniciais }}</span>
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
const langMenuOpen = ref(false);
const langRef = ref(null);
const selectedLang = ref('en');
const notifOpen = ref(false);
const notifRef = ref(null);

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
  if (langRef.value && !langRef.value.contains(e.target)) {
    langMenuOpen.value = false;
  }
  if (notifRef.value && !notifRef.value.contains(e.target)) {
    notifOpen.value = false;
  }
};

onMounted(() => document.addEventListener('click', fecharMenu));
onUnmounted(() => document.removeEventListener('click', fecharMenu));
</script>
