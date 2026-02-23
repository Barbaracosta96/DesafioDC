<template>
  <div class="min-h-screen flex">
    <!-- Painel esquerdo — Identidade Visual CINDEC/MG -->
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 flex-col justify-between relative overflow-hidden" style="background: #020b18;">
      <!-- Imagem de fundo: central de operações tecnológica -->
      <img
        src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=85&w=1400&auto=format&fit=crop"
        alt="Operacoes CINDEC"
        class="absolute inset-0 w-full h-full object-cover object-center opacity-30"
      />
      <!-- Sobreposição gradiente escura -->
      <div class="absolute inset-0" style="background: linear-gradient(160deg, rgba(2,11,24,0.95) 0%, rgba(0,30,80,0.80) 50%, rgba(2,11,24,0.85) 100%)" />

      <!-- Grade tecnológica animada (linhas de fundo) -->
      <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(rgba(56,189,248,0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(56,189,248,0.5) 1px, transparent 1px); background-size: 48px 48px;" />

      <!-- Radar animado (centro decorativo) -->
      <div class="absolute bottom-24 right-10 w-72 h-72 opacity-15 pointer-events-none">
        <div class="absolute inset-0 rounded-full border border-sky-400/60" style="animation: radar-ring 3s linear infinite; animation-delay: 0s;"></div>
        <div class="absolute inset-6 rounded-full border border-sky-400/50" style="animation: radar-ring 3s linear infinite; animation-delay: 0.6s;"></div>
        <div class="absolute inset-12 rounded-full border border-sky-400/40" style="animation: radar-ring 3s linear infinite; animation-delay: 1.2s;"></div>
        <div class="absolute inset-18 rounded-full border border-sky-400/30" style="animation: radar-ring 3s linear infinite; animation-delay: 1.8s;"></div>
        <!-- Varredura rotativa -->
        <div class="absolute inset-0 rounded-full overflow-hidden">
          <div class="absolute top-1/2 left-1/2 w-1/2 h-0.5 origin-left" style="background: linear-gradient(90deg, transparent, rgba(56,189,248,0.8)); animation: radar-sweep 3s linear infinite; transform-origin: left center;"></div>
        </div>
        <!-- Centro do radar -->
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="w-3 h-3 rounded-full bg-sky-400 shadow-lg" style="box-shadow: 0 0 12px rgba(56,189,248,0.9);"></div>
        </div>
      </div>

      <!-- Partículas flutuantes -->
      <div class="absolute top-1/4 left-1/4 w-1.5 h-1.5 rounded-full bg-sky-400/60" style="animation: float-particle 6s ease-in-out infinite; animation-delay: 0s;"></div>
      <div class="absolute top-1/3 left-2/3 w-1 h-1 rounded-full bg-cyan-300/50" style="animation: float-particle 8s ease-in-out infinite; animation-delay: 2s;"></div>
      <div class="absolute top-2/3 left-1/3 w-1.5 h-1.5 rounded-full bg-blue-400/40" style="animation: float-particle 7s ease-in-out infinite; animation-delay: 4s;"></div>
      <div class="absolute top-1/2 left-1/2 w-1 h-1 rounded-full bg-sky-300/50" style="animation: float-particle 9s ease-in-out infinite; animation-delay: 1s;"></div>

      <!-- Linha de scan horizontal -->
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute left-0 right-0 h-px opacity-20" style="background: linear-gradient(90deg, transparent, rgba(56,189,248,0.8), transparent); animation: scan-line 6s linear infinite;"></div>
      </div>

      <!-- Logo CINDEC -->
      <div class="relative z-10 flex items-center gap-3 p-10">
        <div class="w-12 h-12 rounded-2xl border border-sky-400/40 flex items-center justify-center shadow-lg" style="background: rgba(0,60,150,0.4); backdrop-filter: blur(8px);">
          <svg class="w-7 h-7 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <div>
          <span class="text-xl font-extrabold text-white tracking-widest">CINDEC</span>
          <p class="text-xs text-sky-300/70 leading-none mt-0.5 tracking-wide">Centro de Inteligência em Defesa Civil — MG</p>
        </div>
      </div>

      <!-- Conteúdo central -->
      <div class="relative z-10 p-10 pb-16">
        <div class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 mb-6 border border-sky-400/30" style="background: rgba(56,189,248,0.1); backdrop-filter: blur(6px);">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          <span class="text-xs text-sky-200/90 font-medium tracking-wide">Sistema Operacional Ativo — MG-SecureNet</span>
        </div>

        <h2 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight mb-5">
          Inteligência tecnológica<br/>
          <span style="background: linear-gradient(90deg, #38bdf8, #67e8f9); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">para a Defesa Civil</span>
        </h2>
        <p class="text-sky-200/70 text-base mb-10 leading-relaxed max-w-md">
          Gerencie ativos tecnológicos, equipamentos operacionais e missões de defesa civil em uma plataforma segura, integrada e de alta performance.
        </p>

        <div class="space-y-3.5">
          <div v-for="item in beneficios" :key="item.texto" class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 border border-sky-400/30" style="background: rgba(56,189,248,0.15);">
              <svg class="w-4 h-4 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <span class="text-white/80 text-sm font-medium">{{ item.texto }}</span>
          </div>
        </div>

        <div class="flex gap-8 mt-10 pt-8 border-t border-white/10">
          <div>
            <p class="text-2xl font-extrabold text-white">+850</p>
            <p class="text-xs text-sky-300/55 mt-0.5">Ativos cadastrados</p>
          </div>
          <div>
            <p class="text-2xl font-extrabold text-white">99,2%</p>
            <p class="text-xs text-sky-300/55 mt-0.5">Disponibilidade do sistema</p>
          </div>
          <div>
            <p class="text-2xl font-extrabold text-white">24/7</p>
            <p class="text-xs text-sky-300/55 mt-0.5">Monitoramento contínuo</p>
          </div>
        </div>
      </div>

      <div class="relative z-10 px-10 pb-6 text-sky-400/30 text-xs tracking-wider">
        &#169; 2026 CINDEC/MG &middot; Sistema de Gestão Operacional em Defesa Civil
      </div>
    </div>

    <!-- Painel direito — Formulário de acesso -->
    <div class="flex-1 flex flex-col items-center justify-center bg-slate-50 px-6 py-12 sm:px-12">
      <!-- Mobile logo -->
      <div class="lg:hidden flex flex-col items-center mb-10">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg mb-4" style="background: linear-gradient(135deg, #0c3b6e, #1d6fb8);">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-widest">CINDEC</h1>
        <p class="text-sm text-gray-400 mt-1 text-center">Centro de Inteligência em Defesa Civil — MG</p>
      </div>

      <div class="w-full max-w-md">
        <!-- Cabeçalho do formulário -->
        <div class="mb-8">
          <div class="flex items-center gap-2 mb-3">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #0c3b6e, #1d6fb8);">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <span class="text-xs font-bold text-blue-800 uppercase tracking-widest">Acesso Seguro</span>
          </div>
          <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Portal Operacional</h2>
          <p class="text-gray-500 mt-2 text-sm leading-relaxed">
            Autentique suas credenciais para acessar o sistema de gestão integrada da Defesa Civil de Minas Gerais.
          </p>
        </div>

        <div v-if="status" class="mb-5 flex items-center gap-2.5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl">
          <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="text-sm text-emerald-700">{{ status }}</span>
        </div>

        <form @submit.prevent="enviar" class="space-y-5">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              E-mail institucional <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <input
                v-model="form.email"
                type="email"
                placeholder="usuario@cindec.mg.gov.br"
                autocomplete="email"
                :class="['w-full pl-11 pr-4 py-3.5 rounded-xl border text-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent', form.errors.email ? 'border-red-400 bg-red-50 text-red-900' : 'border-gray-200 bg-white text-gray-900 hover:border-gray-300 shadow-sm']"
              />
            </div>
            <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-600">{{ form.errors.email }}</p>
          </div>

          <div>
            <div class="flex items-center justify-between mb-2">
              <label class="text-sm font-semibold text-gray-700">Senha de acesso <span class="text-red-500">*</span></label>
              <Link :href="route('password.request')" class="text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors">
                Esqueci minha senha
              </Link>
            </div>
            <div class="relative">
              <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              <input
                v-model="form.password"
                :type="mostrarSenha ? 'text' : 'password'"
                placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;"
                autocomplete="current-password"
                :class="['w-full pl-11 pr-11 py-3.5 rounded-xl border text-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent', form.errors.password ? 'border-red-400 bg-red-50 text-red-900' : 'border-gray-200 bg-white text-gray-900 hover:border-gray-300 shadow-sm']"
              />
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
              <div class="w-5 h-5 rounded-md border-2 border-gray-300 bg-white peer-checked:bg-blue-700 peer-checked:border-blue-700 transition-all flex items-center justify-center">
                <svg v-if="form.lembrar" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
              </div>
            </div>
            <span class="text-sm text-gray-600 select-none">Manter sessao ativa neste dispositivo</span>
          </label>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full flex items-center justify-center gap-2.5 py-4 px-6 rounded-xl text-sm font-bold text-white transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 disabled:opacity-60 disabled:cursor-not-allowed hover:opacity-95"
            style="background: linear-gradient(135deg, #0c3b6e 0%, #1a5fa8 50%, #2176d2 100%); box-shadow: 0 4px 20px rgba(21,95,168,0.45);"
          >
            <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span>{{ form.processing ? 'Autenticando...' : 'Acessar Sistema CINDEC' }}</span>
          </button>
        </form>

        <div class="flex items-center gap-3 my-6">
          <div class="flex-1 h-px bg-gray-200"></div>
          <span class="text-xs text-gray-400 font-medium">ou</span>
          <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <p class="text-center text-sm text-gray-500">
          Sem acesso ao sistema?&nbsp;
          <Link :href="route('cadastro')" class="text-blue-700 font-semibold hover:text-blue-900 transition-colors">
            Solicitar credenciais
          </Link>
        </p>

        <div class="flex items-center justify-center gap-2 mt-8 text-xs text-gray-400">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          <span>Acesso criptografado SSL/TLS &middot; Rede Governamental Segura</span>
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
  { texto: 'Controle total de equipamentos e ativos tecnológicos' },
  { texto: 'Gestão de vendas e aquisições de tecnologia operacional' },
  { texto: 'Monitoramento de inventário em tempo real' },
  { texto: 'Controle de acesso por perfil e nível de segurança' },
];
</script>

<style scoped>
@keyframes radar-ring {
  0%   { transform: scale(0.4); opacity: 0.8; }
  100% { transform: scale(1.2); opacity: 0; }
}
@keyframes radar-sweep {
  0%   { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
@keyframes float-particle {
  0%, 100% { transform: translateY(0px) translateX(0px); opacity: 0.6; }
  33%       { transform: translateY(-18px) translateX(8px); opacity: 1; }
  66%       { transform: translateY(10px) translateX(-6px); opacity: 0.4; }
}
@keyframes scan-line {
  0%   { top: -2px; opacity: 0; }
  10%  { opacity: 1; }
  90%  { opacity: 1; }
  100% { top: 100%; opacity: 0; }
}
</style>
