<template>
  <component
    :is="as"
    :href="href"
    :type="as === 'button' ? type : undefined"
    :disabled="disabled || carregando"
    :class="classes"
  >
    <svg v-if="carregando" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
    </svg>
    <slot />
  </component>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  variant:    { type: String, default: 'primario' }, // primario, secundario, perigo, fantasma
  size:       { type: String, default: 'md' },
  type:       { type: String, default: 'button' },
  href:       { type: String, default: null },
  disabled:   { type: Boolean, default: false },
  carregando: { type: Boolean, default: false },
});

const as = computed(() => props.href ? Link : 'button');

const classes = computed(() => {
  const base = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

  const tamanhos = {
    sm: 'px-3 py-1.5 text-sm gap-1.5',
    md: 'px-4 py-2.5 text-sm gap-2',
    lg: 'px-6 py-3 text-base gap-2',
  };

  const variantes = {
    primario:   'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
    secundario: 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-indigo-500',
    perigo:     'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    fantasma:   'text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:ring-gray-500',
    sucesso:    'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
  };

  return `${base} ${tamanhos[props.size] ?? tamanhos.md} ${variantes[props.variant] ?? variantes.primario}`;
});
</script>
