<template>
  <GuestLayout>
    <Head title="Criar conta" />

    <div>
      <h1 class="text-2xl font-bold text-gray-800 mb-1">Crie sua conta</h1>
      <p class="text-gray-400 text-sm mb-8">Comece gratuitamente hoje mesmo</p>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome completo</label>
          <input
            v-model="form.name"
            type="text"
            autocomplete="name"
            placeholder="Seu nome"
            class="w-full px-4 py-3 bg-gray-50 border rounded-xl text-sm outline-none transition-all focus:ring-2 focus:ring-violet-200 focus:border-violet-400"
            :class="form.errors.name ? 'border-red-300 bg-red-50' : 'border-gray-200'"
          />
          <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">E-mail</label>
          <input
            v-model="form.email"
            type="email"
            autocomplete="email"
            placeholder="seu@email.com"
            class="w-full px-4 py-3 bg-gray-50 border rounded-xl text-sm outline-none transition-all focus:ring-2 focus:ring-violet-200 focus:border-violet-400"
            :class="form.errors.email ? 'border-red-300 bg-red-50' : 'border-gray-200'"
          />
          <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Senha</label>
          <div class="relative">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              autocomplete="new-password"
              placeholder="Mínimo 8 caracteres"
              class="w-full px-4 py-3 bg-gray-50 border rounded-xl text-sm outline-none transition-all focus:ring-2 focus:ring-violet-200 focus:border-violet-400 pr-11"
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

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar senha</label>
          <input
            v-model="form.password_confirmation"
            :type="showPassword ? 'text' : 'password'"
            autocomplete="new-password"
            placeholder="Repita a senha"
            class="w-full px-4 py-3 bg-gray-50 border rounded-xl text-sm outline-none transition-all focus:ring-2 focus:ring-violet-200 focus:border-violet-400"
            :class="form.errors.password_confirmation ? 'border-red-300 bg-red-50' : 'border-gray-200'"
          />
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-3 bg-violet-600 hover:bg-violet-700 disabled:opacity-60 text-white font-semibold rounded-xl transition-all text-sm shadow-sm shadow-violet-200"
        >
          <span v-if="form.processing">Criando conta...</span>
          <span v-else>Criar conta</span>
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-gray-500">
        Já tem uma conta?
        <Link :href="route('login')" class="font-semibold text-violet-600 hover:text-violet-700 ml-1">
          Entrar
        </Link>
      </p>

      <p class="mt-4 text-center text-xs text-gray-400">
        Ao criar uma conta você concorda com nossos
        <a href="#" class="underline text-gray-500">Termos de Uso</a>.
      </p>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Pages/Layouts/GuestLayout.vue';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';

const showPassword = ref(false);
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

function submit() {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
}
</script>
