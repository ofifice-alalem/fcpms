<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    @click="$emit('click', $event)"
    class="relative inline-flex items-center justify-center font-bold text-sm rounded-xl transition-all duration-300 active:scale-95 shadow-md disabled:opacity-50 disabled:pointer-events-none cursor-pointer overflow-hidden"
    :class="[
      sizeClasses,
      variantClasses,
      block ? 'w-full' : ''
    ]"
  >
    <!-- Loading Spinner Overlay -->
    <span v-if="loading" class="absolute inset-0 flex items-center justify-center bg-black/10 backdrop-blur-sm">
      <svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </span>

    <!-- Icon & Slot Content -->
    <span class="flex items-center space-x-2 space-x-reverse" :class="{ 'opacity-0': loading }">
      <span v-if="icon" class="text-base leading-none">{{ icon }}</span>
      <slot />
    </span>
  </button>
</template>

<script setup>
/**
 * SpatialButton.vue - الأزرار الفضائية الزجاجية الممتازة
 */
import { computed } from 'vue'

const props = defineProps({
  type: { type: String, default: 'button' },
  variant: { type: String, default: 'primary' }, // primary, secondary, destructive, success, outline, ghost
  size: { type: String, default: 'normal' }, // sm, normal, lg
  loading: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  block: { type: Boolean, default: false },
  icon: { type: String, default: '' }
})

defineEmits(['click'])

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm': return 'px-3 py-1.5 text-xs'
    case 'lg': return 'px-7 py-3.5 text-base'
    case 'normal':
    default: return 'px-5 py-2.5 text-sm'
  }
})

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'secondary':
      return 'bg-white/80 dark:bg-slate-800/80 text-slate-800 dark:text-slate-100 hover:bg-white dark:hover:bg-slate-700 border border-slate-200/50 dark:border-slate-700/50'
    case 'destructive':
      return 'bg-gradient-to-r from-rose-600 to-red-600 text-white hover:from-rose-500 hover:to-red-500 shadow-rose-500/25'
    case 'success':
      return 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white hover:from-emerald-500 hover:to-teal-500 shadow-emerald-500/25'
    case 'outline':
      return 'bg-transparent border-2 border-indigo-500 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500/10'
    case 'ghost':
      return 'bg-transparent text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50 shadow-none'
    case 'primary':
    default:
      return 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white hover:from-indigo-500 hover:to-purple-500 shadow-indigo-500/30'
  }
})
</script>
