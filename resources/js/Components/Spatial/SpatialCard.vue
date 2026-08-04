<template>
  <div
    class="spatial-card relative focus-within:z-40 hover:z-20 transition-all duration-300 rounded-[30px] shadow-xl hover:shadow-2xl"
    :class="[
      paddingClass,
      glassClass,
      customClass
    ]"
  >
    <!-- Glow Background Accent (clipped internally so card doesn't clip dropdowns) -->
    <div
      v-if="glow"
      class="absolute inset-0 overflow-hidden rounded-[30px] pointer-events-none"
    >
      <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/20 rounded-full blur-3xl"></div>
    </div>

    <!-- Card Header -->
    <div v-if="$slots.header || title" class="mb-4 flex items-center justify-between">
      <slot name="header">
        <div>
          <h3 v-if="title" class="text-lg font-black text-slate-900 dark:text-white">
            {{ title }}
          </h3>
          <p v-if="subtitle" class="text-xs font-bold text-slate-500 dark:text-white/50 mt-0.5">
            {{ subtitle }}
          </p>
        </div>
      </slot>
    </div>

    <!-- Default Content Slot (z-index auto so focus-within elevates whole card) -->
    <div class="relative">
      <slot />
    </div>

    <!-- Card Footer -->
    <div v-if="$slots.footer" class="mt-6 pt-4 border-t border-black/10 dark:border-white/10 flex items-center justify-between">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
/**
 * SpatialCard.vue - بطاقة الزجاج المقسّى مع رفع الطبقة z-index تلقائياً عند التفاعل (v3.0)
 */
import { computed } from 'vue'

const props = defineProps({
  title: { type: String, default: '' },
  subtitle: { type: String, default: '' },
  padding: { type: String, default: 'normal' }, // none, small, normal, large
  glow: { type: Boolean, default: false },
  variant: { type: String, default: 'default' },
  customClass: { type: String, default: '' }
})

const paddingClass = computed(() => {
  switch (props.padding) {
    case 'none': return 'p-0'
    case 'small': return 'p-4'
    case 'large': return 'p-8'
    case 'normal':
    default: return 'p-6'
  }
})

const glassClass = computed(() => {
  if (props.variant === 'gradient') {
    return 'bg-gradient-to-br from-primary/10 via-primary/5 to-transparent'
  }
  return ''
})
</script>
