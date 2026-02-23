<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1.5">
      {{ label }}
      <span v-if="obrigatorio" class="text-red-500 ml-0.5">*</span>
    </label>
    <div class="relative">
      <select
        :id="id"
        :value="modelValue"
        :disabled="disabled"
        :class="[
          'w-full px-3.5 py-2.5 pr-10 rounded-xl border text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent appearance-none',
          erro ? 'border-red-400 bg-red-50 text-red-900' : 'border-gray-300 bg-white text-gray-900',
          disabled ? 'opacity-60 cursor-not-allowed' : '',
        ]"
        @change="$emit('update:modelValue', $event.target.value)"
      >
        <option v-if="placeholder" value="">{{ placeholder }}</option>
        <option
          v-for="opcao in opcoes"
          :key="opcao.value ?? opcao.id ?? opcao"
          :value="opcao.value ?? opcao.id ?? opcao"
        >
          {{ opcao.label ?? opcao.nome ?? opcao.name ?? opcao }}
        </option>
      </select>
      <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </div>
    </div>
    <p v-if="erro" class="mt-1.5 text-xs text-red-600">{{ erro }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue:  { type: [String, Number], default: '' },
  id:          { type: String, default: () => `select-${Math.random().toString(36).slice(2)}` },
  label:       { type: String, default: '' },
  placeholder: { type: String, default: 'Selecione...' },
  opcoes:      { type: Array, default: () => [] },
  erro:        { type: String, default: '' },
  disabled:    { type: Boolean, default: false },
  obrigatorio: { type: Boolean, default: false },
});
defineEmits(['update:modelValue']);
</script>
