<template>
  <span
    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold transition-all duration-300 border backdrop-blur-md shadow-sm"
    :class="[pillClasses]"
  >
    <span class="w-2 h-2 rounded-full mr-1.5 animate-pulse" :class="[dotClasses]"></span>
    <slot>{{ label }}</slot>
  </span>
</template>

<script setup>
/**
 * SpatialStatusPill.vue - الشارات الحالية للحالات (Status Badges)
 */
import { computed } from 'vue'

const props = defineProps({
  status: { type: String, default: 'active' }, // active, completed, pending, inactive, danger, warning
  label: { type: String, default: '' }
})

const pillClasses = computed(() => {
  switch (props.status) {
    case 'completed':
    case 'active':
      return 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border-emerald-500/30'
    case 'pending':
    case 'warning':
      return 'bg-amber-500/15 text-amber-700 dark:text-amber-300 border-amber-500/30'
    case 'danger':
    case 'inactive':
      return 'bg-rose-500/15 text-rose-700 dark:text-rose-300 border-rose-500/30'
    case 'info':
    default:
      return 'bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border-indigo-500/30'
  }
})

const dotClasses = computed(() => {
  switch (props.status) {
    case 'completed':
    case 'active': return 'bg-emerald-500'
    case 'pending':
    case 'warning': return 'bg-amber-500'
    case 'danger':
    case 'inactive': return 'bg-rose-500'
    case 'info':
    default: return 'bg-indigo-500'
  }
})
</script>
