<template>
  <span :class="classes">
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  variant: { type: String, default: 'cinza' }, // cinza, verde, amarelo, vermelho, roxo, azul
});

const classes = computed(() => {
  const base = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
  const variantes = {
    cinza:    'bg-gray-100 text-gray-700',
    verde:    'bg-green-100 text-green-700',
    amarelo:  'bg-yellow-100 text-yellow-700',
    vermelho: 'bg-red-100 text-red-700',
    roxo:     'bg-indigo-100 text-indigo-700',
    azul:     'bg-blue-100 text-blue-700',
    laranja:  'bg-orange-100 text-orange-700',
  };
  return `${base} ${variantes[props.variant] ?? variantes.cinza}`;
});
</script>
