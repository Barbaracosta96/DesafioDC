<template>
  <GuestLayout>
    <Head title="Entrar" />

    <div>
      <h1 class="text-2xl font-bold text-gray-800 mb-1">Bem-vindo de volta!</h1>
      <p class="text-gray-400 text-sm mb-8">Entre na sua conta para continuar</p>

      <!-- Status message -->
      <div v-if="status" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
        {{ status }}
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">E-mail</label>
          <input
            v-model="form.email"
            type="email"
            autocomplete="email"
            placeholder="seu@email.com"
            class="w-full px-4 py-3 bg-gray-50 border rounded-xl text-sm text-gray-800 outline-none transition-all focus:ring-2 focus:ring-violet-200 focus:border-violet-400"
            :class="form.errors.email ? 'border-red-300 bg-red-50' : 'border-gray-200'"
          />
          <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
        </div>

        <!-- Password -->
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="text-sm font-medium text-gray-700">Senha</label>
            <Link :href="route('password.request')" class="text-xs text-violet-600 hover:text-violet-700 font-medium">
              Esqueceu a senha?
            </Link>
          </div>
          <div class="relative">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              autocomplete="current-password"
              placeholder="••••••••"
              class="w-full px-4 py-3 bg-gray-50 border rounded-xl text-sm text-gray-800 outline-none transition-all focus:ring-2 focus:ring-violet-200 focus:border-violet-400 pr-11"
              :class="form.errors.password ? 'border-red-300 bg-red-50' : 'border-gray-200'"
            />
            <button
              type="button"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              @click="showPassword = !showPassword"
            >
              <EyeIcon v-if="!showPassword" class="w-5 h-5" />
              <EyeSlashIcon v-else class="w-5 h-5" />
            </button>
          </div>
          <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
        </div>

        <!-- Remember me -->
        <div class="flex items-center gap-2">
          <input
            id="remember"
            v-model="form.remember"
            type="checkbox"
            class="w-4 h-4 accent-violet-600 rounded"
          />
          <label for="remember" class="text-sm text-gray-600">Lembrar de mim</label>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-3 bg-violet-600 hover:bg-violet-700 disabled:opacity-60 text-white font-semibold rounded-xl transition-all text-sm shadow-sm shadow-violet-200"
        >
          <span v-if="form.processing" class="flex items-center justify-center gap-2">
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            Entrando...
          </span>
          <span v-else>Entrar</span>
        </button>
      </form>

      <!-- Register link -->
      <p class="mt-6 text-center text-sm text-gray-500">
        Não tem conta?
        <Link :href="route('register')" class="font-semibold text-violet-600 hover:text-violet-700 ml-1">
          Criar conta
        </Link>
      </p>

      <!-- Demo accounts -->
      <div class="mt-8 p-4 bg-gray-50 rounded-xl border border-gray-100">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Contas de demonstração</p>
        <div class="space-y-2">
          <button
            v-for="demo in demoAccounts"
            :key="demo.email"
            @click="fillDemo(demo)"
            type="button"
            class="w-full flex items-center justify-between px-3 py-2 bg-white border border-gray-200 rounded-lg hover:border-violet-300 hover:bg-violet-50 transition text-xs"
          >
            <div class="text-left">
              <p class="font-semibold text-gray-700">{{ demo.label }}</p>
              <p class="text-gray-400">{{ demo.email }}</p>
            </div>
            <span class="px-2 py-0.5 rounded-md text-xs font-medium"
              :class="{
                'bg-violet-100 text-violet-700': demo.role === 'admin',
                'bg-blue-100 text-blue-700': demo.role === 'editor',
                'bg-green-100 text-green-700': demo.role === 'user',
              }">
              {{ demo.role }}
            </span>
          </button>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Pages/Layouts/GuestLayout.vue';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';

defineProps({
  status: String,
});

const showPassword = ref(false);

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const demoAccounts = [
  { label: 'Admin Dabang',  email: 'admin@dabang.app',  password: 'password', role: 'admin' },
  { label: 'Editor User',   email: 'editor@dabang.app', password: 'password', role: 'editor' },
  { label: 'Regular User',  email: 'user@dabang.app',   password: 'password', role: 'user' },
];

function fillDemo(demo) {
  form.email    = demo.email;
  form.password = demo.password;
}

function submit() {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
}
</script>
