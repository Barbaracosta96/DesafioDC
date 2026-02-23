<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="flex flex-col items-center mb-8">
        <div class="w-14 h-14 rounded-full bg-indigo-600 flex items-center justify-center mb-4 shadow-lg">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Base</h1>
        <p class="text-sm text-gray-500 mt-1">Plataforma SaaS de Gestão</p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Bem-vindo de volta</h2>
        <p class="text-sm text-gray-500 mb-6">Entre na sua conta para continuar</p>

        <!-- Status de recuperação de senha -->
        <div v-if="status" class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
          {{ status }}
        </div>

        <form @submit.prevent="enviar" class="space-y-5">
          <InputField
            v-model="form.email"
            label="E-mail"
            type="email"
            placeholder="seu@email.com"
            :erro="form.errors.email"
            obrigatorio
          />
          <div>
            <InputField
              v-model="form.password"
              label="Senha"
              type="password"
              placeholder="••••••••"
              :erro="form.errors.password"
              obrigatorio
            />
            <div class="mt-1.5 text-right">
              <Link :href="route('password.request')" class="text-xs text-indigo-600 hover:text-indigo-800">
                Esqueci minha senha
              </Link>
            </div>
          </div>

          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" v-model="form.lembrar" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
            <span class="text-sm text-gray-600">Lembrar de mim</span>
          </label>

          <Botao type="submit" class="w-full" :carregando="form.processing">
            Entrar
          </Botao>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
          Não tem conta?
          <Link :href="route('cadastro')" class="text-indigo-600 font-medium hover:text-indigo-800">
            Criar conta
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

const form = useForm({ email: '', password: '', lembrar: false });
const enviar = () => form.post(route('login.store'));
</script>
