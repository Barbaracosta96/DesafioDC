<template>
  <AppLayout :page-title="isEdit ? 'Editar Cliente' : 'Novo Cliente'">
    <Head :title="isEdit ? 'Editar Cliente' : 'Novo Cliente'" />

    <div class="max-w-2xl mx-auto">
      <div class="flex items-center gap-3 mb-6">
        <Link :href="route('customers.index')" class="p-2 rounded-xl hover:bg-gray-100 text-gray-400 transition">
          <ArrowLeftIcon class="w-4 h-4" />
        </Link>
        <div>
          <h2 class="text-xl font-bold text-gray-800">{{ isEdit ? 'Editar Cliente' : 'Novo Cliente' }}</h2>
          <p class="text-sm text-gray-400">{{ isEdit ? `Editando ${customer.name}` : 'Preencha os dados de contato' }}</p>
        </div>
      </div>

      <form @submit.prevent="submit" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-50 space-y-4">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

          <div class="sm:col-span-2">
            <label class="text-xs font-medium text-gray-500 mb-1 block">Nome completo *</label>
            <input v-model="form.name" type="text" placeholder="Ex: João Pereira"
              class="w-full px-3 py-2.5 bg-gray-50 border rounded-xl text-sm outline-none transition"
              :class="form.errors.name ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/20'"
            />
            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="text-xs font-medium text-gray-500 mb-1 block">E-mail</label>
            <input v-model="form.email" type="email" placeholder="joao@email.com"
              class="w-full px-3 py-2.5 bg-gray-50 border rounded-xl text-sm outline-none transition"
              :class="form.errors.email ? 'border-red-400 bg-red-50' : 'border-gray-200 focus:border-violet-400 focus:ring-2 focus:ring-violet-500/20'"
            />
            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
          </div>

          <div>
            <label class="text-xs font-medium text-gray-500 mb-1 block">Telefone</label>
            <input v-model="form.phone" type="text" placeholder="(11) 99999-0000"
              class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 transition"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-gray-500 mb-1 block">CPF / CNPJ</label>
            <input v-model="form.document" type="text" placeholder="000.000.000-00"
              class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 transition"
            />
          </div>

          <div class="sm:col-span-2">
            <label class="text-xs font-medium text-gray-500 mb-1 block">Endereço</label>
            <input v-model="form.address" type="text" placeholder="Rua, número, complemento"
              class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 transition"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-gray-500 mb-1 block">Cidade</label>
            <input v-model="form.city" type="text" placeholder="São Paulo"
              class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 transition"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-gray-500 mb-1 block">Estado (UF)</label>
            <input v-model="form.state" type="text" maxlength="2" placeholder="SP"
              class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-400 transition"
            />
          </div>
        </div>

        <div class="flex gap-3 pt-2">
          <Link :href="route('customers.index')" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
            Cancelar
          </Link>
          <button type="submit" :disabled="form.processing" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white text-sm font-semibold rounded-xl transition shadow-sm">
            {{ form.processing ? 'Salvando...' : isEdit ? 'Salvar Alterações' : 'Cadastrar Cliente' }}
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
  customer: { type: Object, default: null },
});

const isEdit = computed(() => !!props.customer);

const form = useForm({
  name:     props.customer?.name     || '',
  email:    props.customer?.email    || '',
  phone:    props.customer?.phone    || '',
  document: props.customer?.document || '',
  address:  props.customer?.address  || '',
  city:     props.customer?.city     || '',
  state:    props.customer?.state    || '',
});

function submit() {
  if (isEdit.value) {
    form.put(route('customers.update', props.customer.id));
  } else {
    form.post(route('customers.store'));
  }
}
</script>
