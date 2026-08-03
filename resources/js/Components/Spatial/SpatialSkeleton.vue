<template>
  <div
    class="animate-pulse bg-slate-200/60 dark:bg-slate-800/60 backdrop-blur-sm border border-white/10"
    :class="[
      roundedClass,
      heightClass,
      widthClass
    ]"
  ></div>
</template>

<script setup>
/**
 * SpatialSkeleton.vue - هيكل الانتظار والتحميل (Skeleton Loader)
 */
import { computed } from 'vue'

const props = defineProps({
  type: { type: String, default: 'text' }, // text, avatar, card, button
  height: { type: String, default: 'h-4' },
  width: { type: String, default: 'w-full' },
  rounded: { type: String, default: 'rounded-xl' }
})

const roundedClass = computed(() => {
  if (props.type === 'avatar') return 'rounded-full'
  return props.rounded
})

const heightClass = computed(() => {
  if (props.type === 'avatar') return 'h-12'
  if (props.type === 'card') return 'h-48'
  if (props.type === 'button') return 'h-10'
  return props.height
})

const widthClass = computed(() => {
  if (props.type === 'avatar') return 'w-12'
  return props.width
})
</script>
