<template>
  <Head title="Redefinir senha" />
  <div class="min-h-screen flex items-center justify-center bg-[#160e35] relative overflow-hidden">

    <div class="absolute top-[-10%] left-[-5%] w-[500px] h-[500px] bg-violet-700/30 rounded-full blur-[120px] animate-blob"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-[450px] h-[450px] bg-purple-600/25 rounded-full blur-[100px] animate-blob animation-delay-2000"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] h-[300px] bg-indigo-500/15 rounded-full blur-[80px] animate-blob animation-delay-4000"></div>

    <div class="absolute inset-0 opacity-[0.04]"
      style="background-image: linear-gradient(rgba(255,255,255,0.8) 1px, transparent 1px), linear-gradient(to right, rgba(255,255,255,0.8) 1px, transparent 1px); background-size: 40px 40px;">
    </div>

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div v-for="p in particles" :key="p.id" class="absolute rounded-full bg-violet-400/20"
        :style="`width:${p.size}px; height:${p.size}px; left:${p.x}%; top:${p.y}%; animation: floatUp ${p.duration}s ease-in-out ${p.delay}s infinite;`">
      </div>
    </div>

    <div class="relative z-10 w-full max-w-md mx-4">
      <div class="absolute -inset-0.5 bg-gradient-to-r from-violet-600 to-purple-600 rounded-3xl blur opacity-30"></div>
      <div class="relative bg-white/[0.07] backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl">

        <!-- Logo -->
        <div class="flex flex-col items-center text-center mb-7">
          <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-violet-500/30 mb-3">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z" />
            </svg>
          </div>
          <span class="text-xl font-bold text-white tracking-wide">Dabang</span>
          <p class="text-[10px] text-violet-300 font-medium tracking-widest uppercase">SaaS Platform</p>
        </div>

        <!-- Icon & Heading -->
        <div class="flex flex-col items-center text-center mb-7">
          <div class="w-14 h-14 bg-violet-500/20 border border-violet-500/30 rounded-2xl flex items-center justify-center mb-4">
            <KeyIcon class="w-7 h-7 text-violet-400" />
          </div>
          <h1 class="text-2xl font-bold text-white leading-tight">Redefinir senha</h1>
          <p class="text-sm text-white/50 mt-2">Insira sua nova senha nos campos abaixo.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
          <!-- Email readonly -->
          <div>
            <label class="block text-sm font-medium text-white/70 mb-1.5">E-mail</label>
            <div class="relative">
              <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-white/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              </div>
              <input v-model="form.email" type="email" readonly
                class="w-full pl-10 pr-4 py-3 bg-white/[0.03] border border-white/5 rounded-xl text-sm text-white/40 cursor-not-allowed" />
            </div>
            <p v-if="form.errors.email" class="mt-1 text-xs text-red-400">{{ form.errors.email }}</p>
          </div>

          <!-- New Password -->
          <div>
            <label class="block text-sm font-medium text-white/70 mb-1.5">Nova senha</label>
            <div class="relative">
              <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-white/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              </div>
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" autocomplete="new-password" placeholder="Minimo 8 caracteres"
                class="w-full pl-10 pr-11 py-3 bg-white/5 border border-white/10 rounded-xl text-sm text-white placeholder-white/25 outline-none transition-all focus:border-violet-500/60 focus:bg-white/[0.08] focus:ring-1 focus:ring-violet-500/30"
                :class="form.errors.password ? 'border-red-500/50' : ''" />
              <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-white/30 hover:text-white/60 transition" @click="showPassword = !showPassword">
                <EyeIcon v-if="!showPassword" class="w-4 h-4" />
                <EyeSlashIcon v-else class="w-4 h-4" />
              </button>
            </div>
            <p v-if="form.errors.password" class="mt-1 text-xs text-red-400">{{ form.errors.password }}</p>
          </div>

          <!-- Confirm Password -->
          <div>
            <label class="block text-sm font-medium text-white/70 mb-1.5">Confirmar senha</label>
            <div class="relative">
              <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-white/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              </div>
              <input v-model="form.password_confirmation" :type="showPassword ? 'text' : 'password'" autocomplete="new-password" placeholder="Repita a senha"
                class="w-full pl-10 pr-4 py-3 bg-white/5 border border-white/10 rounded-xl text-sm text-white placeholder-white/25 outline-none transition-all focus:border-violet-500/60 focus:bg-white/[0.08] focus:ring-1 focus:ring-violet-500/30" />
            </div>
          </div>

          <button type="submit" :disabled="form.processing"
            class="w-full py-3 mt-2 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500 disabled:opacity-50 text-white font-semibold rounded-xl transition-all text-sm shadow-lg shadow-violet-500/25">
            <span v-if="form.processing" class="flex items-center justify-center gap-2">
              <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              Redefinindo...
            </span>
            <span v-else>Redefinir senha</span>
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-white/40">
          <Link :href="route('login')" class="inline-flex items-center gap-1.5 text-violet-400 hover:text-violet-300 transition font-medium">
            <ArrowLeftIcon class="w-3.5 h-3.5" />
            Voltar ao login
          </Link>
        </p>
      </div>
    </div>

    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/20 text-xs">2026 Dabang SaaS - v2.0</div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EyeIcon, EyeSlashIcon, KeyIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  token: { type: String, required: true },
  email: { type: String, required: true },
});

const showPassword = ref(false);
const form = useForm({
  token: props.token, email: props.email, password: '', password_confirmation: '',
});
const particles = Array.from({ length: 12 }, (_, i) => ({
  id: i, size: Math.random() * 8 + 3, x: Math.random() * 100, y: Math.random() * 100,
  duration: Math.random() * 8 + 6, delay: Math.random() * 5,
}));

function submit() {
  form.post(route('password.update'), { onFinish: () => form.reset('password', 'password_confirmation') });
}
</script>

<style>
@keyframes blob {
  0%, 100% { transform: translate(0,0) scale(1); }
  33% { transform: translate(30px,-50px) scale(1.1); }
  66% { transform: translate(-20px,20px) scale(0.9); }
}
@keyframes floatUp {
  0%, 100% { transform: translateY(0) scale(1); opacity: 0.3; }
  50% { transform: translateY(-45px) scale(1.3); opacity: 0.7; }
}
.animate-blob { animation: blob 10s infinite ease-in-out; }
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }
</style>
