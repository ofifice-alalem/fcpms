<template>
  <div class="relative w-full max-w-2xl overflow-hidden rounded-3xl border border-white/20 dark:border-white/10 bg-white/80 dark:bg-slate-900/85 backdrop-blur-2xl shadow-2xl transition-all duration-300 p-6 md:p-8">
    <!-- Top Decorative Gradient Header -->
    <div class="absolute top-0 right-0 left-0 h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-cyan-500"></div>

    <!-- Header Section -->
    <div v-if="title || $slots.header" class="flex items-center justify-between pb-4 border-b border-slate-200/50 dark:border-slate-800/50 mb-6">
      <slot name="header">
        <div>
          <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">{{ title }}</h2>
          <p v-if="subtitle" class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ subtitle }}</p>
        </div>
      </slot>
      <button
        v-if="closeable"
        @click="$emit('close')"
        class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 transition-colors"
      >
        ✕
      </button>
    </div>

    <!-- Content Section -->
    <div class="space-y-4">
      <slot />
    </div>

    <!-- Footer Section -->
    <div v-if="$slots.footer" class="mt-8 pt-4 border-t border-slate-200/50 dark:border-slate-800/50 flex items-center justify-end space-x-3 space-x-reverse">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
/**
 * SpatialModalCard.vue - بطاقة النافذة المنبثقة
 */
defineProps({
  title: { type: String, default: '' },
  subtitle: { type: String, default: '' },
  closeable: { type: Boolean, default: true }
})

defineEmits(['close'])
</script>
