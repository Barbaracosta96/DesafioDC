<template>
  <Teleport to="body">
    <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="aberto" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
          <!-- Overlay -->
          <div class="fixed inset-0 bg-black/50" @click="$emit('fechar')" />

          <!-- Painel -->
          <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100">
            <div
              :class="['relative bg-white rounded-2xl shadow-xl w-full', tamanhos[tamanho]]"
            >
              <!-- Header -->
              <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">{{ titulo }}</h3>
                <button
                  class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                  @click="$emit('fechar')"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Conteúdo -->
              <div class="px-6 py-5">
                <slot />
              </div>

              <!-- Footer -->
              <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
                <slot name="footer" />
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
defineProps({
  aberto:  { type: Boolean, required: true },
  titulo:  { type: String, default: '' },
  tamanho: { type: String, default: 'md' },
});
defineEmits(['fechar']);

const tamanhos = {
  sm: 'max-w-md',
  md: 'max-w-lg',
  lg: 'max-w-2xl',
  xl: 'max-w-4xl',
};
</script>
