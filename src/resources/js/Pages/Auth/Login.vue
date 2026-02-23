<template>
  <div class="min-h-screen flex">
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 flex-col justify-between relative overflow-hidden">
      <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?q=85&w=1400&auto=format&fit=crop" alt="Medica profissional" class="absolute inset-0 w-full h-full object-cover object-center" />
      <div class="absolute inset-0" style="background: linear-gradient(160deg, rgba(15,23,42,0.82) 0%, rgba(30,58,138,0.70) 50%, rgba(15,23,42,0.55) 100%)" />
      <div class="relative z-10 flex items-center gap-3 p-10">
        <div class="w-11 h-11 rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 flex items-center justify-center shadow-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
          </svg>
        </div>
        <div>
          <span class="text-xl font-bold text-white tracking-wide">ClinicBase</span>
          <p class="text-xs text-white/60 leading-none mt-0.5">Gestao em Saude</p>
        </div>
      </div>
      <div class="relative z-10 p-10 pb-16">
        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-1.5 mb-6">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          <span class="text-xs text-white/80 font-medium">Plataforma certificada CFM</span>
        </div>
        <h2 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight mb-5">Tecnologia a servico<br/>da saude do paciente</h2>
        <p class="text-blue-200 text-base mb-10 leading-relaxed max-w-md">Gerencie consultas, estoque de insumos e equipe medica em uma unica plataforma segura e moderna.</p>
        <div class="space-y-3.5">
          <div v-for="item in beneficios" :key="item.texto" class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-emerald-500/25 border border-emerald-400/30 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <span class="text-white/85 text-sm font-medium">{{ item.texto }}</span>
          </div>
        </div>
        <div class="flex gap-8 mt-10 pt-8 border-t border-white/15">
          <div><p class="text-2xl font-extrabold text-white">+1.200</p><p class="text-xs text-white/55 mt-0.5">Clinicas cadastradas</p></div>
          <div><p class="text-2xl font-extrabold text-white">98,7%</p><p class="text-xs text-white/55 mt-0.5">Satisfacao dos usuarios</p></div>
          <div><p class="text-2xl font-extrabold text-white">24/7</p><p class="text-xs text-white/55 mt-0.5">Suporte disponivel</p></div>
        </div>
      </div>
      <div class="relative z-10 px-10 pb-6 text-white/35 text-xs">&#169; 2026 ClinicBase &middot; Plataforma de Gestao em Saude</div>
    </div>
    <div class="flex-1 flex flex-col items-center justify-center bg-slate-50 px-6 py-12 sm:px-12">
      <div class="lg:hidden flex flex-col items-center mb-10">
        <div class="w-14 h-14 rounded-2xl bg-blue-700 flex items-center justify-center shadow-lg mb-4">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">ClinicBase</h1>
        <p class="text-sm text-gray-400 mt-1">Plataforma de Gestao em Saude</p>
      </div>
      <div class="w-full max-w-md">
        <div class="mb-8">
          <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Bem-vindo de volta</h2>
          <p class="text-gray-500 mt-2 text-sm leading-relaxed">Acesse sua conta para gerenciar sua clinica com seguranca.</p>
        </div>
        <div v-if="status" class="mb-5 flex items-center gap-2.5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl">
          <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="text-sm text-emerald-700">{{ status }}</span>
        </div>
        <form @submit.prevent="enviar" class="space-y-5">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">E-mail profissional <span class="text-red-500">*</span></label>
            <div class="relative">
              <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <input v-model="form.email" type="email" placeholder="seu@clinica.com.br" autocomplete="email"
                :class="['w-full pl-11 pr-4 py-3.5 rounded-xl border text-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent', form.errors.email ? 'border-red-400 bg-red-50 text-red-900' : 'border-gray-200 bg-white text-gray-900 hover:border-gray-300 shadow-sm']" />
            </div>
            <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-600">{{ form.errors.email }}</p>
          </div>
          <div>
            <div class="flex items-center justify-between mb-2">
              <label class="text-sm font-semibold text-gray-700">Senha <span class="text-red-500">*</span></label>
              <Link :href="route('password.request')" class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">Esqueci minha senha</Link>
            </div>
            <div class="relative">
              <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <input v-model="form.password" :type="mostrarSenha ? 'text' : 'password'" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" autocomplete="current-password"
                :class="['w-full pl-11 pr-11 py-3.5 rounded-xl border text-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent', form.errors.password ? 'border-red-400 bg-red-50 text-red-900' : 'border-gray-200 bg-white text-gray-900 hover:border-gray-300 shadow-sm']" />
              <button type="button" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" @click="mostrarSenha = !mostrarSenha">
                <svg v-if="!mostrarSenha" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
              </button>
            </div>
            <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-600">{{ form.errors.password }}</p>
          </div>
          <label class="flex items-center gap-3 cursor-pointer group">
            <div class="relative">
              <input type="checkbox" v-model="form.lembrar" class="sr-only peer" />
              <div class="w-5 h-5 rounded-md border-2 border-gray-300 bg-white peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all flex items-center justify-center">
                <svg v-if="form.lembrar" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
              </div>
            </div>
            <span class="text-sm text-gray-600 select-none">Manter sessao ativa</span>
          </label>
          <button type="submit" :disabled="form.processing"
            class="w-full flex items-center justify-center gap-2 py-4 px-6 rounded-xl text-sm font-bold text-white transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-60 disabled:cursor-not-allowed hover:opacity-95"
            style="background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%); box-shadow: 0 4px 20px rgba(37,99,235,0.40)">
            <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            <span>{{ form.processing ? 'Autenticando...' : 'Acessar plataforma' }}</span>
          </button>
        </form>
        <div class="flex items-center gap-3 my-6">
          <div class="flex-1 h-px bg-gray-200"></div>
          <span class="text-xs text-gray-400 font-medium">ou</span>
          <div class="flex-1 h-px bg-gray-200"></div>
        </div>
        <p class="text-center text-sm text-gray-500">Ainda nao tem acesso?&nbsp;<Link :href="route('cadastro')" class="text-blue-600 font-semibold hover:text-blue-800 transition-colors">Solicitar cadastro</Link></p>
        <div class="flex items-center justify-center gap-2 mt-8 text-xs text-gray-400">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          <span>Conexao protegida com criptografia SSL/TLS</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

defineProps({ status: { type: String, default: null } });

const form = useForm({ email: '', password: '', lembrar: false });
const mostrarSenha = ref(false);
const enviar = () => form.post(route('login.store'));

const beneficios = [
  { texto: 'Controle total de estoque de insumos medicos' },
  { texto: 'Gestao de vendas e atendimentos da clinica' },
  { texto: 'Relatorios financeiros em tempo real' },
  { texto: 'Controle de acesso por perfil profissional' },
];
</script>