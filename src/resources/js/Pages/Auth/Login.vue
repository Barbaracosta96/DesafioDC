<template>
  <div class="login-root">

    <!-- ══ PAINEL ESQUERDO — Marca ══ -->
    <aside class="brand-panel">
      <!-- Malha geométrica decorativa (SVG estático, zero JS) -->
      <div class="brand-geo" aria-hidden="true">
        <svg class="geo-svg" viewBox="0 0 520 700" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
          <!-- Círculos concêntricos sutis -->
          <circle cx="260" cy="350" r="260" stroke="rgba(255,255,255,0.05)" stroke-width="1"/>
          <circle cx="260" cy="350" r="200" stroke="rgba(255,255,255,0.05)" stroke-width="1"/>
          <circle cx="260" cy="350" r="140" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
          <circle cx="260" cy="350" r="80"  stroke="rgba(255,255,255,0.08)" stroke-width="1"/>
          <!-- Grade pontilhada sutil -->
          <line x1="0" y1="175" x2="520" y2="175" stroke="rgba(255,255,255,0.04)" stroke-width="1" stroke-dasharray="4 8"/>
          <line x1="0" y1="350" x2="520" y2="350" stroke="rgba(255,255,255,0.04)" stroke-width="1" stroke-dasharray="4 8"/>
          <line x1="0" y1="525" x2="520" y2="525" stroke="rgba(255,255,255,0.04)" stroke-width="1" stroke-dasharray="4 8"/>
          <line x1="130" y1="0" x2="130" y2="700" stroke="rgba(255,255,255,0.04)" stroke-width="1" stroke-dasharray="4 8"/>
          <line x1="260" y1="0" x2="260" y2="700" stroke="rgba(255,255,255,0.04)" stroke-width="1" stroke-dasharray="4 8"/>
          <line x1="390" y1="0" x2="390" y2="700" stroke="rgba(255,255,255,0.04)" stroke-width="1" stroke-dasharray="4 8"/>
          <!-- Hexágono decorativo central -->
          <polygon points="260,140 320,175 320,245 260,280 200,245 200,175"
            fill="none" stroke="rgba(255,255,255,0.10)" stroke-width="1.5"/>
          <polygon points="260,155 308,182 308,238 260,265 212,238 212,182"
            fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.07)" stroke-width="1"/>
          <!-- Acento ponto superior -->
          <circle cx="420" cy="80"  r="3" fill="rgba(165,180,252,0.5)"/>
          <circle cx="80"  cy="560" r="2" fill="rgba(165,180,252,0.4)"/>
          <circle cx="460" cy="480" r="4" fill="rgba(196,181,253,0.35)"/>
          <!-- Linha diagonal elegante -->
          <line x1="0" y1="600" x2="180" y2="420" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
          <line x1="380" y1="0"  x2="520" y2="160" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
        </svg>
      </div>

      <!-- Conteúdo da marca -->
      <div class="brand-content">
        <!-- Logo -->
        <div class="brand-logo">
          <div class="brand-shield">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <div>
            <span class="brand-name">CINDEC</span>
            <span class="brand-acronym">Centro de Inteligência em Defesa Civil</span>
          </div>
        </div>

        <!-- Headline -->
        <div class="brand-headline">
          <h2 class="brand-h2">Gestão Operacional<br/><em>em tempo real</em></h2>
          <p class="brand-body">Plataforma institucional para controle de ativos tecnológicos, ordens de fornecimento e entidades da Defesa Civil de Minas Gerais.</p>
        </div>

        <!-- Feature chips -->
        <ul class="feature-list">
          <li class="feature-item">
            <span class="feat-dot"></span>
            <span>Controle de ativos e inventário</span>
          </li>
          <li class="feature-item">
            <span class="feat-dot"></span>
            <span>Ordens de fornecimento integradas</span>
          </li>
          <li class="feature-item">
            <span class="feat-dot"></span>
            <span>Gestão de acesso por perfil (ACL)</span>
          </li>
          <li class="feature-item">
            <span class="feat-dot"></span>
            <span>Dashboard operacional em tempo real</span>
          </li>
        </ul>

        <!-- Badge de segurança -->
        <div class="brand-badge">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          <span>Conexão criptografada · SSL/TLS ativo</span>
          <span class="badge-dot"></span>
          <span class="badge-status">ONLINE</span>
        </div>
      </div>

      <!-- Rodapé da marca -->
      <p class="brand-footer">© 2026 CINDEC/MG · Governo de Minas Gerais</p>
    </aside>

    <!-- ══ PAINEL DIREITO — Formulário ══ -->
    <main class="form-panel">

      <!-- Barra de status superior (mobile only + topo do form panel) -->
      <div class="form-topbar">
        <span class="topbar-time">{{ horaAtual }}</span>
        <span class="topbar-sep">·</span>
        <span class="topbar-sys">SISTEMA OPERACIONAL</span>
        <span class="topbar-pulse"></span>
      </div>

      <!-- Wrapper central do formulário -->
      <div class="form-center">

        <!-- Cabeçalho -->
        <div class="form-head">
          <h1 class="form-title">Acesso ao Portal</h1>
          <p class="form-desc">Autentique suas credenciais para continuar</p>
        </div>

        <!-- Status message -->
        <div v-if="status" class="msg-ok">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ status }}
        </div>

        <!-- Formulário -->
        <form @submit.prevent="enviar" class="form-body" novalidate>

          <!-- Email -->
          <div class="field-wrap">
            <label class="field-label" for="email">E-mail</label>
            <div class="input-wrap" :class="{ 'input-error': form.errors.email }">
              <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
              <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="usuario@base.com"
                autocomplete="email"
                class="cin-input"
              />
            </div>
            <p v-if="form.errors.email" class="field-err">{{ form.errors.email }}</p>
          </div>

          <!-- Senha -->
          <div class="field-wrap">
            <div class="label-row">
              <label class="field-label" for="senha">Senha</label>
              <Link :href="route('password.request')" class="forgot-link">Esqueci a senha</Link>
            </div>
            <div class="input-wrap" :class="{ 'input-error': form.errors.password }">
              <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              <input
                id="senha"
                v-model="form.password"
                :type="mostrarSenha ? 'text' : 'password'"
                placeholder="••••••••••"
                autocomplete="current-password"
                class="cin-input"
              />
              <button type="button" class="eye-btn" @click="mostrarSenha = !mostrarSenha" tabindex="-1" aria-label="Alternar visibilidade">
                <svg v-if="!mostrarSenha" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
              </button>
            </div>
            <p v-if="form.errors.password" class="field-err">{{ form.errors.password }}</p>
          </div>

          <!-- Lembrar -->
          <label class="remember-row">
            <div class="checkbox-wrap">
              <input type="checkbox" v-model="form.lembrar" class="sr-only peer" />
              <div class="checkbox-box">
                <svg v-if="form.lembrar" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
            </div>
            <span class="remember-text">Manter sessão ativa</span>
          </label>

          <!-- Botão -->
          <button type="submit" :disabled="form.processing" class="submit-btn">
            <span v-if="form.processing" class="btn-inner">
              <svg class="spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
              </svg>
              Autenticando...
            </span>
            <span v-else class="btn-inner">
              Acessar Portal
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </span>
          </button>
        </form>

        <!-- Divisor -->
        <div class="form-divider"><span>ou</span></div>

        <div class="card-footer">
          <span>Sem credenciais?</span>
          <Link :href="route('cadastro')" class="register-link">Solicitar acesso</Link>
        </div>

        <!-- Selos de segurança -->
        <div class="security-row">
          <div class="sec-badge">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span>Acesso seguro</span>
          </div>
          <div class="sec-badge">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Dados criptografados</span>
          </div>
          <div class="sec-badge">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Certificado SSL</span>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

defineProps({ status: { type: String, default: null } });

const form = useForm({ email: '', password: '', lembrar: false });
const mostrarSenha = ref(false);
const horaAtual = ref('');
const enviar = () => form.post(route('login.store'));

let timer;
const atualizarHora = () => {
  const now = new Date();
  horaAtual.value = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
};
onMounted(() => { atualizarHora(); timer = setInterval(atualizarHora, 1000); });
onUnmounted(() => clearInterval(timer));
</script>

<style scoped>
/* ══ ROOT — Split layout ══ */
.login-root {
  min-height: 100vh;
  display: flex;
  font-family: 'Inter', system-ui, sans-serif;
  background: #f5f3ff;
}

/* ══ PAINEL ESQUERDO ══ */
.brand-panel {
  display: none; /* oculto no mobile */
  flex-direction: column;
  justify-content: space-between;
  width: 45%;
  min-height: 100vh;
  position: relative;
  overflow: hidden;
  background: linear-gradient(145deg, #312e81 0%, #4f46e5 45%, #6d28d9 100%);
  padding: 44px 48px;
}

@media (min-width: 960px) {
  .brand-panel { display: flex; }
}

/* Fundo geométrico */
.brand-geo {
  position: absolute; inset: 0; pointer-events: none;
}
.geo-svg { width: 100%; height: 100%; }

/* Conteúdo marca */
.brand-content {
  position: relative; z-index: 2;
  display: flex; flex-direction: column; gap: 40px;
  flex: 1;
}

/* Logo */
.brand-logo {
  display: flex; align-items: center; gap: 16px;
}
.brand-shield {
  width: 52px; height: 52px; border-radius: 16px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  background: rgba(255,255,255,0.15);
  border: 1.5px solid rgba(255,255,255,0.25);
  box-shadow: 0 8px 32px rgba(0,0,0,0.2);
}
.brand-shield svg { width: 28px; height: 28px; color: white; }
.brand-name {
  display: block;
  font-size: 26px; font-weight: 900; letter-spacing: 0.2em;
  color: white;
}
.brand-acronym {
  display: block; font-size: 10px; color: rgba(255,255,255,0.55);
  letter-spacing: 0.06em; margin-top: 2px;
}

/* Headline */
.brand-headline { }
.brand-h2 {
  font-size: 32px; font-weight: 800; color: white;
  line-height: 1.2; margin-bottom: 14px;
  letter-spacing: -0.01em;
}
.brand-h2 em {
  font-style: normal;
  background: linear-gradient(90deg, #c4b5fd, #a5f3fc);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.brand-body { font-size: 14px; color: rgba(255,255,255,0.65); line-height: 1.7; }

/* Feature list */
.feature-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; }
.feature-item {
  display: flex; align-items: center; gap: 12px;
  font-size: 13px; color: rgba(255,255,255,0.80);
}
.feat-dot {
  width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
  background: #a5f3fc;
  box-shadow: 0 0 8px rgba(165,243,252,0.6);
}

/* Badge de segurança */
.brand-badge {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 9px 16px; border-radius: 100px;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.18);
  font-size: 11px; color: rgba(255,255,255,0.70);
  letter-spacing: 0.03em;
  align-self: flex-start;
}
.brand-badge svg { width: 13px; height: 13px; color: #a5f3fc; flex-shrink: 0; }
.badge-dot {
  width: 5px; height: 5px; border-radius: 50%;
  background: #34d399;
  box-shadow: 0 0 6px #34d399;
  animation: blink 2.5s ease-in-out infinite;
}
.badge-status { font-weight: 700; color: #34d399; letter-spacing: 0.1em; font-size: 10px; }
@keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0.3; } }

/* Footer da marca */
.brand-footer {
  position: relative; z-index: 2;
  font-size: 10px; color: rgba(255,255,255,0.30);
  letter-spacing: 0.06em; text-transform: uppercase;
}

/* ══ PAINEL DIREITO ══ */
.form-panel {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #ffffff;
  min-height: 100vh;
  position: relative;
}

/* Topbar */
.form-topbar {
  display: flex; align-items: center; gap: 8px;
  padding: 12px 40px;
  border-bottom: 1px solid #f1f5f9;
  font-size: 11px; letter-spacing: 0.06em; text-transform: uppercase;
  color: #94a3b8;
  background: #fafafa;
}
.topbar-time { font-variant-numeric: tabular-nums; color: #64748b; font-weight: 600; }
.topbar-sep { color: #cbd5e1; }
.topbar-sys { color: #64748b; }
.topbar-pulse {
  margin-left: auto;
  width: 7px; height: 7px; border-radius: 50%;
  background: #34d399;
  box-shadow: 0 0 0 2px rgba(52,211,153,0.25);
  animation: blink 2.5s ease-in-out infinite;
}

/* Centro do formulário */
.form-center {
  flex: 1; display: flex; flex-direction: column; justify-content: center;
  padding: 48px 40px;
  max-width: 440px;
  width: 100%;
  margin: 0 auto;
}

/* Cabeçalho */
.form-head { margin-bottom: 32px; }
.form-title {
  font-size: 26px; font-weight: 800; color: #0f172a;
  letter-spacing: -0.02em; margin-bottom: 6px;
}
.form-desc { font-size: 13px; color: #64748b; line-height: 1.5; }

/* Status */
.msg-ok {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 16px; border-radius: 12px; margin-bottom: 20px;
  background: #f0fdf4; border: 1px solid #bbf7d0;
  font-size: 13px; color: #15803d;
}
.msg-ok svg { width: 16px; height: 16px; flex-shrink: 0; color: #16a34a; }

/* Campos */
.form-body { display: flex; flex-direction: column; gap: 18px; }
.field-wrap { display: flex; flex-direction: column; gap: 7px; }
.field-label {
  font-size: 12px; font-weight: 700; color: #374151;
  letter-spacing: 0.04em;
}
.label-row { display: flex; justify-content: space-between; align-items: center; }
.forgot-link { font-size: 12px; color: #4f46e5; font-weight: 600; text-decoration: none; transition: color .15s; }
.forgot-link:hover { color: #7c3aed; text-decoration: underline; }

.input-wrap {
  display: flex; align-items: center; position: relative;
  background: #f8fafc;
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  transition: border-color .15s, box-shadow .15s;
}
.input-wrap:focus-within {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.10);
  background: #fff;
}
.input-wrap.input-error { border-color: #fca5a5; background: #fff5f5; }
.input-icon {
  width: 16px; height: 16px; color: #9ca3af;
  position: absolute; left: 14px; pointer-events: none;
  flex-shrink: 0;
}
.cin-input {
  flex: 1; background: transparent; border: none; outline: none;
  padding: 13px 13px 13px 42px;
  font-size: 14px; color: #111827;
  font-family: inherit;
}
.cin-input::placeholder { color: #d1d5db; }
.eye-btn {
  padding: 0 14px; background: none; border: none; cursor: pointer;
  color: #9ca3af; transition: color .15s; flex-shrink: 0;
}
.eye-btn:hover { color: #6366f1; }
.eye-btn svg { width: 16px; height: 16px; display: block; }
.field-err { font-size: 12px; color: #ef4444; display: flex; align-items: center; gap: 4px; }

/* Lembrar */
.remember-row { display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none; }
.checkbox-wrap { position: relative; flex-shrink: 0; }
.checkbox-box {
  width: 18px; height: 18px; border-radius: 5px;
  border: 1.5px solid #d1d5db;
  background: #f9fafb;
  display: flex; align-items: center; justify-content: center;
  transition: all .15s;
}
input:checked ~ .checkbox-box,
.remember-row input[type=checkbox]:checked + .checkbox-box {
  background: #4f46e5; border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}
.checkbox-box svg { width: 11px; height: 11px; color: white; }
.remember-text { font-size: 13px; color: #6b7280; }

/* Botão principal */
.submit-btn {
  width: 100%; padding: 15px 20px;
  background: linear-gradient(135deg, #4338ca 0%, #4f46e5 60%, #6366f1 100%);
  border: none; border-radius: 12px; cursor: pointer;
  font-family: inherit; font-size: 14px; font-weight: 700;
  color: white; letter-spacing: 0.03em;
  box-shadow: 0 4px 20px rgba(79,70,229,0.40), 0 1px 0 rgba(255,255,255,0.15) inset;
  transition: box-shadow .15s, transform .1s;
  position: relative; overflow: hidden;
}
.submit-btn:hover:not(:disabled) {
  box-shadow: 0 8px 28px rgba(79,70,229,0.55), 0 1px 0 rgba(255,255,255,0.15) inset;
  transform: translateY(-1px);
}
.submit-btn:active:not(:disabled) { transform: translateY(0); }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
.btn-inner { display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-inner svg { width: 16px; height: 16px; }
.spin { animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Divisor */
.form-divider {
  display: flex; align-items: center; gap: 12px;
  margin: 24px 0 16px;
  font-size: 12px; color: #d1d5db;
}
.form-divider::before,
.form-divider::after {
  content: ''; flex: 1; height: 1px; background: #f1f5f9;
}
.form-divider span { padding: 0 4px; }

/* Footer */
.card-footer {
  text-align: center;
  font-size: 13px; color: #6b7280;
  display: flex; align-items: center; justify-content: center; gap: 6px;
}
.register-link { color: #4f46e5; font-weight: 700; text-decoration: none; transition: color .15s; }
.register-link:hover { color: #7c3aed; text-decoration: underline; }

/* Selos de segurança */
.security-row {
  display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;
  margin-top: 28px; padding-top: 20px;
  border-top: 1px solid #f1f5f9;
}
.sec-badge {
  display: flex; align-items: center; gap: 5px;
  padding: 5px 10px; border-radius: 100px;
  background: #f8fafc; border: 1px solid #e2e8f0;
  font-size: 10px; color: #64748b; font-weight: 500;
}
.sec-badge svg { width: 11px; height: 11px; color: #6366f1; flex-shrink: 0; }

/* ══ RESPONSIVO MOBILE ══ */
@media (max-width: 959px) {
  .form-panel { min-height: 100vh; }
  .form-center { padding: 36px 24px; }
  .form-title { font-size: 22px; }
}
</style>

