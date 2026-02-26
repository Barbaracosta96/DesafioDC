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
          <li>
            <Link :href="route('dashboard')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all w-full text-left" :class="route().current('dashboard') ? 'bg-violet-600 text-white shadow-sm shadow-violet-200' : 'text-gray-500 hover:bg-violet-50 hover:text-violet-700'">
              <HomeIcon class="w-5 h-5 flex-shrink-0" /><span>{{ t('nav_dashboard') }}</span>
            </Link>
          </li>
          <li>
            <Link :href="route('leaderboard')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all w-full text-left" :class="route().current('leaderboard') ? 'bg-violet-600 text-white shadow-sm shadow-violet-200' : 'text-gray-500 hover:bg-violet-50 hover:text-violet-700'">
              <ChartBarIcon class="w-5 h-5 flex-shrink-0" /><span>{{ t('nav_leaderboard') }}</span>
            </Link>
          </li>
          <li>
            <Link :href="route('sales.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all w-full text-left" :class="route().current('sales.*') ? 'bg-violet-600 text-white shadow-sm shadow-violet-200' : 'text-gray-500 hover:bg-violet-50 hover:text-violet-700'">
              <ShoppingCartIcon class="w-5 h-5 flex-shrink-0" /><span>{{ t('nav_order') }}</span>
            </Link>
          </li>
          <li>
            <Link :href="route('products.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all w-full text-left" :class="route().current('products.*') ? 'bg-violet-600 text-white shadow-sm shadow-violet-200' : 'text-gray-500 hover:bg-violet-50 hover:text-violet-700'">
              <CubeIcon class="w-5 h-5 flex-shrink-0" /><span>{{ t('nav_products') }}</span>
            </Link>
          </li>
          <li>
            <Link :href="route('customers.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all w-full text-left" :class="route().current('customers.*') ? 'bg-violet-600 text-white shadow-sm shadow-violet-200' : 'text-gray-500 hover:bg-violet-50 hover:text-violet-700'">
              <ChartPieIcon class="w-5 h-5 flex-shrink-0" /><span>{{ t('nav_salesReport') }}</span>
            </Link>
          </li>
          <li>
            <button @click="messagesOpen = !messagesOpen; sidebarOpen = false; notificationsOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all w-full text-left" :class="messagesOpen ? 'bg-violet-600 text-white shadow-sm shadow-violet-200' : 'text-gray-500 hover:bg-violet-50 hover:text-violet-700'">
              <ChatBubbleLeftRightIcon class="w-5 h-5 flex-shrink-0" /><span>{{ t('nav_messages') }}</span>
              <span class="ml-auto text-[10px] font-bold px-1.5 py-0.5 rounded-full" :class="messagesOpen ? 'bg-white/30 text-white' : 'bg-violet-500 text-white'">3</span>
            </button>
          </li>
          <li>
            <Link :href="route('settings')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all w-full text-left" :class="route().current('settings') ? 'bg-violet-600 text-white shadow-sm shadow-violet-200' : 'text-gray-500 hover:bg-violet-50 hover:text-violet-700'">
              <Cog6ToothIcon class="w-5 h-5 flex-shrink-0" /><span>{{ t('nav_settings') }}</span>
            </Link>
          </li>
          <li>
            <Link :href="route('logout')" method="post" as="button" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all w-full text-left text-gray-500 hover:bg-red-50 hover:text-red-600">
              <ArrowRightOnRectangleIcon class="w-5 h-5 flex-shrink-0" /><span>{{ t('nav_signOut') }}</span>
            </Link>
          </li>
        </ul>
        <div v-if="$page.props.auth.user?.roles?.includes('admin')" class="mt-4 px-3">
          <p class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ t('nav_admin') }}</p>
          <ul class="space-y-0.5">
            <li>
              <Link :href="route('users.index')" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all" :class="route().current('users.*') ? 'bg-violet-600 text-white' : 'text-gray-500 hover:bg-violet-50 hover:text-violet-700'">
                <UsersIcon class="w-5 h-5 flex-shrink-0" /><span>{{ t('nav_users') }}</span>
              </Link>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Pro Banner -->
      <div class="mx-3 mb-4 p-4 bg-violet-600 rounded-2xl text-white">
        <div class="flex items-center gap-2 mb-2">
          <div class="w-7 h-7 bg-white/20 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z" /></svg>
          </div>
          <span class="font-bold text-sm">Dabang Pro</span>
        </div>
        <p class="text-xs text-violet-200 mb-3">{{ t('nav_proDesc') }}</p>
        <button class="w-full py-1.5 bg-white text-violet-700 text-xs font-bold rounded-lg hover:bg-violet-50 transition">{{ t('nav_getPro') }}</button>
      </div>
    </aside>

    <!-- Mobile overlay -->
    <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/30 md:hidden" @click="sidebarOpen = false" />

    <!-- Main content -->
    <div class="flex-1 flex flex-col md:ml-64">
      <!-- Top bar -->
      <header class="sticky top-0 z-30 flex items-center justify-between h-16 px-6 bg-white border-b border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
          <button class="md:hidden p-2 rounded-lg hover:bg-gray-100" @click="sidebarOpen = !sidebarOpen">
            <Bars3Icon class="w-5 h-5 text-gray-500" />
          </button>
          <h1 class="text-lg font-bold text-gray-800">{{ pageTitle }}</h1>
        </div>

        <div class="flex items-center gap-2">
          <!-- Search -->
          <div class="hidden md:flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 w-52">
            <MagnifyingGlassIcon class="w-4 h-4 text-gray-400 flex-shrink-0" />
            <input v-model="searchQuery" type="text" :placeholder="t('search_placeholder')" class="bg-transparent text-sm text-gray-600 outline-none w-full" />
          </div>

          <!-- Language selector -->
          <div class="relative hidden md:block" ref="langRef">
            <button
              @click="langOpen = !langOpen; notificationsOpen = false; userMenuOpen = false"
              class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-600 cursor-pointer hover:bg-gray-100 transition select-none"
            >
              <img :src="currentLang.flagImg" :alt="currentLang.flagAlt" class="w-5 h-4 object-cover rounded-sm flex-shrink-0" />
              <span class="font-medium">{{ currentLang.label }}</span>
              <ChevronDownIcon class="w-3 h-3 text-gray-400" />
            </button>
            <div v-if="langOpen" class="absolute right-0 top-full mt-1 w-44 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
              <button
                v-for="lang in languages"
                :key="lang.code"
                @click="selectLang(lang)"
                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm hover:bg-gray-50 transition"
                :class="currentLang.code === lang.code ? 'text-violet-600 font-semibold' : 'text-gray-600'"
              >
                <img :src="lang.flagImg" :alt="lang.flagAlt" class="w-5 h-4 object-cover rounded-sm flex-shrink-0" />
                <span>{{ lang.label }}</span>
                <span v-if="currentLang.code === lang.code" class="ml-auto w-1.5 h-1.5 bg-violet-600 rounded-full"></span>
              </button>
            </div>
          </div>

          <!-- Notifications bell -->
          <button
            class="relative p-2 rounded-xl hover:bg-gray-100 transition"
            @click="notificationsOpen = !notificationsOpen; messagesOpen = false; langOpen = false; userMenuOpen = false"
          >
            <BellIcon class="w-5 h-5 text-gray-500" />
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-amber-400 rounded-full ring-2 ring-white"></span>
          </button>

          <!-- User avatar + dropdown -->
          <div class="relative" ref="userMenuRef">
            <button class="flex items-center gap-2.5 pl-1.5 pr-3 py-1 rounded-xl hover:bg-gray-100 transition" @click="userMenuOpen = !userMenuOpen; notificationsOpen = false; langOpen = false">
              <img :src="userAvatar" :alt="$page.props.auth.user?.name" class="w-9 h-9 rounded-full object-cover border-2 border-violet-100 flex-shrink-0" @error="onAvatarError" />
              <div class="hidden md:block text-left">
                <p class="text-sm font-semibold text-gray-800 leading-tight">{{ $page.props.auth.user?.name }}</p>
                <p class="text-xs text-gray-400 capitalize leading-tight">{{ $page.props.auth.user?.roles?.[0] }}</p>
              </div>
              <ChevronDownIcon class="w-4 h-4 text-gray-400 hidden md:block" />
            </button>
            <div v-if="userMenuOpen" class="absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
              <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-50 mb-1">
                <img :src="userAvatar" class="w-8 h-8 rounded-full object-cover" @error="onAvatarError" />
                <div class="min-w-0">
                  <p class="text-sm font-semibold text-gray-800 truncate">{{ $page.props.auth.user?.name }}</p>
                  <p class="text-xs text-gray-400 capitalize">{{ $page.props.auth.user?.roles?.[0] }}</p>
                </div>
              </div>
              <Link :href="route('dashboard')" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50" @click="userMenuOpen = false">
                <HomeIcon class="w-4 h-4" /> {{ t('nav_dashboard') }}
              </Link>
              <Link :href="route('settings')" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50" @click="userMenuOpen = false">
                <Cog6ToothIcon class="w-4 h-4" /> {{ t('menu_settings') }}
              </Link>
              <hr class="my-1 border-gray-100" />
              <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-500 hover:bg-red-50" @click="userMenuOpen = false">
                <ArrowRightOnRectangleIcon class="w-4 h-4" /> {{ t('menu_signOut') }}
              </Link>
            </div>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="flex-1 p-6 overflow-y-auto">
        <div v-if="$page.props.flash?.success" class="mb-4 flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
          <CheckCircleIcon class="w-4 h-4 flex-shrink-0" />{{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" class="mb-4 flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
          <XCircleIcon class="w-4 h-4 flex-shrink-0" />{{ $page.props.flash.error }}
        </div>
        <slot />
      </main>
    </div>

    <!-- Notifications Panel -->
    <transition
      enter-active-class="transition-transform duration-300 ease-out"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform duration-200 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div v-if="notificationsOpen" class="fixed inset-y-0 right-0 z-50 w-96 bg-white shadow-2xl flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <div>
            <h2 class="font-bold text-gray-800">{{ t('notif_title') }}</h2>
            <p class="text-xs text-gray-400 mt-0.5">{{ demoNotifications.filter(n => !n.read).length }} {{ t('notif_unread') }}</p>
          </div>
          <div class="flex items-center gap-2">
            <button @click="markAllRead" class="text-xs text-violet-600 hover:text-violet-800 font-medium transition">{{ t('notif_markAllRead') }}</button>
            <button @click="notificationsOpen = false" class="p-2 rounded-lg hover:bg-gray-100"><XMarkIcon class="w-5 h-5 text-gray-500" /></button>
          </div>
        </div>
        <div class="flex gap-1 px-4 pt-3 pb-2">
          <button v-for="tab in [
            { key: 'all', label: t('notif_all') },
            { key: 'unread', label: t('notif_unreadTab') },
            { key: 'read', label: t('notif_readTab') },
          ]" :key="tab.key" @click="notifTabKey = tab.key"
            class="px-3 py-1 rounded-lg text-xs font-medium transition"
            :class="notifTabKey === tab.key ? 'bg-violet-600 text-white' : 'text-gray-500 hover:bg-gray-100'"
          >{{ tab.label }}</button>
        </div>
        <div class="flex-1 overflow-y-auto divide-y divide-gray-50">
          <div
            v-for="notif in filteredNotifications"
            :key="notif.id"
            @click="notif.read = true"
            class="flex items-start gap-3 px-4 py-4 hover:bg-gray-50 cursor-pointer transition"
            :class="{ 'bg-violet-50/40': !notif.read }"
          >
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-lg" :class="notif.bgColor">
              {{ notif.icon }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-gray-800">{{ t(notif.titleKey) }}</p>
              <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ t(notif.bodyKey) }}</p>
              <p class="text-xs text-gray-400 mt-1">{{ t(notif.timeKey) }}</p>
            </div>
            <span v-if="!notif.read" class="mt-1.5 flex-shrink-0 w-2 h-2 bg-violet-500 rounded-full"></span>
          </div>
          <div v-if="filteredNotifications.length === 0" class="flex flex-col items-center justify-center py-16 text-gray-400">
            <BellIcon class="w-10 h-10 mb-3 opacity-30" />
            <p class="text-sm">{{ t('notif_empty') }}</p>
          </div>
        </div>
        <div class="p-4 border-t border-gray-100">
          <button class="w-full py-2.5 border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-medium rounded-xl transition">
            {{ t('notif_viewAll') }}
          </button>
        </div>
      </div>
    </transition>
    <div v-if="notificationsOpen" class="fixed inset-0 z-40 bg-black/20" @click="notificationsOpen = false" />

    <!-- Messages Panel -->
    <transition enter-active-class="transition-transform duration-300 ease-out" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transition-transform duration-200 ease-in" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
      <div v-if="messagesOpen" class="fixed inset-y-0 right-0 z-50 w-96 bg-white shadow-2xl flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
          <div>
            <h2 class="font-bold text-gray-800">{{ t('msg_title') }}</h2>
            <p class="text-xs text-gray-400 mt-0.5">3 {{ t('msg_unread') }}</p>
          </div>
          <button @click="messagesOpen = false" class="p-2 rounded-lg hover:bg-gray-100"><XMarkIcon class="w-5 h-5 text-gray-500" /></button>
        </div>
        <div class="px-4 py-3 border-b border-gray-50">
          <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
            <MagnifyingGlassIcon class="w-4 h-4 text-gray-400" />
            <input type="text" :placeholder="t('search_messages')" class="bg-transparent text-sm outline-none flex-1 text-gray-600" />
          </div>
        </div>
        <div class="flex-1 overflow-y-auto divide-y divide-gray-50">
          <div v-for="msg in demoMessages" :key="msg.id" class="flex items-start gap-3 px-4 py-4 hover:bg-gray-50 cursor-pointer transition" :class="{ 'bg-violet-50/50': !msg.read }">
            <div class="relative flex-shrink-0">
              <img :src="msg.avatar" class="w-10 h-10 rounded-full object-cover" />
              <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white" :class="msg.online ? 'bg-green-400' : 'bg-gray-300'"></span>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between mb-0.5">
                <p class="text-sm font-semibold text-gray-800">{{ msg.name }}</p>
                <span class="text-xs text-gray-400">{{ msg.time }}</span>
              </div>
              <p class="text-xs text-gray-500 truncate">{{ msg.preview }}</p>
            </div>
            <span v-if="!msg.read" class="mt-1 flex-shrink-0 w-2 h-2 bg-violet-500 rounded-full"></span>
          </div>
        </div>
        <div class="p-4 border-t border-gray-100">
          <button class="w-full py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-xl transition flex items-center justify-center gap-2">
            <PencilSquareIcon class="w-4 h-4" /> {{ t('msg_newMessage') }}
          </button>
        </div>
      </div>
    </transition>
    <div v-if="messagesOpen" class="fixed inset-0 z-40 bg-black/20" @click="messagesOpen = false" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, reactive, provide } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  HomeIcon, ShoppingCartIcon, CubeIcon, ChartBarIcon, ChartPieIcon,
  ChatBubbleLeftRightIcon, Cog6ToothIcon, ArrowRightOnRectangleIcon,
  UsersIcon, BellIcon, MagnifyingGlassIcon, ChevronDownIcon, Bars3Icon,
  CheckCircleIcon, XCircleIcon, XMarkIcon, PencilSquareIcon,
} from '@heroicons/vue/24/outline';
import { useI18n } from '@/composables/useI18n.js';

defineProps({
  pageTitle: { type: String, default: 'Dashboard' },
});

const { t, locale, setLocale } = useI18n();
// Provide i18n to all descendant components (Dashboard, etc.)
provide('i18n', { t, locale, setLocale });

const page = usePage();
const sidebarOpen = ref(false);
const userMenuOpen = ref(false);
const messagesOpen = ref(false);
const notificationsOpen = ref(false);
const langOpen = ref(false);
const notifTabKey = ref('all'); // 'all' | 'unread' | 'read'
const userMenuRef = ref(null);
const langRef = ref(null);
const searchQuery = ref('');
const avatarFailed = ref(false);

const languages = [
  { code: 'en', flagImg: 'https://flagcdn.com/20x15/us.png', label: 'Eng (US)', flagAlt: 'US' },
  { code: 'pt', flagImg: 'https://flagcdn.com/20x15/br.png', label: 'PT (BR)', flagAlt: 'BR' },
];
const currentLang = computed(() => languages.find(l => l.code === locale.value) || languages[0]);

function selectLang(lang) {
  setLocale(lang.code);
  langOpen.value = false;
}

const userAvatar = computed(() => {
  if (avatarFailed.value) {
    const name = encodeURIComponent(page.props.auth?.user?.name || 'User');
    return `https://ui-avatars.com/api/?name=${name}&background=7C3AED&color=fff&size=128`;
  }
  const stored = page.props.auth?.user?.avatar;
  if (stored) return `/storage/${stored}`;
  return `https://i.pravatar.cc/150?img=56`;
});

function onAvatarError() {
  avatarFailed.value = true;
}

const demoNotifications = reactive([
  { id: 1, icon: '🛒', bgColor: 'bg-blue-50', titleKey: 'notif1_title', bodyKey: 'notif1_body', timeKey: 'time_2min', read: false },
  { id: 2, icon: '📦', bgColor: 'bg-amber-50', titleKey: 'notif2_title', bodyKey: 'notif2_body', timeKey: 'time_15min', read: false },
  { id: 3, icon: '✅', bgColor: 'bg-green-50', titleKey: 'notif3_title', bodyKey: 'notif3_body', timeKey: 'time_1h', read: false },
  { id: 4, icon: '👤', bgColor: 'bg-violet-50', titleKey: 'notif4_title', bodyKey: 'notif4_body', timeKey: 'time_3h', read: true },
  { id: 5, icon: '💳', bgColor: 'bg-red-50', titleKey: 'notif5_title', bodyKey: 'notif5_body', timeKey: 'time_yesterday', read: true },
  { id: 6, icon: '📊', bgColor: 'bg-indigo-50', titleKey: 'notif6_title', bodyKey: 'notif6_body', timeKey: 'time_yesterday', read: true },
  { id: 7, icon: '🎉', bgColor: 'bg-pink-50', titleKey: 'notif7_title', bodyKey: 'notif7_body', timeKey: 'time_2days', read: true },
]);

const filteredNotifications = computed(() => {
  if (notifTabKey.value === 'unread') return demoNotifications.filter(n => !n.read);
  if (notifTabKey.value === 'read') return demoNotifications.filter(n => n.read);
  return demoNotifications;
});

function markAllRead() {
  demoNotifications.forEach(n => { n.read = true; });
}

const demoMessages = [
  { id: 1, name: 'Sarah Johnson', time: '2m ago', preview: 'Hey! The new order just arrived. Can you check...', read: false, online: true, avatar: 'https://i.pravatar.cc/150?img=47' },
  { id: 2, name: 'Michael Chen', time: '15m ago', preview: "The inventory report for Q1 looks great! Let's discuss...", read: false, online: true, avatar: 'https://i.pravatar.cc/150?img=11' },
  { id: 3, name: 'Emma Wilson', time: '1h ago', preview: 'Client meeting is confirmed for Friday at 3pm...', read: false, online: false, avatar: 'https://i.pravatar.cc/150?img=5' },
  { id: 4, name: 'David Park', time: '3h ago', preview: 'The sales dashboard is showing great numbers this month!', read: true, online: true, avatar: 'https://i.pravatar.cc/150?img=12' },
  { id: 5, name: 'Lisa Torres', time: 'Yesterday', preview: 'Can you update the product catalog before end of day?', read: true, online: false, avatar: 'https://i.pravatar.cc/150?img=9' },
  { id: 6, name: 'James Smith', time: 'Yesterday', preview: 'All shipments have been processed and are on their way.', read: true, online: false, avatar: 'https://i.pravatar.cc/150?img=15' },
];

function handleClickOutside(e) {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target)) {
    userMenuOpen.value = false;
  }
  if (langRef.value && !langRef.value.contains(e.target)) {
    langOpen.value = false;
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));
</script>
