<template>
  <div class="min-h-screen flex bg-white">
    <!-- Left panel - visual/brand side (visible at md+) -->
    <div class="hidden md:flex md:flex-col md:w-2/5 lg:w-1/2 bg-gradient-to-br from-violet-700 to-purple-900 relative overflow-hidden">
      <!-- Decorative circles -->
      <div class="absolute -top-20 -left-20 w-64 h-64 bg-white/5 rounded-full"></div>
      <div class="absolute top-1/3 -right-10 w-48 h-48 bg-white/5 rounded-full"></div>
      <div class="absolute bottom-20 left-1/4 w-32 h-32 bg-white/10 rounded-full"></div>

      <!-- Grid pattern overlay -->
      <div class="absolute inset-0 opacity-10"
        style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 32px 32px;">
      </div>

      <div class="relative z-10 flex flex-col h-full p-8 lg:p-12">
        <!-- Logo -->
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z" />
            </svg>
          </div>
          <span class="text-2xl font-bold text-white tracking-wide">Dabang</span>
        </div>

        <!-- Hero content -->
        <div class="flex-1 flex flex-col justify-center">
          <h2 class="text-3xl lg:text-4xl font-bold text-white leading-tight mb-4">
            Gerencie seu negócio<br>com inteligência
          </h2>
          <p class="text-violet-200 text-base lg:text-lg mb-8 max-w-sm">
            Controle de estoque, vendas e clientes em um único painel intuitivo e moderno.
          </p>

          <!-- Stats cards -->
          <div class="grid grid-cols-3 gap-3 max-w-md">
            <div class="bg-white/10 backdrop-blur rounded-2xl p-3 lg:p-4 text-center">
              <p class="text-xl lg:text-2xl font-bold text-white">1K+</p>
              <p class="text-violet-200 text-xs mt-1">Total Vendas</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-2xl p-3 lg:p-4 text-center">
              <p class="text-xl lg:text-2xl font-bold text-white">300</p>
              <p class="text-violet-200 text-xs mt-1">Pedidos</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-2xl p-3 lg:p-4 text-center">
              <p class="text-xl lg:text-2xl font-bold text-white">98%</p>
              <p class="text-violet-200 text-xs mt-1">Satisfação</p>
            </div>
          </div>
        </div>

        <!-- Footer note -->
        <p class="text-violet-300 text-xs">
          © 2026 Dabang SaaS · Todos os direitos reservados
        </p>
      </div>
    </div>

    <!-- Right panel - form side -->
    <div class="flex-1 flex flex-col items-center justify-center p-6 bg-white">
      <!-- Mobile header (only on small screens) -->
      <div class="flex md:hidden items-center gap-2 mb-8">
        <div class="w-9 h-9 bg-violet-600 rounded-xl flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z" />
          </svg>
        </div>
        <span class="text-xl font-bold text-gray-800">Dabang</span>
      </div>

      <!-- Mobile top accent bar -->
      <div class="md:hidden fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-violet-600 to-purple-600"></div>

      <div class="w-full max-w-md">
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
</script>
