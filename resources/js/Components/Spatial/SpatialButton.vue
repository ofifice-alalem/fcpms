<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    @click="$emit('click', $event)"
    class="relative inline-flex items-center justify-center font-bold rounded-[18px] transition-all duration-200 active:scale-95 shadow-md disabled:opacity-50 disabled:pointer-events-none cursor-pointer overflow-hidden"
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
    <span class="flex items-center gap-2" :class="{ 'opacity-0': loading }">
      <span v-if="icon" class="text-base leading-none">{{ icon }}</span>
      <slot />
    </span>
  </button>
</template>

<script setup>
/**
 * SpatialButton.vue - زر الفضاء التفاعلي المطابق لـ Design System v3.0
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
    case 'sm': return 'px-3.5 py-1.5 text-xs rounded-[14px]'
    case 'lg': return 'px-7 py-3.5 text-base rounded-[22px]'
    case 'normal':
    default: return 'px-5 py-2.5 text-sm rounded-[18px]'
  }
})

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'secondary':
      return 'bg-black/5 dark:bg-white/10 text-slate-800 dark:text-white hover:bg-black/10 dark:hover:bg-white/15 border border-black/10 dark:border-white/15'
    case 'destructive':
      return 'bg-red-500/20 hover:bg-red-500/30 text-red-400 border border-red-500/30 text-xs font-black shadow-md'
    case 'success':
      return 'bg-emerald-500 hover:bg-emerald-600 text-white font-black shadow-md'
    case 'outline':
      return 'bg-transparent border border-primary text-primary hover:bg-primary/10'
    case 'ghost':
      return 'bg-transparent text-slate-700 dark:text-white/80 hover:bg-black/5 dark:hover:bg-white/10 shadow-none'
    case 'primary':
    default:
      return 'bg-primary text-white font-black shadow-md shadow-primary/30 hover:bg-blue-600'
  }
})
</script>
