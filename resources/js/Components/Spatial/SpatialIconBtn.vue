<template>
  <button
    :type="type"
    :disabled="disabled"
    @click="$emit('click', $event)"
    class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-200 active:scale-95 border backdrop-blur-md shadow-sm disabled:opacity-50 disabled:pointer-events-none"
    :class="[variantClasses]"
  >
    <slot />
  </button>
</template>

<script setup>
/**
 * SpatialIconBtn.vue - زر الأيقونة المنفرد الفضائي
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
      return 'bg-indigo-600 text-white border-indigo-500 hover:bg-indigo-500 shadow-indigo-500/30'
    case 'danger':
      return 'bg-rose-500/15 text-rose-600 dark:text-rose-400 border-rose-500/30 hover:bg-rose-500/25'
    case 'ghost':
      return 'bg-transparent text-slate-500 dark:text-slate-400 border-transparent hover:bg-slate-100 dark:hover:bg-slate-800'
    case 'secondary':
    default:
      return 'bg-white/70 dark:bg-slate-800/70 text-slate-700 dark:text-slate-200 border-white/20 dark:border-white/10 hover:bg-white dark:hover:bg-slate-700'
  }
})
</script>
