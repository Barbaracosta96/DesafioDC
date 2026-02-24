<template>
  <GuestLayout>
    <Head title="Recuperar senha" />

    <div>
      <!-- Back link -->
      <Link :href="route('login')" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-600 mb-8">
        <ArrowLeftIcon class="w-4 h-4" />
        Voltar ao login
      </Link>

      <div class="w-16 h-16 bg-violet-100 rounded-2xl flex items-center justify-center mb-6">
        <LockClosedIcon class="w-8 h-8 text-violet-600" />
      </div>

      <h1 class="text-2xl font-bold text-gray-800 mb-2">Esqueceu a senha?</h1>
      <p class="text-gray-400 text-sm mb-8">
        Sem problema. Informe seu e-mail e enviaremos um link para redefinir sua senha.
      </p>

      <!-- Status message -->
      <div v-if="status" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
        <CheckCircleIcon class="w-4 h-4 flex-shrink-0" />
        {{ status }}
      </div>

      <form @submit.prevent="submit" class="space-y-4">
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

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-3 bg-violet-600 hover:bg-violet-700 disabled:opacity-60 text-white font-semibold rounded-xl transition-all text-sm shadow-sm shadow-violet-200"
        >
          <span v-if="form.processing">Enviando...</span>
          <span v-else>Enviar link de redefinição</span>
        </button>
      </form>
    </div>
  </GuestLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Pages/Layouts/GuestLayout.vue';
import { ArrowLeftIcon, LockClosedIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

defineProps({
  status: String,
});

const form = useForm({ email: '' });

function submit() {
  form.post(route('password.email'));
}
</script>
