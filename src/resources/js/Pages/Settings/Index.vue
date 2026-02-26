<template>
  <AppLayout :page-title="t('set_title')">
    <div class="max-w-4xl mx-auto space-y-6">

      <!-- Header -->
      <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ t('set_title') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ t('set_subtitle') }}</p>
      </div>

      <!-- Tabs -->
      <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="activeTab === tab.id
            ? 'bg-white text-violet-700 shadow font-semibold'
            : 'text-gray-500 hover:text-gray-700'"
          class="px-5 py-2 rounded-lg text-sm transition-all"
        >
          {{ tab.label }}
        </button>
      </div>

      <!-- Profile Tab -->
      <div v-if="activeTab === 'profile'" class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-semibold text-gray-800 mb-4">{{ t('set_profileInfo') }}</h2>

          <!-- Avatar upload -->
          <div class="flex items-center gap-6 mb-6">
            <div class="relative">
              <img :src="previewAvatar" class="w-20 h-20 rounded-full object-cover border-4 border-violet-100" />
              <label class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-full cursor-pointer opacity-0 hover:opacity-100 transition-opacity">
                <CameraIcon class="w-6 h-6 text-white" />
                <input type="file" class="hidden" accept="image/*" @change="handleAvatarChange" />
              </label>
            </div>
            <div>
              <p class="font-medium text-gray-700">{{ t('set_profilePhoto') }}</p>
              <p class="text-xs text-gray-400 mt-1">{{ t('set_photoDesc') }}</p>
              <button class="mt-2 text-sm text-violet-600 font-medium hover:text-violet-700">{{ t('set_changePhoto') }}</button>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('set_firstName') }}</label>
              <input v-model="form.firstName" type="text"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 outline-none text-sm transition" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('set_lastName') }}</label>
              <input v-model="form.lastName" type="text"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 outline-none text-sm transition" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('set_email') }}</label>
              <input v-model="form.email" type="email"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 outline-none text-sm transition" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('set_phone') }}</label>
              <input v-model="form.phone" type="tel"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 outline-none text-sm transition" />
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('set_bio') }}</label>
              <textarea v-model="form.bio" rows="3"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 outline-none text-sm transition resize-none"
                :placeholder="t('set_bioPlaceholder')"></textarea>
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <button
              @click="saved = true; setTimeout(() => saved = false, 3000)"
              class="px-6 py-2.5 bg-violet-600 text-white rounded-xl text-sm font-medium hover:bg-violet-700 transition flex items-center gap-2"
            >
              <CheckCircleIcon v-if="saved" class="w-4 h-4" />
              {{ saved ? t('set_saved') : t('set_saveChanges') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Security Tab -->
      <div v-if="activeTab === 'security'" class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-semibold text-gray-800 mb-4">{{ t('set_changePassword') }}</h2>
          <div class="space-y-4 max-w-md">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('set_currentPassword') }}</label>
              <input type="password" v-model="passwordForm.current"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 outline-none text-sm transition" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('set_newPassword') }}</label>
              <input type="password" v-model="passwordForm.new"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 outline-none text-sm transition" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('set_confirmPassword') }}</label>
              <input type="password" v-model="passwordForm.confirm"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 outline-none text-sm transition" />
            </div>
            <button class="px-6 py-2.5 bg-violet-600 text-white rounded-xl text-sm font-medium hover:bg-violet-700 transition">
              {{ t('set_updatePassword') }}
            </button>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="font-semibold text-gray-800 mb-1">{{ t('set_2fa') }}</h2>
          <p class="text-sm text-gray-400 mb-4">{{ t('set_2faDesc') }}</p>
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
            <div class="flex items-center gap-3">
              <ShieldCheckIcon class="w-8 h-8 text-violet-500" />
              <div>
                <p class="font-medium text-gray-700 text-sm">{{ t('set_authApp') }}</p>
                <p class="text-xs text-gray-400">{{ t('set_authAppDesc') }}</p>
              </div>
            </div>
            <button class="px-4 py-1.5 border border-violet-200 text-violet-700 rounded-lg text-sm font-medium hover:bg-violet-50 transition">
              {{ t('set_enable') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Notifications Tab -->
      <div v-if="activeTab === 'notifications'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-semibold text-gray-800 mb-4">{{ t('set_notifPrefs') }}</h2>
        <div class="space-y-4">
          <div v-for="notif in notifications" :key="notif.id"
            class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
            <div>
              <p class="font-medium text-gray-700 text-sm">{{ t(notif.labelKey) }}</p>
              <p class="text-xs text-gray-400">{{ t(notif.descKey) }}</p>
            </div>
            <div class="flex gap-4">
              <label class="flex items-center gap-2 text-xs text-gray-500">
                <input type="checkbox" v-model="notif.email" class="rounded accent-violet-600" />
                Email
              </label>
              <label class="flex items-center gap-2 text-xs text-gray-500">
                <input type="checkbox" v-model="notif.push" class="rounded accent-violet-600" />
                Push
              </label>
            </div>
          </div>
          <div class="flex justify-end pt-2">
            <button class="px-6 py-2.5 bg-violet-600 text-white rounded-xl text-sm font-medium hover:bg-violet-700 transition">
              {{ t('set_savePrefs') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Appearance Tab -->
      <div v-if="activeTab === 'appearance'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-semibold text-gray-800 mb-4">{{ t('set_appearance') }}</h2>

        <div class="mb-6">
          <p class="text-sm font-medium text-gray-700 mb-3">{{ t('set_theme') }}</p>
          <div class="flex gap-3">
            <button
              v-for="theme in themes"
              :key="theme.id"
              @click="activeTheme = theme.id"
              :class="activeTheme === theme.id ? 'ring-2 ring-violet-500' : 'ring-1 ring-gray-200'"
              class="flex flex-col items-center gap-2 p-3 rounded-xl cursor-pointer transition-all"
            >
              <div :class="theme.preview" class="w-16 h-10 rounded-lg border border-gray-200"></div>
              <span class="text-xs text-gray-600">{{ theme.label }}</span>
            </button>
          </div>
        </div>

        <div>
          <p class="text-sm font-medium text-gray-700 mb-3">{{ t('set_accentColor') }}</p>
          <div class="flex gap-3">
            <button
              v-for="color in colors"
              :key="color.id"
              @click="activeColor = color.id"
              :class="[color.bg, activeColor === color.id ? 'ring-2 ring-offset-2 ring-gray-400 scale-110' : '']"
              class="w-8 h-8 rounded-full transition-all"
            ></button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Pages/Layouts/AppLayout.vue';
import { CameraIcon, CheckCircleIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n.js';

const { t } = useI18n();

const page = usePage();
const user = page.props.auth?.user || {};
const nameParts = (user.name || '').split(' ');

const tabs = computed(() => [
  { id: 'profile', label: t('set_tab_profile') },
  { id: 'security', label: t('set_tab_security') },
  { id: 'notifications', label: t('set_tab_notifications') },
  { id: 'appearance', label: t('set_tab_appearance') },
]);
const activeTab = ref('profile');

const saved = ref(false);
const previewAvatar = ref(user.avatar ? `/storage/${user.avatar}` : 'https://i.pravatar.cc/150?img=56');

const form = ref({
  firstName: nameParts[0] || '',
  lastName: nameParts.slice(1).join(' ') || '',
  email: user.email || '',
  phone: '',
  bio: '',
});

const passwordForm = ref({ current: '', new: '', confirm: '' });

function handleAvatarChange(e) {
  const file = e.target.files[0];
  if (file) previewAvatar.value = URL.createObjectURL(file);
}

const notifications = ref([
  { id: 1, labelKey: 'set_notif1', descKey: 'set_notif1desc', email: true, push: true },
  { id: 2, labelKey: 'set_notif2', descKey: 'set_notif2desc', email: true, push: false },
  { id: 3, labelKey: 'set_notif3', descKey: 'set_notif3desc', email: false, push: true },
  { id: 4, labelKey: 'set_notif4', descKey: 'set_notif4desc', email: true, push: true },
  { id: 5, labelKey: 'set_notif5', descKey: 'set_notif5desc', email: true, push: false },
  { id: 6, labelKey: 'set_notif6', descKey: 'set_notif6desc', email: true, push: true },
]);

const themes = computed(() => [
  { id: 'light', label: t('set_themeLight'), preview: 'bg-white' },
  { id: 'dark', label: t('set_themeDark'), preview: 'bg-gray-900' },
  { id: 'system', label: t('set_themeSystem'), preview: 'bg-gradient-to-r from-white to-gray-900' },
]);
const activeTheme = ref('light');

const colors = [
  { id: 'violet', bg: 'bg-violet-600' },
  { id: 'blue', bg: 'bg-blue-600' },
  { id: 'emerald', bg: 'bg-emerald-600' },
  { id: 'rose', bg: 'bg-rose-500' },
  { id: 'amber', bg: 'bg-amber-500' },
  { id: 'cyan', bg: 'bg-cyan-500' },
];
const activeColor = ref('violet');
</script>
