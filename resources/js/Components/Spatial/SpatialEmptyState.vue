<template>
  <div class="flex flex-col items-center justify-center p-8 text-center space-y-3">
    <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-indigo-500/20 to-purple-500/20 flex items-center justify-center text-3xl text-indigo-500 border border-indigo-500/30 shadow-lg">
      {{ icon }}
    </div>
    <h4 class="text-base font-bold text-slate-800 dark:text-slate-100">{{ title }}</h4>
    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm">{{ message }}</p>
    <div v-if="$slots.action" class="pt-2">
      <slot name="action" />
    </div>
  </div>
</template>

<script setup>
/**
 * SpatialEmptyState.vue - حالة خلو البيانات
 */
defineProps({
  icon: { type: String, default: '📭' },
  title: { type: String, default: 'لا توجد بيانات' },
  message: { type: String, default: 'لم نتمكن من العثور على أي معلومات متاحة حالياً.' }
})
</script>
