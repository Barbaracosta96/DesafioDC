<template>
  <div v-if="links && links.length > 3" class="flex items-center justify-between mt-6">
    <p class="text-sm text-gray-600">
      Mostrando <span class="font-medium">{{ de }}</span> a <span class="font-medium">{{ ate }}</span>
      de <span class="font-medium">{{ total }}</span> registros
    </p>
    <div class="flex gap-1">
      <template v-for="link in links" :key="link.label">
        <Link
          v-if="link.url"
          :href="link.url"
          preserve-scroll
          :class="[
            'px-3 py-1.5 rounded-lg text-sm font-medium transition-colors',
            link.active
              ? 'bg-indigo-600 text-white'
              : 'text-gray-600 hover:bg-gray-100',
          ]"
          v-html="link.label"
        />
        <span
          v-else
          :class="['px-3 py-1.5 rounded-lg text-sm text-gray-400', link.active ? 'bg-indigo-600 text-white' : '']"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: { type: Array, default: () => [] },
  de:    { type: Number, default: 0 },
  ate:   { type: Number, default: 0 },
  total: { type: Number, default: 0 },
});
</script>
