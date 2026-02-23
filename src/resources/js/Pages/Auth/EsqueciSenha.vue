<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div class="flex flex-col items-center mb-8">
        <div class="w-14 h-14 rounded-full bg-indigo-600 flex items-center justify-center mb-4 shadow-lg">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Base</h1>
      </div>

      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <div class="flex justify-center mb-6">
          <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
        </div>

        <h2 class="text-xl font-semibold text-gray-900 text-center mb-2">Esqueceu a senha?</h2>
        <p class="text-sm text-gray-500 text-center mb-6">
          Informe seu e-mail e enviaremos um link para redefinir sua senha.
        </p>

        <div v-if="status" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
          {{ status }}
        </div>

        <form @submit.prevent="enviar" class="space-y-5">
          <InputField
            v-model="form.email"
            label="E-mail cadastrado"
            type="email"
            placeholder="seu@email.com"
            :erro="form.errors.email"
            obrigatorio
          />
          <Botao type="submit" class="w-full" :carregando="form.processing">
            Enviar link de recuperação
          </Botao>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
          <Link :href="route('login')" class="text-indigo-600 font-medium hover:text-indigo-800">
            ← Voltar ao login
          </Link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import InputField from '@/Components/InputField.vue';
import Botao from '@/Components/Botao.vue';

defineProps({ status: { type: String, default: null } });
const form = useForm({ email: '' });
const enviar = () => form.post(route('password.email'));
</script>
