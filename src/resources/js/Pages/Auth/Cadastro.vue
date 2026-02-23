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
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Criar conta gratuita</h2>
        <p class="text-sm text-gray-500 mb-6">Preencha os dados para começar</p>

        <form @submit.prevent="enviar" class="space-y-5">
          <InputField
            v-model="form.name"
            label="Nome completo"
            placeholder="Seu nome"
            :erro="form.errors.name"
            obrigatorio
          />
          <InputField
            v-model="form.email"
            label="E-mail"
            type="email"
            placeholder="seu@email.com"
            :erro="form.errors.email"
            obrigatorio
          />
          <InputField
            v-model="form.password"
            label="Senha"
            type="password"
            placeholder="Mínimo 8 caracteres"
            :erro="form.errors.password"
            obrigatorio
          />
          <InputField
            v-model="form.password_confirmation"
            label="Confirmar senha"
            type="password"
            placeholder="Repita a senha"
            obrigatorio
          />

          <Botao type="submit" class="w-full" :carregando="form.processing">
            Criar conta
          </Botao>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
          Já tem conta?
          <Link :href="route('login')" class="text-indigo-600 font-medium hover:text-indigo-800">
            Fazer login
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

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });
const enviar = () => form.post(route('cadastro.store'));
</script>
