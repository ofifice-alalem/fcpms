<template>
  <button
    :type="type"
    :disabled="disabled"
    @click="$emit('click', $event)"
    class="spatial-icon-btn !w-9 !h-9 text-sm rounded-full transition-all duration-200 active:scale-95 disabled:opacity-50 disabled:pointer-events-none"
    :class="[variantClasses]"
  >
    <slot />
  </button>
</template>

<script setup>
/**
 * SpatialIconBtn.vue - زر الأيقونة الدائري الفضائي لـ Design System v3.0
 */
import { computed } from 'vue'

const props = defineProps({
  type: { type: String, default: 'button' },
  variant: { type: String, default: 'secondary' }, // primary, secondary, danger, ghost
  disabled: { type: Boolean, default: false }
})

defineEmits(['click'])

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'bg-primary text-white border-primary shadow-md hover:bg-blue-600'
    case 'danger':
      return 'bg-red-500/20 text-red-400 border-red-500/30 hover:bg-red-500/30'
    case 'ghost':
      return 'bg-black/5 dark:bg-white/10 text-slate-700 dark:text-white border-transparent hover:bg-black/10 dark:hover:bg-white/20'
    case 'secondary':
    default:
      return 'bg-black/5 dark:bg-white/10 text-slate-700 dark:text-white border-black/10 dark:border-white/15 hover:bg-black/10 dark:hover:bg-white/20'
  }
})
</script>
