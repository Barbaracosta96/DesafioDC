<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1.5">
      {{ label }}
      <span v-if="obrigatorio" class="text-red-500 ml-0.5">*</span>
    </label>
    <input
      :id="id"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :required="obrigatorio"
      :class="[
        'w-full px-3.5 py-2.5 rounded-xl border text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
        erro ? 'border-red-400 bg-red-50 text-red-900' : 'border-gray-300 bg-white text-gray-900',
        disabled ? 'opacity-60 cursor-not-allowed bg-gray-50' : '',
      ]"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <p v-if="erro" class="mt-1.5 text-xs text-red-600">{{ erro }}</p>
    <p v-else-if="dica" class="mt-1.5 text-xs text-gray-500">{{ dica }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue:  { type: [String, Number], default: '' },
  id:          { type: String, default: () => `input-${Math.random().toString(36).slice(2)}` },
  label:       { type: String, default: '' },
  type:        { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  erro:        { type: String, default: '' },
  dica:        { type: String, default: '' },
  disabled:    { type: Boolean, default: false },
  obrigatorio: { type: Boolean, default: false },
});
defineEmits(['update:modelValue']);
</script>
