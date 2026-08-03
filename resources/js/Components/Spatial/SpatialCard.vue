<template>
  <div
    class="relative overflow-hidden transition-all duration-300 rounded-2xl border border-white/20 dark:border-white/10 bg-white/60 dark:bg-slate-900/60 backdrop-blur-xl shadow-lg hover:shadow-2xl hover:-translate-y-0.5"
    :class="[
      paddingClass,
      glassClass,
      customClass
    ]"
  >
    <!-- Glow Background Accent -->
    <div
      v-if="glow"
      class="absolute -top-24 -right-24 w-48 h-48 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-full blur-3xl pointer-events-none"
    ></div>

    <!-- Card Header -->
    <div v-if="$slots.header || title" class="mb-4 flex items-center justify-between">
      <slot name="header">
        <div>
          <h3 v-if="title" class="text-lg font-bold text-slate-800 dark:text-slate-100">
            {{ title }}
          </h3>
          <p v-if="subtitle" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            {{ subtitle }}
          </p>
        </div>
      </slot>
    </div>

    <!-- Default Content Slot -->
    <div class="relative z-10">
      <slot />
    </div>

    <!-- Card Footer -->
    <div v-if="$slots.footer" class="mt-6 pt-4 border-t border-slate-200/50 dark:border-slate-800/50 flex items-center justify-between">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
/**
 * SpatialCard.vue - بطاقة الزجاج المقسّى (Spatial UI Glassmorphism Card)
 */
import { computed } from 'vue'

const props = defineProps({
  title: { type: String, default: '' },
  subtitle: { type: String, default: '' },
  padding: { type: String, default: 'normal' }, // none, small, normal, large
  glow: { type: Boolean, default: false },
  variant: { type: String, default: 'default' }, // default, interactive, gradient
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
    return 'bg-gradient-to-br from-white/80 via-white/50 to-indigo-50/30 dark:from-slate-900/80 dark:via-slate-900/50 dark:to-indigo-950/30 border-indigo-200/30 dark:border-indigo-800/30'
  }
  return ''
})
</script>
