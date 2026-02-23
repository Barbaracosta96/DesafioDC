<template>
  <AppLayout :titulo="editando ? 'Editar Usuário' : 'Novo Usuário'">
    <Head :title="editando ? 'Editar Usuário' : 'Novo Usuário'" />

    <div class="flex items-center gap-3 mb-6">
      <Botao variant="fantasma" size="sm" :href="route('usuarios.index')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Voltar
      </Botao>
      <h2 class="text-2xl font-bold text-gray-900">{{ editando ? 'Editar Usuário' : 'Novo Usuário' }}</h2>
    </div>

    <form @submit.prevent="salvar" class="space-y-6 max-w-2xl">
      <!-- Dados principais -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
        <h3 class="font-semibold text-gray-900">Informações Básicas</h3>
        <InputField label="Nome completo *" v-model="form.name" :erro="form.errors.name" placeholder="Nome do usuário" />
        <InputField label="E-mail *" v-model="form.email" type="email" :erro="form.errors.email" placeholder="email@exemplo.com" />

        <div>
          <InputField :label="editando ? 'Nova senha (deixe em branco para manter)' : 'Senha *'" v-model="form.password" type="password" :erro="form.errors.password" placeholder="Mínimo 8 caracteres" />
        </div>
        <div v-if="!editando || form.password">
          <InputField label="Confirmar senha" v-model="form.password_confirmation" type="password" placeholder="Repita a senha" />
        </div>
      </div>

      <!-- Perfil de acesso -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Perfil de Acesso</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <label v-for="role in roles" :key="role.id" class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors"
            :class="form.role === role.name ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
            <input type="radio" :value="role.name" v-model="form.role" class="mt-0.5 text-indigo-600" />
            <div>
              <p class="text-sm font-semibold text-gray-900">{{ nomeRole(role.name) }}</p>
              <p class="text-xs text-gray-500 mt-0.5">{{ descricaoRole(role.name) }}</p>
            </div>
          </label>
        </div>
        <p v-if="form.errors.role" class="text-xs text-red-500 mt-2">{{ form.errors.role }}</p>
      </div>

      <!-- Status -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-semibold text-gray-900">Status da Conta</p>
            <p class="text-sm text-gray-500 mt-0.5">Usuários inativos não conseguem fazer login</p>
          </div>
          <button type="button" role="switch" :aria-checked="form.ativo" @click="form.ativo = !form.ativo" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="form.ativo ? 'bg-indigo-600' : 'bg-gray-300'">
            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform" :class="form.ativo ? 'translate-x-6' : 'translate-x-1'" />
          </button>
        </div>
      </div>

      <div class="flex justify-end gap-3">
        <Botao variant="secundario" :href="route('usuarios.index')">Cancelar</Botao>
        <Botao type="submit" :carregando="form.processing">{{ editando ? 'Salvar Alterações' : 'Criar Usuário' }}</Botao>
      </div>
    </form>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Botao from '@/Components/Botao.vue';
import InputField from '@/Components/InputField.vue';

const props = defineProps({
  usuario: { type: Object, default: null },
  roles:   { type: Array,  default: () => [] },
});

const editando = computed(() => !!props.usuario?.id);

const form = useForm({
  name:                 props.usuario?.name  ?? '',
  email:                props.usuario?.email ?? '',
  password:             '',
  password_confirmation:'',
  role:                 props.usuario?.roles?.[0] ?? '',
  ativo:                props.usuario?.ativo  ?? true,
});

const salvar = () => {
  if (editando.value) {
    form.put(route('usuarios.update', props.usuario.id));
  } else {
    form.post(route('usuarios.store'));
  }
};

const nomeRole      = (r) => ({ admin: 'Administrador', editor: 'Editor', usuario: 'Usuário' }[r] ?? r);
const descricaoRole = (r) => ({
  admin:   'Acesso completo ao sistema',
  editor:  'Pode criar e editar dados',
  usuario: 'Somente visualização',
}[r] ?? '');
</script>
