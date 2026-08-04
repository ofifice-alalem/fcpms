<template>
  <span
    class="status-pill inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all shadow-sm"
    :class="[pillClasses]"
  >
    <span class="w-2 h-2 rounded-full animate-pulse shrink-0" :class="[dotClasses]"></span>
    <span><slot>{{ label }}</slot></span>
  </span>
</template>

<script setup>
/**
 * SpatialStatusPill.vue - شارات الحالة المباشرة لـ Design System v3.0
 */
import { computed } from 'vue'

const props = defineProps({
  status: { type: String, default: 'active' }, // active, completed, pending, vacation, inactive, danger, warning
  label: { type: String, default: '' }
})

const pillClasses = computed(() => {
  switch (props.status) {
    case 'completed':
    case 'active':
      return 'completed'
    case 'pending':
    case 'vacation':
    case 'warning':
      return 'pending'
    case 'danger':
    case 'overdue':
    case 'inactive':
      return 'overdue'
    case 'info':
    default:
      return 'bg-primary/20 text-primary border border-primary/30'
  }
})

const dotClasses = computed(() => {
  switch (props.status) {
    case 'completed':
    case 'active': return 'bg-emerald-500'
    case 'pending':
    case 'vacation':
    case 'warning': return 'bg-amber-500'
    case 'danger':
    case 'overdue':
    case 'inactive': return 'bg-rose-500'
    case 'info':
    default: return 'bg-primary'
  }
})
</script>
