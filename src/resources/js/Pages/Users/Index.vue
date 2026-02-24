<template>
  <AppLayout page-title="Usuários">
    <Head title="Usuários" />

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Gerenciar Usuários</h2>
        <p class="text-sm text-gray-400 mt-0.5">{{ users.total }} usuário(s) registrado(s)</p>
      </div>
      <Link
        :href="route('users.create')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-xl transition shadow-sm"
      >
        <PlusIcon class="w-4 h-4" />
        Novo Usuário
      </Link>
    </div>

    <!-- Flash -->
    <div v-if="$page.props.flash?.success" class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl">
      {{ $page.props.flash.success }}
    </div>

    <!-- Search -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-50 mb-4">
      <form @submit.prevent="applyFilters" class="flex gap-3">
        <div class="flex items-center gap-2 flex-1 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
          <MagnifyingGlassIcon class="w-4 h-4 text-gray-400 flex-shrink-0" />
          <input v-model="filterForm.search" type="text" placeholder="Buscar por nome ou e-mail..." class="flex-1 bg-transparent text-sm outline-none" />
        </div>
        <button type="submit" class="px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-xl transition">Filtrar</button>
      </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 overflow-hidden">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-gray-50 text-gray-400 text-xs font-semibold uppercase tracking-wide">
            <th class="text-left px-5 py-3.5">Usuário</th>
            <th class="text-left px-5 py-3.5">E-mail</th>
            <th class="text-center px-5 py-3.5">Papel</th>
            <th class="text-left px-5 py-3.5">Desde</th>
            <th class="text-right px-5 py-3.5">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 transition">
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center text-violet-700 font-bold text-xs flex-shrink-0">
                  {{ user.name[0].toUpperCase() }}
                </div>
                <span class="font-medium text-gray-700">{{ user.name }}</span>
              </div>
            </td>
            <td class="px-5 py-3.5 text-gray-500">{{ user.email }}</td>
            <td class="px-5 py-3.5 text-center">
              <span
                v-for="role in user.roles"
                :key="role.name"
                class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                :class="{
                  'bg-violet-100 text-violet-700': role.name === 'admin',
                  'bg-blue-100 text-blue-700':   role.name === 'editor',
                  'bg-gray-100 text-gray-600':   role.name === 'user',
                }"
              >
                {{ role.name }}
              </span>
              <span v-if="!user.roles?.length" class="text-gray-300 text-xs">—</span>
            </td>
            <td class="px-5 py-3.5 text-gray-400 text-xs">{{ formatDate(user.created_at) }}</td>
            <td class="px-5 py-3.5">
              <div class="flex items-center justify-end gap-1">
                <Link
                  :href="route('users.edit', user.id)"
                  class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition"
                >
                  <PencilSquareIcon class="w-4 h-4" />
                </Link>
                <button
                  @click="confirmDelete(user)"
                  class="p-1.5 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition"
                >
                  <TrashIcon class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="!users.data.length">
            <td colspan="5" class="px-5 py-12 text-center">
              <UsersIcon class="w-10 h-10 text-gray-300 mx-auto mb-2" />
              <p class="text-gray-400 text-sm">Nenhum usuário encontrado</p>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="users.last_page > 1" class="flex items-center justify-between px-5 py-3 border-t border-gray-50">
        <p class="text-xs text-gray-400">Mostrando {{ users.from }}–{{ users.to }} de {{ users.total }}</p>
        <div class="flex gap-1">
          <Link
            v-for="page in users.links"
            :key="page.label"
            :href="page.url || '#'"
            class="px-3 py-1.5 text-xs rounded-lg transition"
            :class="page.active ? 'bg-violet-600 text-white font-semibold' : page.url ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-gray-50 text-gray-300 cursor-not-allowed'"
            v-html="page.label"
          />
        </div>
      </div>
    </div>

    <!-- Delete modal -->
    <Teleport to="body">
      <div v-if="userToDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
              <ExclamationTriangleIcon class="w-5 h-5 text-red-500" />
            </div>
            <h3 class="text-base font-bold text-gray-800">Excluir Usuário?</h3>
          </div>
          <p class="text-sm text-gray-500 mb-5">
            Tem certeza que deseja excluir <strong>{{ userToDelete.name }}</strong>? Esta ação é irreversível.
          </p>
          <div class="flex gap-3">
            <button @click="userToDelete = null" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
              Cancelar
            </button>
            <button @click="deleteUser" :disabled="deleteForm.processing" class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition">
              Excluir
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Pages/Layouts/AppLayout.vue';
import {
  PlusIcon, MagnifyingGlassIcon, PencilSquareIcon,
  TrashIcon, UsersIcon, ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  users:   { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
});

const filterForm = reactive({ search: props.filters.search || '' });
const userToDelete = ref(null);
const deleteForm = useForm({});

function formatDate(date) {
  return new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' });
}

function applyFilters() {
  router.get(route('users.index'), filterForm, { preserveState: true, replace: true });
}

function confirmDelete(user) { userToDelete.value = user; }

function deleteUser() {
  deleteForm.delete(route('users.destroy', userToDelete.value.id), {
    onSuccess: () => { userToDelete.value = null; },
  });
}
</script>
