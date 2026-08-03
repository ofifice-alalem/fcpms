<template>
  <div class="space-y-1.5 w-full">
    <!-- Header Labels -->
    <div v-if="showLabel || label" class="flex justify-between text-xs font-bold">
      <span class="text-slate-700 dark:text-slate-300">{{ label }}</span>
      <span class="text-indigo-600 dark:text-indigo-400 font-mono">{{ percentage }}%</span>
    </div>

    <!-- Progress Track Bar -->
    <div class="w-full bg-slate-200/60 dark:bg-slate-800/60 h-3 rounded-full overflow-hidden backdrop-blur-md p-0.5 border border-white/20 dark:border-white/5">
      <div
        class="h-full rounded-full transition-all duration-500 ease-out bg-gradient-to-r shadow-sm"
        :class="[gradientClasses]"
        :style="{ width: `${percentage}%` }"
      ></div>
    </div>
  </div>
</template>

<script setup>
/**
 * SpatialProgressBar.vue - شريط التقدم الفضائي المتحرك
 */
import { computed } from 'vue'

const props = defineProps({
  value: { type: Number, default: 0 },
  max: { type: Number, default: 100 },
  label: { type: String, default: '' },
  showLabel: { type: Boolean, default: true },
  variant: { type: String, default: 'default' } // default, success, warning, danger
})

const percentage = computed(() => {
  const p = Math.round((props.value / props.max) * 100)
  return isNaN(p) ? 0 : Math.min(100, Math.max(0, p))
})

const gradientClasses = computed(() => {
  switch (props.variant) {
    case 'success': return 'from-emerald-500 to-teal-400 shadow-emerald-500/30'
    case 'warning': return 'from-amber-500 to-orange-400 shadow-amber-500/30'
    case 'danger': return 'from-rose-500 to-red-400 shadow-rose-500/30'
    case 'default':
    default: return 'from-indigo-600 via-purple-600 to-cyan-500 shadow-indigo-500/30'
  }
})
</script>
