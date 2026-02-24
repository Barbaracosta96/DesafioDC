<template>
  <AppLayout :page-title="isEdit ? 'Editar Usuário' : 'Novo Usuário'">
    <Head :title="isEdit ? 'Editar Usuário' : 'Novo Usuário'" />

    <div class="max-w-2xl mx-auto">
      <div class="flex items-center gap-3 mb-6">
        <Link :href="route('users.index')" class="p-2 rounded-xl hover:bg-gray-100 text-gray-400 transition">
          <ArrowLeftIcon class="w-4 h-4" />
        </Link>
        <div>
          <h2 class="text-xl font-bold text-gray-800">{{ isEdit ? 'Editar Usuário' : 'Novo Usuário' }}</h2>
          <p class="text-sm text-gray-400">{{ isEdit ? `Editando ${user.name}` : 'Preencha os dados do novo colaborador' }}</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-50 space-y-4">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-xs font-medium text-gray-500 mb-1 block">Nome completo *</label>
            <input v-model="form.name" type="text" placeholder="Ex: Maria Silva"
              class="w-full px-3 py-2.5 bg-gray-50 border rounded-xl text-sm outline-none transition"
              :class="form.errors.name ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/20'"
            />
            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
          </div>
          <div>
            <label class="text-xs font-medium text-gray-500 mb-1 block">E-mail *</label>
            <input v-model="form.email" type="email" placeholder="usuario@email.com"
              class="w-full px-3 py-2.5 bg-gray-50 border rounded-xl text-sm outline-none transition"
              :class="form.errors.email ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/20'"
            />
            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
          </div>
          <div>
            <label class="text-xs font-medium text-gray-500 mb-1 block">
              Senha {{ isEdit ? '(deixe em branco para manter)' : '*' }}
            </label>
            <input v-model="form.password" type="password" placeholder="Mín. 8 caracteres"
              class="w-full px-3 py-2.5 bg-gray-50 border rounded-xl text-sm outline-none transition"
              :class="form.errors.password ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/20'"
            />
            <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
          </div>
          <div>
            <label class="text-xs font-medium text-gray-500 mb-1 block">Confirmar senha</label>
            <input v-model="form.password_confirmation" type="password" placeholder="Repita a senha"
              class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 transition"
            />
          </div>
        </div>

        <div>
          <label class="text-xs font-medium text-gray-500 mb-2 block">Papel (ACL)</label>
          <div class="flex gap-3">
            <label
              v-for="role in roles"
              :key="role"
              class="flex items-center gap-2 px-4 py-2.5 rounded-xl border cursor-pointer transition"
              :class="form.role === role
                ? 'border-violet-400 bg-violet-50 text-violet-700'
                : 'border-gray-200 bg-gray-50 text-gray-600 hover:border-gray-300'"
            >
              <input type="radio" v-model="form.role" :value="role" class="sr-only" />
              <span class="text-sm font-medium capitalize">{{ role }}</span>
            </label>
          </div>
          <p v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</p>
        </div>

        <div class="flex gap-3 pt-2">
          <Link :href="route('users.index')" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
            Cancelar
          </Link>
          <button type="submit" :disabled="form.processing" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition shadow-sm">
            {{ form.processing ? 'Salvando...' : isEdit ? 'Salvar Alterações' : 'Criar Usuário' }}
          </button>
        </div>

      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Pages/Layouts/AppLayout.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  user:  { type: Object, default: null },
  roles: { type: Array, default: () => ['admin', 'editor', 'user'] },
});

const isEdit = computed(() => !!props.user);

const form = useForm({
  name:                  props.user?.name || '',
  email:                 props.user?.email || '',
  password:              '',
  password_confirmation: '',
  role:                  props.user?.roles?.[0]?.name || 'user',
});

function submit() {
  if (isEdit.value) {
    form.put(route('users.update', props.user.id));
  } else {
    form.post(route('users.store'));
  }
}
</script>
