<template>
  <div class="flex flex-col items-center justify-center space-y-3 p-6">
    <div class="relative w-12 h-12">
      <div class="absolute inset-0 rounded-full border-4 border-indigo-500/20"></div>
      <div class="absolute inset-0 rounded-full border-4 border-indigo-500 border-t-transparent animate-spin"></div>
    </div>
    <span v-if="label" class="text-xs font-bold text-slate-600 dark:text-slate-400 animate-pulse">
      {{ label }}
    </span>
  </div>
</template>

<script setup>
/**
 * SpatialLoader.vue - دوّار التحميل الدائري الفضائي
 */
defineProps({
  label: { type: String, default: 'جاري التحميل...' }
})
</script>
