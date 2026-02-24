<template>
  <GuestLayout>
    <Head title="Redefinir senha" />

    <div>
      <div class="w-16 h-16 bg-violet-100 rounded-2xl flex items-center justify-center mb-6">
        <KeyIcon class="w-8 h-8 text-violet-600" />
      </div>

      <h1 class="text-2xl font-bold text-gray-800 mb-2">Redefinir senha</h1>
      <p class="text-gray-400 text-sm mb-8">
        Insira sua nova senha nos campos abaixo.
      </p>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">E-mail</label>
          <input
            v-model="form.email"
            type="email"
            readonly
            class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm text-gray-500 cursor-not-allowed"
          />
          <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Nova senha</label>
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
          <span v-if="form.processing">Redefinindo...</span>
          <span v-else>Redefinir senha</span>
        </button>
      </form>
    </div>
  </GuestLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Pages/Layouts/GuestLayout.vue';
import { EyeIcon, EyeSlashIcon, KeyIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  token: { type: String, required: true },
  email: { type: String, required: true },
});

const showPassword = ref(false);

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

function submit() {
  form.post(route('password.update'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
}
</script>
