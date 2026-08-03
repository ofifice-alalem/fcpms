<template>
  <div class="min-h-screen w-full bg-slate-950 text-slate-100 relative overflow-hidden font-sans flex flex-col antialiased selection:bg-indigo-500 selection:text-white">
    <!-- Spatial Background Blur Circles -->
    <div class="fixed top-[-10%] left-[-10%] w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-purple-600/15 rounded-full blur-[160px] pointer-events-none"></div>
    <div class="fixed top-[40%] right-[20%] w-[400px] h-[400px] bg-cyan-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <!-- Main Spatial Container -->
    <div class="relative z-10 flex-1 flex flex-col w-full max-w-[1600px] mx-auto p-4 md:p-8 space-y-6">
      <slot />
    </div>
  </div>
</template>

<script setup>
/**
 * SpatialWindow.vue - الحاوية الرئيسية الشفافة للواجهات الزجاجية العملاقة
 */
</script>
