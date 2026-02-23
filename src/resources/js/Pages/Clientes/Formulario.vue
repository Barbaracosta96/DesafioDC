<template>
  <AppLayout :titulo="editando ? 'Editar Cliente' : 'Novo Cliente'">
    <Head :title="editando ? 'Editar Cliente' : 'Novo Cliente'" />

    <div class="flex items-center gap-3 mb-6">
      <Botao variant="fantasma" size="sm" :href="route('clientes.index')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Voltar
      </Botao>
      <h2 class="text-2xl font-bold text-gray-900">{{ editando ? 'Editar Cliente' : 'Novo Cliente' }}</h2>
    </div>

    <form @submit.prevent="salvar" class="space-y-6">
      <!-- Dados Pessoais -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Dados Pessoais</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div class="md:col-span-2">
            <InputField label="Nome completo / Razão Social *" v-model="form.nome" :erro="form.errors.nome" placeholder="Nome do cliente" />
          </div>
          <InputField label="E-mail" v-model="form.email" type="email" :erro="form.errors.email" placeholder="email@exemplo.com" />
          <InputField label="Telefone / WhatsApp" v-model="form.telefone" :erro="form.errors.telefone" placeholder="(11) 9 9999-9999" />
          <div>
            <SelectField label="Tipo de Pessoa" v-model="form.tipo" :opcoes="tiposPessoa" :erro="form.errors.tipo" />
          </div>
          <InputField :label="form.tipo === 'pessoa_juridica' ? 'CNPJ' : 'CPF'" v-model="form.cpf_cnpj" :erro="form.errors.cpf_cnpj" :placeholder="form.tipo === 'pessoa_juridica' ? '00.000.000/0001-00' : '000.000.000-00'" />
        </div>
      </div>

      <!-- Endereço -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Endereço</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <InputField label="CEP" v-model="form.cep" :erro="form.errors.cep" placeholder="00000-000" />
          <div class="md:col-span-2">
            <InputField label="Logradouro" v-model="form.logradouro" :erro="form.errors.logradouro" placeholder="Rua, Av..." />
          </div>
          <InputField label="Número" v-model="form.numero" :erro="form.errors.numero" placeholder="123" />
          <InputField label="Complemento" v-model="form.complemento" :erro="form.errors.complemento" placeholder="Ap, Sala..." />
          <InputField label="Bairro" v-model="form.bairro" :erro="form.errors.bairro" placeholder="Bairro" />
          <InputField label="Cidade" v-model="form.cidade" :erro="form.errors.cidade" placeholder="Cidade" />
          <InputField label="Estado (UF)" v-model="form.estado" :erro="form.errors.estado" placeholder="SP" maxlength="2" />
        </div>
      </div>

      <div class="flex justify-end gap-3">
        <Botao variant="secundario" :href="route('clientes.index')">Cancelar</Botao>
        <Botao type="submit" :carregando="form.processing">{{ editando ? 'Salvar Alterações' : 'Cadastrar Cliente' }}</Botao>
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
import SelectField from '@/Components/SelectField.vue';

const props = defineProps({
  cliente: { type: Object, default: null },
});

const editando = computed(() => !!props.cliente?.id);

const form = useForm({
  nome:        props.cliente?.nome        ?? '',
  email:       props.cliente?.email       ?? '',
  telefone:    props.cliente?.telefone    ?? '',
  tipo:        props.cliente?.tipo        ?? 'pessoa_fisica',
  cpf_cnpj:    props.cliente?.cpf_cnpj    ?? '',
  cep:         props.cliente?.cep         ?? '',
  logradouro:  props.cliente?.logradouro  ?? '',
  numero:      props.cliente?.numero      ?? '',
  complemento: props.cliente?.complemento ?? '',
  bairro:      props.cliente?.bairro      ?? '',
  cidade:      props.cliente?.cidade      ?? '',
  estado:      props.cliente?.estado      ?? '',
});

const tiposPessoa = [
  { value: 'pessoa_fisica', label: 'Pessoa Física' },
  { value: 'pessoa_juridica', label: 'Pessoa Jurídica' },
];

const salvar = () => {
  if (editando.value) {
    form.put(route('clientes.update', props.cliente.id));
  } else {
    form.post(route('clientes.store'));
  }
};
</script>
